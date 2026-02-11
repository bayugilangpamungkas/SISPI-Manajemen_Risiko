<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Peta;
use App\Models\CommentPr;
use App\Models\HasilAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ResetManajemenRisiko extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'risiko:reset
        {--force : Force reset tanpa konfirmasi}
        {--tahun= : Reset hanya untuk tahun tertentu}
        {--unit= : Reset hanya untuk unit kerja tertentu}
        {--all-roles : Reset data untuk SEMUA role (admin, auditor, auditee)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset status workflow manajemen risiko ke kondisi awal untuk semua role';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('╔══════════════════════════════════════════════════════════════════╗');
        $this->info('║     RESET STATUS MANAJEMEN RISIKO UNTUK SEMUA ROLE              ║');
        $this->info('╚══════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Get options
        $force = $this->option('force');
        $tahun = $this->option('tahun') ?? date('Y');
        $unitKerja = $this->option('unit');
        $allRoles = $this->option('all-roles');

        // Build query
        $query = Peta::query();
        
        if (!$allRoles) {
            $query->whereYear('created_at', $tahun);
        }

        if ($unitKerja) {
            $query->where('jenis', $unitKerja);
        }

        $totalRisiko = $query->count();

        if ($totalRisiko == 0) {
            $this->warn('⚠️  Tidak ada data risiko yang ditemukan!');
            return Command::SUCCESS;
        }

        // Show info
        $this->info("📊 DATA YANG AKAN DIRESET:");
        $this->line("   • Tahun: " . ($allRoles ? "SEMUA TAHUN" : $tahun));
        if ($unitKerja) {
            $this->line("   • Unit Kerja: {$unitKerja}");
        }
        $this->line("   • Total Risiko: {$totalRisiko}");
        $this->line("   • Scope: " . ($allRoles ? "SEMUA ROLE (Admin, Auditor, Auditee)" : "Tahun {$tahun} saja"));
        $this->newLine();

        // Konfirmasi
        if (!$force) {
            $this->warn('⚠️  ⚠️  ⚠️  PERINGATAN TINGGI! ⚠️  ⚠️  ⚠️');
            $this->warn('   Ini akan mereset SEMUA status workflow ke kondisi awal!');
            $this->warn('   Data yang akan direset:');
            $this->warn('   - Status risiko ke DRAFT');
            $this->warn('   - Auditor & auditee assignments akan dihapus');
            $this->warn('   - Hasil audit akan dihapus');
            $this->warn('   - Komentar akan dihapus');
            $this->warn('   - Data akan hilang dari SEMUA role');
            $this->newLine();

            if (!$this->confirm('Apakah Anda YAKIN 100% ingin melanjutkan?', false)) {
                $this->info('❌ Reset dibatalkan.');
                return Command::SUCCESS;
            }
        }

        $this->info('🔄 Memulai proses reset...');
        $this->newLine();

        DB::beginTransaction();

        try {
            $steps = $allRoles ? 6 : 5;
            $progressBar = $this->output->createProgressBar($steps);
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% %message%');

            // ============================================
            // STEP 1: Reset status di tabel petas
            // ============================================
            $progressBar->setMessage('Reset status risiko di tabel utama...');
            $progressBar->advance();

            // Kolom-kolom yang akan direset (dengan pengecekan)
            $resetColumns = [
                'koreksiPr' => null,
                'koreksiPr_at' => null,
                'approvalPr' => null,
                'approvalPr_at' => null,
                'status_telaah' => 0,
                'waktu_telaah_spi' => null,
                'auditor_id' => null,
                'tampil_manajemen_risiko' => 0,
                'template_data' => null,
                'template_sent_at' => null,
                // Kolom-kolom opsional (akan dicek dulu)
                'auditee_response' => null,
                'status_konfirmasi_auditee' => null,
                'status_konfirmasi_auditor' => null,
                'auditee_feedback' => null,
                'auditor_feedback' => null,
                'is_active' => 1,
                'is_visible' => 1,
            ];

            // Filter hanya kolom yang ada di tabel
            $tableColumns = Schema::getColumnListing('petas');
            $filteredResetData = [];
            
            foreach ($resetColumns as $column => $value) {
                if (in_array($column, $tableColumns)) {
                    $filteredResetData[$column] = $value;
                }
            }

            $resetCount = $query->update($filteredResetData);

            // ============================================
            // STEP 2: Hapus hasil audit
            // ============================================
            $progressBar->setMessage('Menghapus hasil audit...');
            $progressBar->advance();

            $petaIds = $query->pluck('id')->toArray();
            
            if (!empty($petaIds)) {
                $deletedHasilAudit = HasilAudit::whereIn('peta_id', $petaIds);
                if (!$allRoles) {
                    $deletedHasilAudit->where('tahun_anggaran', $tahun);
                }
                $deletedHasilAudit = $deletedHasilAudit->delete();
            } else {
                $deletedHasilAudit = 0;
            }

            // ============================================
            // STEP 3: Hapus komentar
            // ============================================
            $progressBar->setMessage('Menghapus komentar...');
            $progressBar->advance();

            if (!empty($petaIds)) {
                $deletedComments = CommentPr::whereIn('peta_id', $petaIds)->delete();
            } else {
                $deletedComments = 0;
            }

            // ============================================
            // STEP 4: Hapus assignments auditee & auditor
            // ============================================
            $progressBar->setMessage('Menghapus assignments...');
            $progressBar->advance();

            $deletedAssignments = 0;
            
            // Cek dan hapus dari tabel assignments jika ada
            $assignmentTables = ['auditee_assignments', 'auditor_assignments', 'user_assignments', 'risk_assignments'];
            
            foreach ($assignmentTables as $table) {
                if (Schema::hasTable($table)) {
                    if (!empty($petaIds)) {
                        $deleted = DB::table($table)->whereIn('peta_id', $petaIds)->delete();
                        $deletedAssignments += $deleted;
                    } else {
                        // Jika all-roles, truncate semua
                        if ($allRoles) {
                            DB::table($table)->truncate();
                        }
                    }
                }
            }

            // ============================================
            // STEP 5: Reset data di tabel lain yang terkait
            // ============================================
            $progressBar->setMessage('Reset tabel terkait...');
            $progressBar->advance();

            $relatedTables = [
                'risk_mitigations' => 'peta_id',
                'risk_monitorings' => 'peta_id',
                'risk_treatments' => 'peta_id',
                'risk_evaluations' => 'peta_id',
                'audit_findings' => 'peta_id',
                'corrective_actions' => 'peta_id',
            ];

            $deletedRelated = 0;
            foreach ($relatedTables as $table => $foreignKey) {
                if (Schema::hasTable($table)) {
                    if (!empty($petaIds)) {
                        $deleted = DB::table($table)->whereIn($foreignKey, $petaIds)->delete();
                        $deletedRelated += $deleted;
                    } elseif ($allRoles) {
                        DB::table($table)->truncate();
                    }
                }
            }

            // ============================================
            // STEP 6 (Optional jika all-roles): Reset semua tahun
            // ============================================
            if ($allRoles) {
                $progressBar->setMessage('Finalisasi reset semua data...');
                $progressBar->advance();
                
                // Reset flag untuk SEMUA data
                DB::table('petas')->update(['tampil_manajemen_risiko' => 0]);
                
                // Hapus SEMUA data dari tabel dependen
                $tablesToTruncate = [
                    'hasil_audits',
                    'comment_prs',
                ];
                
                foreach ($tablesToTruncate as $table) {
                    if (Schema::hasTable($table)) {
                        DB::table($table)->truncate();
                    }
                }
            }

            $progressBar->setMessage('Selesai!');
            $progressBar->finish();
            $this->newLine(2);

            DB::commit();

            // ============================================
            // CLEAR CACHE
            // ============================================
            $this->call('cache:clear');
            $this->call('config:clear');
            $this->call('view:clear');

            // ============================================
            // SUMMARY REPORT
            // ============================================
            $this->info('✅ RESET BERHASIL!');
            $this->newLine();
            
            $summaryData = [
                ['Risiko direset', $resetCount, 'Status kembali ke DRAFT'],
                ['Hasil audit dihapus', $deletedHasilAudit, 'Data hasil audit'],
                ['Komentar dihapus', $deletedComments, 'Komentar PR'],
            ];

            if ($deletedAssignments > 0) {
                $summaryData[] = ['Assignments dihapus', $deletedAssignments, 'Assign auditee/auditor'];
            }

            if ($deletedRelated > 0) {
                $summaryData[] = ['Data terkait dihapus', $deletedRelated, 'Mitigasi, monitoring, dll'];
            }

            $this->table(
                ['Item', 'Jumlah', 'Keterangan'],
                $summaryData
            );

            $this->newLine();
            $this->info('📋 STATUS AKHIR:');
            $this->line("   • Total data risiko: " . Peta::count());
            $this->line("   • Tampil di manajemen risiko: " . Peta::where('tampil_manajemen_risiko', 1)->count());
            $this->line("   • Hasil audit tersisa: " . HasilAudit::count());
            $this->line("   • Komentar tersisa: " . CommentPr::count());
            
            $this->newLine();
            $this->info('🎯 DATA SEKARANG TIDAK AKAN MUNCUL DI:');
            $this->line("   • Halaman Admin ✓");
            $this->line("   • Halaman Auditor ✓");
            $this->line("   • Halaman Auditee ✓");
            
            $this->newLine();
            $this->info('💡 TIPS:');
            $this->line("   • Gunakan 'php artisan risiko:generate-missing' untuk generate ulang");
            $this->line("   • Refresh browser (Ctrl+F5) untuk melihat perubahan");
            $this->line("   • Logout dan login ulang jika perlu");

            // Log activity
            Log::info('Reset Manajemen Risiko BERHASIL', [
                'tahun' => $allRoles ? 'ALL' : $tahun,
                'unit_kerja' => $unitKerja ?? 'all',
                'all_roles' => $allRoles,
                'total_risiko' => $resetCount,
                'hasil_audit_deleted' => $deletedHasilAudit,
                'comments_deleted' => $deletedComments,
                'assignments_deleted' => $deletedAssignments,
                'related_deleted' => $deletedRelated,
                'user' => auth()->user()->name ?? 'System',
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();

            $this->newLine(2);
            $this->error('❌ RESET GAGAL!');
            $this->error('Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile());
            $this->error('Line: ' . $e->getLine());

            // Debug info
            $this->newLine();
            $this->warn('🛠️  INFO DEBUG:');
            $this->line("   • Total risiko ditemukan: " . ($totalRisiko ?? 0));
            $this->line("   • Peta IDs: " . (empty($petaIds) ? 'Kosong' : count($petaIds)));
            $this->line("   • Tahun: " . $tahun);
            $this->line("   • All Roles: " . ($allRoles ? 'Ya' : 'Tidak'));

            Log::error('Reset Manajemen Risiko GAGAL', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tahun' => $tahun,
                'all_roles' => $allRoles,
                'total_risiko' => $totalRisiko ?? 0,
            ]);

            return Command::FAILURE;
        }
    }
}
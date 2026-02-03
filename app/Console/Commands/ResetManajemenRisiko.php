<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Peta;

class ResetManajemenRisiko extends Command
{
    protected $signature = 'risiko:reset {--tahun=2026 : Tahun yang akan direset}';

    protected $description = 'Reset semua data manajemen risiko ke kondisi awal (kosong)';

    public function handle()
    {
        $tahun = $this->option('tahun');

        $this->info("=== RESET DATA MANAJEMEN RISIKO TAHUN {$tahun} ===\n");

        if (!$this->confirm('Apakah Anda yakin ingin mereset semua data? Proses ini tidak dapat dibatalkan!')) {
            $this->info('Reset dibatalkan.');
            return Command::SUCCESS;
        }

        $this->info('Memulai proses reset...');

        // 1. Reset flag tampil_manajemen_risiko di tabel petas
        $this->info('1. Me-reset flag tampil_manajemen_risiko di tabel petas...');
        $updatedPetas = Peta::whereYear('created_at', $tahun)
            ->update(['tampil_manajemen_risiko' => 0]);
        $this->info("   ✓ Berhasil mereset {$updatedPetas} risiko");

        // 2. Hitung total risiko per unit kerja
        $this->info('2. Menghitung statistik per unit kerja...');
        $units = DB::table('petas')
            ->select('jenis', DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', $tahun)
            ->groupBy('jenis')
            ->get();

        $this->info("   ✓ Ditemukan " . count($units) . " unit kerja");

        // Tampilkan ringkasan
        $this->newLine();
        $this->info('=== RESET SELESAI ===');
        $this->table(
            ['Aksi', 'Jumlah'],
            [
                ['Risiko direset (tampil_manajemen_risiko = 0)', $updatedPetas],
                ['Unit kerja diproses', count($units)],
            ]
        );

        $this->newLine();
        $this->info('✅ Semua data manajemen risiko tahun ' . $tahun . ' berhasil direset!');
        $this->info('💡 Kolom "Kegiatan" dan "Risiko yang Dipilih" sekarang kembali kosong (0/X).');

        return Command::SUCCESS;
    }
}

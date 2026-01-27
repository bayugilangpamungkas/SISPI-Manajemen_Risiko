<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kegiatan;
use App\Models\Peta;
use App\Models\UnitKerja;
use Carbon\Carbon;

class GenerateMissingRisiko extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'risiko:generate-missing {--tahun=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate risiko otomatis untuk kegiatan yang belum memiliki risiko';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Memulai proses penambahan risiko untuk kegiatan...');

        $tahun = $this->option('tahun') ?? date('Y');
        $this->info("📅 Tahun target: {$tahun}");

        // Data template risiko dengan variasi
        $templateRisiko = $this->getRisikoTemplates();

        // Get semua kegiatan
        $allKegiatans = Kegiatan::with('unitKerja')->get();
        $this->info("📊 Total kegiatan ditemukan: " . $allKegiatans->count());

        $totalAdded = 0;
        $kegiatanTanpaRisiko = 0;

        $progressBar = $this->output->createProgressBar($allKegiatans->count());
        $progressBar->start();

        foreach ($allKegiatans as $kegiatan) {
            // Cek apakah kegiatan ini punya risiko di tahun target
            $hasRisiko = Peta::where('id_kegiatan', $kegiatan->id)
                ->whereYear('created_at', $tahun)
                ->exists();

            if (!$hasRisiko) {
                $kegiatanTanpaRisiko++;

                // Dapatkan jenis unit kerja
                $jenisUnit = $kegiatan->unitKerja ? $kegiatan->unitKerja->nama_unit_kerja : 'default';

                // Pilih template risiko yang sesuai
                $templates = $templateRisiko[$jenisUnit] ?? $templateRisiko['default'];

                // Generate 2-4 risiko untuk kegiatan ini
                $jumlahRisiko = rand(2, 4);
                
                // Shuffle untuk variasi
                shuffle($templates);
                $selectedTemplates = array_slice($templates, 0, $jumlahRisiko);

                foreach ($selectedTemplates as $template) {
                    $tingkatRisiko = $this->getTingkatRisiko($template['kemungkinan'], $template['dampak']);

                    Peta::create([
                        'id_kegiatan' => $kegiatan->id,
                        'judul' => $template['judul'],
                        'jenis' => $jenisUnit,
                        'anggaran' => $kegiatan->anggaran ?? '0',
                        'kode_regist' => 'AUTO-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                        'kategori' => $template['kategori'],
                        'uraian' => $template['uraian'],
                        'skor_kemungkinan' => $template['kemungkinan'],
                        'skor_dampak' => $template['dampak'],
                        'tingkat_risiko' => $tingkatRisiko,
                        'tampil_manajemen_risiko' => 0,
                        'status_telaah' => 0,
                        'metode' => 'Risk Assessment',
                        'pernyataan' => $template['pernyataan'] ?? 'Risiko ini telah diidentifikasi dan perlu dilakukan mitigasi',
                        'pengendalian' => $template['pengendalian'] ?? 'Monitoring berkala dan evaluasi risiko',
                        'mitigasi' => $template['mitigasi'] ?? 'Penyusunan rencana mitigasi risiko dan tindakan preventif',
                        'waktu' => Carbon::create($tahun, rand(1, 12), rand(1, 28))->format('Y-m-d'),
                        'created_at' => Carbon::create($tahun, rand(1, 12), rand(1, 28)),
                        'updated_at' => Carbon::now(),
                    ]);

                    $totalAdded++;
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("📈 RINGKASAN:");
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Total Kegiatan', $allKegiatans->count()],
                ['Kegiatan Tanpa Risiko', $kegiatanTanpaRisiko],
                ['Total Risiko Ditambahkan', $totalAdded],
            ]
        );

        $this->newLine();
        $this->info("✨ Proses selesai!");

        return Command::SUCCESS;
    }

    /**
     * Get template risiko berdasarkan jenis unit kerja
     */
    private function getRisikoTemplates()
    {
        return [
            'UPA TIK' => [
                [
                    'judul' => 'Upgrade Infrastruktur Jaringan Terhambat',
                    'kategori' => 'Operasional',
                    'uraian' => 'Risiko keterlambatan atau hambatan dalam proses upgrade infrastruktur jaringan yang dapat mempengaruhi kinerja sistem TI',
                    'kemungkinan' => 3,
                    'dampak' => 4,
                    'pernyataan' => 'Infrastruktur jaringan yang tidak optimal dapat menghambat operasional institusi',
                    'pengendalian' => 'Penjadwalan maintenance berkala dan monitoring kapasitas jaringan',
                    'mitigasi' => 'Menyediakan backup system dan rencana kontingensi',
                ],
                [
                    'judul' => 'Gangguan Sistem Server',
                    'kategori' => 'Teknologi',
                    'uraian' => 'Risiko gangguan atau downtime pada sistem server yang dapat mengganggu layanan',
                    'kemungkinan' => 2,
                    'dampak' => 4,
                    'pernyataan' => 'Server yang down dapat menghentikan layanan sistem informasi',
                    'pengendalian' => 'Implementasi high availability dan redundancy system',
                    'mitigasi' => 'Disaster recovery plan dan backup data otomatis',
                ],
                [
                    'judul' => 'Keamanan Data dan Informasi',
                    'kategori' => 'Keamanan',
                    'uraian' => 'Risiko kebocoran atau kerusakan data penting akibat serangan cyber atau human error',
                    'kemungkinan' => 2,
                    'dampak' => 5,
                    'pernyataan' => 'Data sensitif perlu dilindungi dari akses tidak sah',
                    'pengendalian' => 'Implementasi security protocols dan access control',
                    'mitigasi' => 'Enkripsi data, firewall, dan security training untuk staff',
                ],
                [
                    'judul' => 'Kegagalan Sistem Backup Data',
                    'kategori' => 'Teknologi',
                    'uraian' => 'Risiko kehilangan data karena sistem backup tidak berjalan optimal',
                    'kemungkinan' => 2,
                    'dampak' => 4,
                    'pernyataan' => 'Backup data penting untuk recovery sistem',
                    'pengendalian' => 'Automated backup schedule dan verifikasi backup integrity',
                    'mitigasi' => 'Multiple backup locations dan regular testing restore procedures',
                ],
            ],
            'default' => [
                [
                    'judul' => 'Keterlambatan Pelaksanaan Kegiatan',
                    'kategori' => 'Operasional',
                    'uraian' => 'Risiko tidak tercapainya target waktu pelaksanaan kegiatan yang telah direncanakan',
                    'kemungkinan' => 3,
                    'dampak' => 3,
                    'pernyataan' => 'Timeline yang tidak terpenuhi dapat mempengaruhi pencapaian target kinerja',
                    'pengendalian' => 'Project management dengan milestone tracking',
                    'mitigasi' => 'Buffer time allocation dan prioritisasi kegiatan kritis',
                ],
                [
                    'judul' => 'Anggaran Tidak Mencukupi',
                    'kategori' => 'Keuangan',
                    'uraian' => 'Risiko kekurangan dana untuk menyelesaikan kegiatan sesuai rencana',
                    'kemungkinan' => 2,
                    'dampak' => 4,
                    'pernyataan' => 'Keterbatasan anggaran dapat menghambat pencapaian output kegiatan',
                    'pengendalian' => 'Budget monitoring dan cost control yang ketat',
                    'mitigasi' => 'Realokasi anggaran dan pencarian sumber pendanaan alternatif',
                ],
                [
                    'judul' => 'Kurangnya SDM Kompeten',
                    'kategori' => 'Sumber Daya Manusia',
                    'uraian' => 'Risiko keterbatasan sumber daya manusia yang memiliki kompetensi sesuai kebutuhan kegiatan',
                    'kemungkinan' => 2,
                    'dampak' => 3,
                    'pernyataan' => 'SDM yang tidak kompeten dapat mempengaruhi kualitas output',
                    'pengendalian' => 'Skills assessment dan training needs analysis',
                    'mitigasi' => 'Program pelatihan, coaching, dan rekrutmen SDM qualified',
                ],
                [
                    'judul' => 'Koordinasi Antar Unit Tidak Optimal',
                    'kategori' => 'Operasional',
                    'uraian' => 'Risiko hambatan komunikasi dan koordinasi antar unit kerja terkait',
                    'kemungkinan' => 3,
                    'dampak' => 2,
                    'pernyataan' => 'Koordinasi yang buruk dapat menyebabkan duplikasi atau missed activities',
                    'pengendalian' => 'Regular coordination meetings dan clear communication channels',
                    'mitigasi' => 'Standard operating procedures dan liaison officers',
                ],
                [
                    'judul' => 'Perubahan Regulasi atau Kebijakan',
                    'kategori' => 'Legal & Compliance',
                    'uraian' => 'Risiko perubahan aturan atau kebijakan yang mempengaruhi pelaksanaan kegiatan',
                    'kemungkinan' => 2,
                    'dampak' => 3,
                    'pernyataan' => 'Perubahan regulasi dapat memerlukan adjustment dalam pelaksanaan',
                    'pengendalian' => 'Regular regulatory monitoring dan legal consultation',
                    'mitigasi' => 'Flexible planning dan compliance update procedures',
                ],
                [
                    'judul' => 'Kualitas Output Tidak Sesuai Standar',
                    'kategori' => 'Kualitas',
                    'uraian' => 'Risiko hasil kegiatan tidak memenuhi standar mutu yang ditetapkan',
                    'kemungkinan' => 2,
                    'dampak' => 4,
                    'pernyataan' => 'Output berkualitas rendah dapat mengurangi kepuasan stakeholder',
                    'pengendalian' => 'Quality assurance process dan regular review',
                    'mitigasi' => 'Quality control checkpoints dan continuous improvement',
                ],
            ],
        ];
    }

    /**
     * Hitung tingkat risiko berdasarkan skor kemungkinan dan dampak
     */
    private function getTingkatRisiko($kemungkinan, $dampak)
    {
        $skor = $kemungkinan * $dampak;

        if ($skor >= 15) {
            return 'Extreme';
        } elseif ($skor >= 10) {
            return 'High';
        } elseif ($skor >= 5) {
            return 'Moderate';
        } else {
            return 'Low';
        }
    }
}

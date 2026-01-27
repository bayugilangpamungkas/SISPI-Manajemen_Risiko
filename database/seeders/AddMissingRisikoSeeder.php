<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kegiatan;
use App\Models\Peta;
use App\Models\UnitKerja;
use Carbon\Carbon;

class AddMissingRisikoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder ini akan menambahkan risiko otomatis untuk setiap kegiatan yang belum memiliki risiko
     */
    public function run()
    {
        $this->command->info('🚀 Memulai proses penambahan risiko untuk kegiatan...');

        // Data template risiko berdasarkan jenis unit kerja
        $templateRisiko = [
            'UPA TIK' => [
                [
                    'judul' => 'Upgrade Infrastruktur Jaringan Terhambat',
                    'kategori' => 'Operasional',
                    'uraian' => 'Risiko keterlambatan atau hambatan dalam proses upgrade infrastruktur jaringan yang dapat mempengaruhi kinerja sistem TI',
                    'kemungkinan' => 3,
                    'dampak' => 4,
                ],
                [
                    'judul' => 'Gangguan Sistem Server',
                    'kategori' => 'Teknologi',
                    'uraian' => 'Risiko gangguan atau downtime pada sistem server yang dapat mengganggu layanan',
                    'kemungkinan' => 2,
                    'dampak' => 4,
                ],
                [
                    'judul' => 'Keamanan Data dan Informasi',
                    'kategori' => 'Keamanan',
                    'uraian' => 'Risiko kebocoran atau kerusakan data penting akibat serangan cyber atau human error',
                    'kemungkinan' => 2,
                    'dampak' => 5,
                ],
            ],
            'default' => [
                [
                    'judul' => 'Keterlambatan Pelaksanaan Kegiatan',
                    'kategori' => 'Operasional',
                    'uraian' => 'Risiko tidak tercapainya target waktu pelaksanaan kegiatan yang telah direncanakan',
                    'kemungkinan' => 3,
                    'dampak' => 3,
                ],
                [
                    'judul' => 'Anggaran Tidak Mencukupi',
                    'kategori' => 'Keuangan',
                    'uraian' => 'Risiko kekurangan dana untuk menyelesaikan kegiatan sesuai rencana',
                    'kemungkinan' => 2,
                    'dampak' => 4,
                ],
                [
                    'judul' => 'Kurangnya SDM Kompeten',
                    'kategori' => 'Sumber Daya Manusia',
                    'uraian' => 'Risiko keterbatasan sumber daya manusia yang memiliki kompetensi sesuai kebutuhan kegiatan',
                    'kemungkinan' => 2,
                    'dampak' => 3,
                ],
            ],
        ];

        $tahunSekarang = date('Y');
        $tahunLalu = $tahunSekarang - 1;

        // Get semua kegiatan
        $allKegiatans = Kegiatan::with('unitKerja')->get();
        $this->command->info("📊 Total kegiatan ditemukan: " . $allKegiatans->count());

        $totalAdded = 0;
        $kegiatanTanpaRisiko = 0;

        foreach ($allKegiatans as $kegiatan) {
            // Cek apakah kegiatan ini punya risiko di tahun ini atau tahun lalu
            $hasRisikoTahunIni = Peta::where('id_kegiatan', $kegiatan->id)
                ->whereYear('created_at', $tahunSekarang)
                ->exists();

            $hasRisikoTahunLalu = Peta::where('id_kegiatan', $kegiatan->id)
                ->whereYear('created_at', $tahunLalu)
                ->exists();

            if (!$hasRisikoTahunIni && !$hasRisikoTahunLalu) {
                $kegiatanTanpaRisiko++;

                // Dapatkan jenis unit kerja
                $jenisUnit = $kegiatan->unitKerja ? $kegiatan->unitKerja->nama_unit_kerja : 'default';

                // Pilih template risiko yang sesuai
                $templates = $templateRisiko[$jenisUnit] ?? $templateRisiko['default'];

                // Generate 2-3 risiko untuk kegiatan ini
                $jumlahRisiko = rand(2, 3);
                $selectedTemplates = array_slice($templates, 0, $jumlahRisiko);

                foreach ($selectedTemplates as $template) {
                    $tingkatRisiko = $this->getTingkatRisiko($template['kemungkinan'], $template['dampak']);

                    $peta = Peta::create([
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
                        'tampil_manajemen_risiko' => 0, // Default tidak tampil, nanti bisa di-update via UI
                        'status_telaah' => 0,
                        'metode' => 'Risk Assessment',
                        'pernyataan' => 'Risiko ini telah diidentifikasi dan perlu dilakukan mitigasi',
                        'pengendalian' => 'Monitoring berkala dan evaluasi risiko',
                        'mitigasi' => 'Penyusunan rencana mitigasi risiko dan tindakan preventif',
                        'waktu' => Carbon::now()->format('Y-m-d'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    $totalAdded++;
                }

                $this->command->info("✅ Menambahkan {$jumlahRisiko} risiko untuk kegiatan: {$kegiatan->judul} (ID: {$kegiatan->id_kegiatan})");
            }
        }

        $this->command->info("\n📈 RINGKASAN:");
        $this->command->info("   - Total Kegiatan: " . $allKegiatans->count());
        $this->command->info("   - Kegiatan Tanpa Risiko: {$kegiatanTanpaRisiko}");
        $this->command->info("   - Total Risiko Ditambahkan: {$totalAdded}");
        $this->command->info("\n✨ Seeder selesai dijalankan!");
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

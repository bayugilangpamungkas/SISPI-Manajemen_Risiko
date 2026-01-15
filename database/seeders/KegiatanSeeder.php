<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kegiatan;
use App\Models\Peta;

class KegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ✅ Ambil semua unit kerja yang ada di database
        $unitKerjas = \App\Models\UnitKerja::all();

        if ($unitKerjas->isEmpty()) {
            $this->command->info('⚠️ Tidak ada unit kerja di database. Silakan jalankan UnitKerjaSeeder terlebih dahulu.');
            return;
        }

        $this->command->info('📊 Menambahkan kegiatan untuk ' . $unitKerjas->count() . ' unit kerja...');

        // ✅ Template kegiatan yang akan ditambahkan ke setiap unit kerja
        $kegiatanTemplates = [
            [
                'judul' => 'Pengembangan Kurikulum dan Pembelajaran',
                'iku' => 'IKU 1 - Kualitas Pendidikan',
                'sasaran' => 'Meningkatkan kualitas pembelajaran',
                'proker' => 'Program Kerja Akademik 2026',
                'indikator' => 'Jumlah program studi terakreditasi',
                'anggaran' => 150000000,
            ],
            [
                'judul' => 'Peningkatan Penelitian dan Publikasi',
                'iku' => 'IKU 2 - Riset dan Inovasi',
                'sasaran' => 'Meningkatkan jumlah publikasi internasional',
                'proker' => 'Program Kerja Penelitian 2026',
                'indikator' => 'Jumlah publikasi bereputasi',
                'anggaran' => 200000000,
            ],
            [
                'judul' => 'Program Kerjasama dan Kemitraan',
                'iku' => 'IKU 3 - Kemitraan Industri',
                'sasaran' => 'Memperluas jejaring kerjasama',
                'proker' => 'Program Kerja Kemitraan 2026',
                'indikator' => 'Jumlah MoU aktif',
                'anggaran' => 180000000,
            ],
            [
                'judul' => 'Peningkatan Layanan dan Infrastruktur',
                'iku' => 'IKU 4 - Layanan Berkualitas',
                'sasaran' => 'Meningkatkan kualitas layanan institusi',
                'proker' => 'Program Kerja Sarana Prasarana 2026',
                'indikator' => 'Indeks kepuasan pengguna',
                'anggaran' => 250000000,
            ],
            [
                'judul' => 'Pengembangan SDM dan Kompetensi',
                'iku' => 'IKU 5 - Sumber Daya Manusia',
                'sasaran' => 'Meningkatkan kompetensi SDM',
                'proker' => 'Program Kerja Pengembangan SDM 2026',
                'indikator' => 'Persentase dosen berkualifikasi S3',
                'anggaran' => 300000000,
            ],
        ];

        $counter = 1;

        // ✅ Loop untuk setiap unit kerja
        foreach ($unitKerjas as $unitKerja) {
            $this->command->info("  ➤ Menambahkan kegiatan untuk: {$unitKerja->nama_unit_kerja}");

            // Tambahkan 3-5 kegiatan per unit kerja (random)
            $jumlahKegiatan = rand(3, 5);

            for ($i = 0; $i < $jumlahKegiatan; $i++) {
                // Pilih template secara random
                $template = $kegiatanTemplates[array_rand($kegiatanTemplates)];

                Kegiatan::create([
                    'id_unit_kerja' => $unitKerja->id,
                    'id_kegiatan' => sprintf('KEG-2026-%03d', $counter),
                    'judul' => $template['judul'] . ' - ' . $unitKerja->nama_unit_kerja,
                    'iku' => $template['iku'],
                    'sasaran' => $template['sasaran'],
                    'proker' => $template['proker'],
                    'indikator' => $template['indikator'],
                    'anggaran' => $template['anggaran'] + rand(-50000000, 50000000), // Variasi anggaran
                ]);

                $counter++;
            }
        }

        $this->command->info("✅ Berhasil menambahkan total " . ($counter - 1) . " kegiatan untuk {$unitKerjas->count()} unit kerja!");

        // Update data Peta yang sudah ada dengan id_kegiatan (foreign key ke tabel kegiatans)
        $this->assignKegiatanToPeta();
    }

    private function assignKegiatanToPeta()
    {
        $this->command->info("\n🔗 Mengassign kegiatan ke data peta risiko...");

        $petas = Peta::all();
        $kegiatans = Kegiatan::all();

        if ($kegiatans->isEmpty()) {
            $this->command->warn('⚠️ Tidak ada kegiatan untuk diassign ke peta risiko.');
            return;
        }

        $updated = 0;
        foreach ($petas as $peta) {
            // Cari unit kerja berdasarkan nama jenis
            $unitKerja = \App\Models\UnitKerja::where('nama_unit_kerja', $peta->jenis)->first();

            if ($unitKerja) {
                // Ambil kegiatan dari unit kerja yang sama
                $kegiatanByUnit = $kegiatans->where('id_unit_kerja', $unitKerja->id);

                if ($kegiatanByUnit->isNotEmpty()) {
                    // Assign kegiatan secara random dari unit kerja yang sama
                    $kegiatan = $kegiatanByUnit->random();
                    $peta->update(['id_kegiatan' => $kegiatan->id]);
                    $updated++;
                }
            }
        }

        $this->command->info("✅ Berhasil mengassign kegiatan ke {$updated} data peta risiko!");
    }
}

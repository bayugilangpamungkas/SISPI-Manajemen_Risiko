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
        $kegiatanData = [
            [
                'id_unit_kerja' => 1, // Set default
                'judul' => 'Pengembangan Kurikulum Berbasis Industri 4.0',
                'iku' => 'IKU 1 - Kualitas Pendidikan',
                'sasaran' => 'Meningkatkan relevansi kurikulum dengan kebutuhan industri',
                'proker' => 'Program Kerja Akademik 2026',
                'indikator' => 'Jumlah kurikulum yang disesuaikan',
                'anggaran' => 150000000,
            ],
            [
                'id_unit_kerja' => 1,
                'judul' => 'Peningkatan Kualitas Pembelajaran Digital',
                'iku' => 'IKU 2 - Digitalisasi Pembelajaran',
                'sasaran' => 'Meningkatkan kualitas pembelajaran online',
                'proker' => 'Program Kerja Akademik 2026',
                'indikator' => 'Persentase mata kuliah digital',
                'anggaran' => 200000000,
            ],
            [
                'id_unit_kerja' => 1,
                'judul' => 'Pengembangan Penelitian Kolaboratif',
                'iku' => 'IKU 3 - Riset dan Publikasi',
                'sasaran' => 'Meningkatkan jumlah publikasi internasional',
                'proker' => 'Program Kerja Penelitian 2026',
                'indikator' => 'Jumlah publikasi internasional',
                'anggaran' => 300000000,
            ],
            [
                'id_unit_kerja' => 1,
                'judul' => 'Program Kerjasama Industri',
                'iku' => 'IKU 4 - Kemitraan',
                'sasaran' => 'Meningkatkan kerjasama dengan industri',
                'proker' => 'Program Kerja Kemitraan 2026',
                'indikator' => 'Jumlah MoU dengan industri',
                'anggaran' => 250000000,
            ],
            [
                'id_unit_kerja' => 1,
                'judul' => 'Peningkatan Layanan Penjaminan Mutu',
                'iku' => 'IKU 5 - Jaminan Mutu',
                'sasaran' => 'Meningkatkan nilai akreditasi institusi',
                'proker' => 'Program Kerja BPKU 2026',
                'indikator' => 'Nilai akreditasi institusi',
                'anggaran' => 100000000,
            ],
            [
                'id_unit_kerja' => 1,
                'judul' => 'Upgrade Infrastruktur Jaringan Kampus',
                'iku' => 'IKU 6 - Infrastruktur TIK',
                'sasaran' => 'Meningkatkan kualitas infrastruktur TIK',
                'proker' => 'Program Kerja UPA TIK 2026',
                'indikator' => 'Kecepatan dan stabilitas jaringan',
                'anggaran' => 600000000,
            ],
        ];

        foreach ($kegiatanData as $data) {
            Kegiatan::create($data);
        }

        // Update data Peta yang sudah ada dengan id_kegiatan
        $this->assignKegiatanToPeta();
    }

    private function assignKegiatanToPeta()
    {
        $petas = Peta::all();
        $kegiatans = Kegiatan::all();

        if ($kegiatans->isEmpty()) {
            return;
        }

        foreach ($petas as $peta) {
            // Assign kegiatan secara random
            $kegiatan = $kegiatans->random();
            $peta->update(['id_kegiatan' => $kegiatan->id]);
        }
    }
}

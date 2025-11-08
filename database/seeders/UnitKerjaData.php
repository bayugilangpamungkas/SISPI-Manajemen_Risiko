<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitKerjaData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unit_kerja = [
            ['id' => 1, 'nama_unit_kerja' => 'WADIR I'],
            ['id' => 2, 'nama_unit_kerja' => 'WADIR II'],
            ['id' => 3, 'nama_unit_kerja' => 'WADIR III'],
            ['id' => 4, 'nama_unit_kerja' => 'WADIR IV'],
            ['id' => 5, 'nama_unit_kerja' => 'BPKU'],
            ['id' => 6, 'nama_unit_kerja' => 'BAK'],
            ['id' => 7, 'nama_unit_kerja' => 'SENAT'],
            ['id' => 8, 'nama_unit_kerja' => 'DEWAS'],
            ['id' => 9, 'nama_unit_kerja' => 'SPI'],
            ['id' => 10, 'nama_unit_kerja' => 'Kesekretariatan Direktur'],
            ['id' => 11, 'nama_unit_kerja' => 'UPA Layanan Uji Kompetensi'],
            ['id' => 12, 'nama_unit_kerja' => 'UPA Bahasa'],
            ['id' => 13, 'nama_unit_kerja' => 'UPA Percetakan dan Penerbitan'],
            ['id' => 14, 'nama_unit_kerja' => 'UPA Perpustakaan'],
            ['id' => 15, 'nama_unit_kerja' => 'UPA TIK'],
            ['id' => 16, 'nama_unit_kerja' => 'UPA Perawatan dan Perbaikan'],
            ['id' => 17, 'nama_unit_kerja' => 'UPA Pengembangan Karir dan Kewirausahaan'],
            ['id' => 18, 'nama_unit_kerja' => 'P2MPP'],
            ['id' => 19, 'nama_unit_kerja' => 'P3M'],
            ['id' => 20, 'nama_unit_kerja' => 'PEDP'],
            ['id' => 21, 'nama_unit_kerja' => 'Program PSTBI/CF PS D3 Manajemen Informatika'],
            ['id' => 22, 'nama_unit_kerja' => 'Program PSTBI/CF PS D4 Teknologi Kimia Industri'],
            ['id' => 23, 'nama_unit_kerja' => 'Program PD2JC D4 Teknologi Informasi'],
            ['id' => 24, 'nama_unit_kerja' => 'PTPPV - Penguatan Ekosistem Kewirausahaan'],
            ['id' => 25, 'nama_unit_kerja' => 'Matching Fund'],
            ['id' => 26, 'nama_unit_kerja' => 'Competitive Fund'],
            ['id' => 27, 'nama_unit_kerja' => 'Program Perjanjian Kerja Sama (Hibah) Kementerian'],
            ['id' => 28, 'nama_unit_kerja' => 'Program Matching Fund (MF) PS D4 Teknologi Informatika'],
            ['id' => 29, 'nama_unit_kerja' => 'Program PSTBI/CF PS D4 Manajemen Pemasaran'],
            ['id' => 30, 'nama_unit_kerja' => 'Program PSTBI/CF PS D3 Bahasa Inggris'],
            ['id' => 31, 'nama_unit_kerja' => 'Jurusan Teknik Elektro'],
            ['id' => 32, 'nama_unit_kerja' => 'D-III  Teknik Elektronika'],
            ['id' => 33, 'nama_unit_kerja' => 'D-III   Teknik Listrik'],
            ['id' => 34, 'nama_unit_kerja' => 'D-III  Teknik Telekomunikasi'],
            ['id' => 35, 'nama_unit_kerja' => 'D-IV Teknik Elektronika'],
            ['id' => 36, 'nama_unit_kerja' => 'D-IV Sistem Kelistrikan'],
            ['id' => 37, 'nama_unit_kerja' => 'D-IV Jaringan Telekomunikasi Digital'],
            ['id' => 38, 'nama_unit_kerja' => 'S-2 Teknik Elektro'],
            ['id' => 39, 'nama_unit_kerja' => 'Jurusan Teknik Mesin'],
            ['id' => 40, 'nama_unit_kerja' => 'D-III  Teknik Mesin'],
            ['id' => 41, 'nama_unit_kerja' => 'D-IV Teknik Otomotif Elektronik'],
            ['id' => 42, 'nama_unit_kerja' => 'D-IV Teknik Mesin Produksi dan Perawatan'],
            ['id' => 43, 'nama_unit_kerja' => 'S-2 Rekayasa Teknologi Manufaktur'],
            ['id' => 44, 'nama_unit_kerja' => 'D-III Teknologi Pemeliharaan Pesawat Udara'],
            ['id' => 45, 'nama_unit_kerja' => 'Jurusan Teknik Sipil'],
            ['id' => 46, 'nama_unit_kerja' => 'D-III Teknik Sipil'],
            ['id' => 47, 'nama_unit_kerja' => 'D-IV Manajemen Rekayasa Konstruksi'],
            ['id' => 48, 'nama_unit_kerja' => 'D-III Teknologi Konstruksi Jalan, Jembatan, dan Bangunan Air'],
            ['id' => 49, 'nama_unit_kerja' => 'D-III Teknologi Pertambangan'],
            ['id' => 50, 'nama_unit_kerja' => 'D-IV Teknologi Rekayasa Konstruksi Jalan dan Jembatan'],
            ['id' => 51, 'nama_unit_kerja' => 'Jurusan Teknik Kimia'],
            ['id' => 52, 'nama_unit_kerja' => 'D-III Teknik Kimia'],
            ['id' => 53, 'nama_unit_kerja' => 'D-IV Teknik Kimia Industri'],
            ['id' => 54, 'nama_unit_kerja' => 'S-2 Optimasi Proses Kimia'],
            ['id' => 55, 'nama_unit_kerja' => 'Jurusan Akuntansi'],
            ['id' => 56, 'nama_unit_kerja' => 'D-III Akuntansi'],
            ['id' => 57, 'nama_unit_kerja' => 'D-IV Akuntansi Manajemen'],
            ['id' => 58, 'nama_unit_kerja' => 'D-IV Keuangan'],
            ['id' => 59, 'nama_unit_kerja' => 'S-2 Sistem Informasi Akuntansi'],
            ['id' => 60, 'nama_unit_kerja' => 'Jurusan Administrasi Niaga'],
            ['id' => 61, 'nama_unit_kerja' => 'D-III Administrasi Bisnis'],
            ['id' => 62, 'nama_unit_kerja' => 'D-IV Manajemen Pemasaran'],
            ['id' => 63, 'nama_unit_kerja' => 'D-III Bahasa Inggris'],
            ['id' => 64, 'nama_unit_kerja' => 'D-IV Bahasa Inggris'],
            ['id' => 65, 'nama_unit_kerja' => 'D-IV Bahasa Inggris untuk KomBis & Profesional'],
            ['id' => 66, 'nama_unit_kerja' => 'D-IV Bahasa Inggris untuk Industri Pariwisata'],
            ['id' => 67, 'nama_unit_kerja' => 'D-IV Pengelolaan Arsip dan Rekaman Informasi'],
            ['id' => 68, 'nama_unit_kerja' => 'D-IV Usaha Perjalanan Wisata'],
            ['id' => 69, 'nama_unit_kerja' => 'Jurusan Teknologi Informasi'],
            ['id' => 70, 'nama_unit_kerja' => 'D-IV Teknik Informatika'],
            ['id' => 71, 'nama_unit_kerja' => 'D-IV Sistem Informasi Bisnis'],
            ['id' => 72, 'nama_unit_kerja' => 'D-II Perangkat Piranti Lunak Situs'],
            ['id' => 73, 'nama_unit_kerja' => 'S-2 Rekayasa Teknologi Informasi'],
            ['id' => 74, 'nama_unit_kerja' => 'Jurusan Bahasa Inggris'],
            ['id' => 75, 'nama_unit_kerja' => 'PSDKU Kota Kediri'],
            ['id' => 76, 'nama_unit_kerja' => 'D-III Teknik Mesin PSDKU Kediri'],
            ['id' => 77, 'nama_unit_kerja' => 'D-III Manajemen Informatika PSDKU Kediri'],
            ['id' => 78, 'nama_unit_kerja' => 'D-III Akuntansi PSDKU Kediri'],
            ['id' => 79, 'nama_unit_kerja' => 'D-IV Teknik Elektronika PSDKU Kediri'],
            ['id' => 80, 'nama_unit_kerja' => 'D-IV Teknik Mesin Produksi dan Perawatan PSDKU Kediri'],
            ['id' => 81, 'nama_unit_kerja' => 'D-IV Keuangan PSDKU Kediri'],
            ['id' => 82, 'nama_unit_kerja' => 'PSDKU Kota Lumajang'],
            ['id' => 83, 'nama_unit_kerja' => 'PSDKU Kota Lumajang (Dana Hibah)'],
            ['id' => 84, 'nama_unit_kerja' => 'D-III Teknologi Informasi PSDKU Lumajang'],
            ['id' => 85, 'nama_unit_kerja' => 'D-III Teknologi Sipil PSDKU Lumajang'],
            ['id' => 86, 'nama_unit_kerja' => 'D-III Akuntansi PSDKU Lumajang'],
            ['id' => 87, 'nama_unit_kerja' => 'D-IV Teknologi Rekayasa Otomotif PSDKU Lumajang'],
            ['id' => 88, 'nama_unit_kerja' => 'PSDKU Kota Pamekasan'],
            ['id' => 89, 'nama_unit_kerja' => 'PSDKU Kota Pamekasan (Dana Hibah)'],
            ['id' => 90, 'nama_unit_kerja' => 'D-III Manajemen Informatika PSDKU Pamekasan'],
            ['id' => 91, 'nama_unit_kerja' => 'D-IV Teknik Otomotif Elektronik PSDKU Pamekasan'],
            ['id' => 92, 'nama_unit_kerja' => 'D-IV Akuntansi Manajemen PSDKU Pamekasan'],
            ['id' => 93, 'nama_unit_kerja' => 'Ketata Usahaan'],
        ];

        foreach($unit_kerja as $key => $value){
            UnitKerja::create($value);
        }
    }
}

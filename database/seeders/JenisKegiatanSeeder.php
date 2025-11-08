<?php

namespace Database\Seeders;

use App\Models\JenisKegiatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisKegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $jenis = [
            [
                'jenis' => 'Pelatihan dan Sertifikasi',
            ],
            [
                'jenis' => 'Reviu, Audit, Monev',
            ],
            [
                'jenis' => 'Peraturan',
            ],
        ];
        foreach ($jenis as $key => $value) {
            JenisKegiatan::create($value);
        }
    }
}

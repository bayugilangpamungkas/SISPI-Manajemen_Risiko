<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aktivitas;

class AktivitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $aktivitas = [
        [
            'id_kualitas' => 1,
            'aktivitas_pengawasan' => 'Audit Ketaatan',
        ],
        [
            'id_kualitas' => 1,
            'aktivitas_pengawasan' => 'Audit Kinerja',
        ],
        [
            'id_kualitas' => 3,
            'aktivitas_pengawasan' => 'Asurans atas tata kelola, manajemen risiko, dan pengendalian organisasi K/L/D',
        ],
        [
            'id_kualitas' => 2,
            'aktivitas_pengawasan' => 'Jasa Konsultasi',
        ],
    ];
    foreach($aktivitas as $key => $value){
        Aktivitas::create($value);
    }
    }
}

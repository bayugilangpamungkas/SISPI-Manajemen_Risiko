<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kualitas;

class KualitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $kualitas = [
            [
                'kualitas_pengawasan' => 'Result 1: Keyakinan yang Memadai atas Ketaatan dan 3E'
            ],
            [
                'kualitas_pengawasan' => 'Result 2 : Early Warning dan Peningkatan Efektivitas MR'
            ],
            [
                'kualitas_pengawasan' => 'Result 3: Memelihara dan meningkatkan kualitas tata kelola'
            ],
        ];
        foreach($kualitas as $key => $value){
            Kualitas::create($value);
        }
    }
}

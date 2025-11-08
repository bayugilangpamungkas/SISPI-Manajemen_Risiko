<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ManagementElement;

class ManagementElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $elemen=[
            [
                'elemen' => 'Dukungan Pengawasan (Enabler)',
                'bobot_elemen' => 60,
            ],
            [
                'elemen' => 'Aktivitas Pengawasan (Delivery) dan Kualitas Pengawasan (Result)',
                'bobot_elemen' => 40,
            ],
        ];
        foreach($elemen as $key => $value){
            ManagementElement::create($value);
        }
    }
}

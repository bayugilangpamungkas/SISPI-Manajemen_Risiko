<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ManagementSubElement;

class ManagementSubElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subElement = [
            [
                'id_management_element' => 1,
                'sub_elemen' => 'Pengelolaan SDM',
                'bobot_sub_elemen' => 30,
            ],
            [
                'id_management_element' => 1,
                'sub_elemen' => 'Praktik Profesional',
                'bobot_sub_elemen' => 30,
            ],
            [
                'id_management_element' => 1,
                'sub_elemen' => 'Akuntabilitas dan Manajemen Kinerja',
                'bobot_sub_elemen' => 10,
            ],
            [
                'id_management_element' => 1,
                'sub_elemen' => 'Budaya dan Hubungan Organisasi',
                'bobot_sub_elemen' => 10,
            ],
            [
                'id_management_element' => 1,
                'sub_elemen' => 'Struktur Tata Kelola',
                'bobot_sub_elemen' => 20,
            ],
            [
                'id_management_element' => 2,
                'sub_elemen' => 'Peran dan Layanan',
                'bobot_sub_elemen' => 40,
            ],
        ];
        foreach($subElement as $key => $value){
            ManagementSubElement::create($value);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ManagementTopic;

class ManagementTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $topic=[
            [
                'id_management_sub_element' => 1,
                'topik' => 'Rekrutmen SDM SPI BLU',
            ],
            [
                'id_management_sub_element' => 1,
                'topik' => 'Pengembangan SDM Profesional SPI BLU',
            ],
            [
                'id_management_sub_element' => 2,
                'topik' => 'Perencanaan Pengawasan',
            ],
            [
                'id_management_sub_element' => 2,
                'topik' => 'Program Penjaminan dan Peningkatan Kualitas',
            ],
            [
                'id_management_sub_element' => 3,
                'topik' => 'Rencana kerja dan anggaran SPI BLU',
            ],
            [
                'id_management_sub_element' => 3,
                'topik' => 'Pelaksanaan Anggaran',
            ],
            [
                'id_management_sub_element' => 3,
                'topik' => 'Pelaporan kepada manajemen K/L/D',
            ],
            [
                'id_management_sub_element' => 3,
                'topik' => 'Sistem Pengukuran Kinerja SPI BLU',
            ],
            [
                'id_management_sub_element' => 4,
                'topik' => 'Pengelolaan Proses Bisnis Pengawasan Internal SPI BLU',
            ],
            [
                'id_management_sub_element' => 4,
                'topik' => 'Hubungan SPI BLU dengan Manajemen',
            ],
            [
                'id_management_sub_element' => 4,
                'topik' => 'Koordinasi dengan Pihak Lain yang Memberikan Saran dan Penjaminan',
            ],
            [
                'id_management_sub_element' => 5,
                'topik' => 'Mekanisme Pendanaan',
            ],
            [
                'id_management_sub_element' => 5,
                'topik' => 'Akses penuh terhadap informasi organisasi, aset dan SDM',
            ],
            [
                'id_management_sub_element' => 5,
                'topik' => 'Hubungan Pelaporan',
            ],
            [
                'id_management_sub_element' => 6,
                'topik' => 'Audit Ketaatan (Compliance Auditing)',
            ],
            [
                'id_management_sub_element' => 6,
                'topik' => 'Audit Kinerja (Performance Auditing)',
            ],
            [
                'id_management_sub_element' => 6,
                'topik' => 'Asurans atas tata kelola, manajemen risiko,
                 dan pengendalian organisasi K/L/D (Overall Assurance on
                  Governance, Risk, and Control/GRC)',
            ],
            [
                'id_management_sub_element' => 6,
                'topik' => 'Jasa Konsultansi (Advisory Services)',
            ],
        ];
        foreach($topic as $key => $value){
            ManagementTopic::create($value);
        }
    }
}

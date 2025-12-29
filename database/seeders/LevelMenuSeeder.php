<?php

namespace Database\Seeders;

use App\Models\Level_menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $level = [
            // ========================================
            // LEVEL 1: SUPER ADMIN
            // Full Access - Semua Menu
            // ========================================
            [
                'id_level' => '1',
                'id_menu' => '1',
            ],
            [
                'id_level' => '1',
                'id_menu' => '2',
            ],
            [
                'id_level' => '1',
                'id_menu' => '3',
            ],
            [
                'id_level' => '1',
                'id_menu' => '4',
            ],
            [
                'id_level' => '1',
                'id_menu' => '7',
            ],
            [
                'id_level' => '1',
                'id_menu' => '8',
            ],
            [
                'id_level' => '1',
                'id_menu' => '9',
            ],
            [
                'id_level' => '1',
                'id_menu' => '10',
            ],
            [
                'id_level' => '1',
                'id_menu' => '17',
            ],
            [
                'id_level' => '1',
                'id_menu' => '18',
            ],
            [
                'id_level' => '1',
                'id_menu' => '19',
            ],

            // ========================================
            // LEVEL 2: ADMIN
            // Akses: Dashboard, User Management, Peta Risiko, 
            // Dokumen, Unit Kerja, Manajemen Risiko
            // ========================================
            [
                'id_level' => '2',
                'id_menu' => '1',
            ],
            [
                'id_level' => '2',
                'id_menu' => '2',
            ],
            [
                'id_level' => '2',
                'id_menu' => '3',
            ],
            [
                'id_level' => '2',
                'id_menu' => '4',
            ],
            [
                'id_level' => '2',
                'id_menu' => '7',
            ],
            [
                'id_level' => '2',
                'id_menu' => '8',
            ],
            [
                'id_level' => '2',
                'id_menu' => '10',
            ],
            [
                'id_level' => '2',
                'id_menu' => '18',
            ],
            [
                'id_level' => '2',
                'id_menu' => '19',
            ],
            [
                'id_level' => '2',
                'id_menu' => '21', // Menu: Manajemen Risiko
            ],

            // ========================================
            // LEVEL 3: KETUA
            // Akses: Dashboard, User Management, Laporan,
            // Peta Risiko, Berita Acara
            // ========================================
            [
                'id_level' => '3',
                'id_menu' => '1',
            ],
            [
                'id_level' => '3',
                'id_menu' => '2',
            ],
            [
                'id_level' => '3',
                'id_menu' => '3',
            ],
            [
                'id_level' => '3',
                'id_menu' => '4',
            ],
            [
                'id_level' => '3',
                'id_menu' => '7',
            ],
            [
                'id_level' => '3',
                'id_menu' => '19',
            ],

            // ========================================
            // LEVEL 4: ANGGOTA
            // Akses: Dashboard, User Management, Laporan,
            // Peta Risiko, Berita Acara
            // ========================================
            [
                'id_level' => '4',
                'id_menu' => '1',
            ],
            [
                'id_level' => '4',
                'id_menu' => '2',
            ],
            [
                'id_level' => '4',
                'id_menu' => '3',
            ],
            [
                'id_level' => '4',
                'id_menu' => '4',
            ],
            [
                'id_level' => '4',
                'id_menu' => '7',
            ],
            [
                'id_level' => '4',
                'id_menu' => '19',
            ],

            // ========================================
            // LEVEL 5: AUDITEE
            // Akses: Dashboard, Laporan, Peta Risiko,
            // Dokumen, Berita Acara
            // ========================================
            [
                'id_level' => '5',
                'id_menu' => '1',
            ],
            [
                'id_level' => '5',
                'id_menu' => '3',
            ],
            [
                'id_level' => '5',
                'id_menu' => '4',
            ],
            [
                'id_level' => '5',
                'id_menu' => '7',
            ],
            [
                'id_level' => '5',
                'id_menu' => '8',
            ],
            [
                'id_level' => '5',
                'id_menu' => '19',
            ],

            // ========================================
            // LEVEL 6: SEKRETARIS
            // Akses: Dashboard, User Management, Laporan,
            // Peta Risiko, Dokumen, Template, Berita Acara
            // ========================================
            [
                'id_level' => '6',
                'id_menu' => '1',
            ],
            [
                'id_level' => '6',
                'id_menu' => '2',
            ],
            [
                'id_level' => '6',
                'id_menu' => '3',
            ],
            [
                'id_level' => '6',
                'id_menu' => '4',
            ],
            [
                'id_level' => '6',
                'id_menu' => '7',
            ],
            [
                'id_level' => '6',
                'id_menu' => '18',
            ],
            [
                'id_level' => '6',
                'id_menu' => '19',
            ],
            [
                'id_level' => '6',
                'id_menu' => '20',
            ]

        ];

        foreach ($level as $key => $value) {
            Level_menu::create($value);
        }
    }
}

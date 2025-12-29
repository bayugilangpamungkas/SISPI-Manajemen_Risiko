<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menu = [
            [
                'id' => 1,
                'name' => 'Dashboard',
                'link' => '/dashboard',
                'icon' => 'fas fa-gauge',
            ],
            [
                'id' => 2,
                'name' => 'Rencana Kegiatan',
                'link' => '/posts',
                'icon' => 'fas fa-list-check',
            ],
            [
                'id' => 3,
                'name' => 'Tindak Lanjut',
                'link' => '/tindak-lanjut',
                'icon' => 'fas fa-notes-medical',
            ],
            [
                'id' => 4,
                'name' => 'RTM',
                'link' => '/rtm',
                'icon' => 'fas fa-toolbox',
            ],
            // [
            //     'id' => 5,
            //     'name' => 'Master Kegiatan',
            //     'link' => '/kegiatan',
            //     'icon' => 'fas fa-paperclip',
            // ],
            [
                'id' => 7,
                'name' => 'Peta Risiko',
                'link' => '/petas',
                'icon' => 'far fa-newspaper',
            ],
            [
                'id' => 8,
                'name' => 'Dokumen Reviu',
                'link' => '/dokumens',
                'icon' => 'fas fa-file',
            ],
            [
                'id' => 9,
                'name' => 'Setting Menu',
                'link' => '/admin/panel',
                'icon' => 'fas fa-gear',
            ],
            [
                'id' => 10,
                'name' => 'Manajemen User',
                'link' => '/users',
                'icon' => 'fas fa-user',
            ],
            [
                'id' => 17,
                'name' => 'Unit Kerja',
                'link' => '/unit-kerja',
                'icon' => 'far fa-bookmark',
            ],
            [
                'id' => 18,
                'name' => 'Maturity Rating',
                'link' => '/MR',
                'icon' => 'fas fa-star',
            ],
            [
                'id' => 19,
                'name' => 'Template Dokumen',
                'link' => '/template-dokumen',
                'icon' => 'fas fa-paste',
            ],
            [
                'id' => 20,
                'name' => 'Berita Acara',
                'link' => '/berita-acara',
                'icon' => 'fas fa-paste',
            ],

            [
                'id' => 21,
                'name' => 'Manajemen Risiko',
                'link' => '/manajemen-risiko',
                'icon' => 'fas fa-shield-halved',
            ],
        ];

        foreach ($menu as $key => $value) {
            Menu::create($value);
        }
    }
}

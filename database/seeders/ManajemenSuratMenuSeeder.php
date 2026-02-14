<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManajemenSuratMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Cek apakah menu sudah ada
        $existingMenu = DB::table('menus')->where('name', 'Manajemen Surat')->first();

        if (!$existingMenu) {
            // Insert menu baru
            $menuId = DB::table('menus')->insertGetId([
                'name' => 'Manajemen Surat',
                'link' => '/surat',
                'icon' => 'fas fa-envelope',
                'id_head_menu' => null,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Get level ID untuk Super Admin dan Admin
            $superAdminLevel = DB::table('levels')->where('name', 'Super Admin')->first();
            $adminLevel = DB::table('levels')->where('name', 'Admin')->first();

            // Insert level menu (hanya Super Admin dan Admin yang bisa akses)
            if ($superAdminLevel) {
                DB::table('level_menus')->insert([
                    'id_menu' => $menuId,
                    'id_level' => $superAdminLevel->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($adminLevel) {
                DB::table('level_menus')->insert([
                    'id_menu' => $menuId,
                    'id_level' => $adminLevel->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->command->info('✅ Menu Manajemen Surat berhasil ditambahkan!');
        } else {
            $this->command->info('ℹ️ Menu Manajemen Surat sudah ada di database.');
        }
    }
}

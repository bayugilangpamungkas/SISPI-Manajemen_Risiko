<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(MenuData::class);
        $this->call(LevelSeeder::class);
        $this->call(LevelMenuSeeder::class);
        $this->call(UnitKerjaData::class);
        $this->call(UserData::class);
        $this->call(PostSeeder::class);
        $this->call(ManagementElementSeeder::class);
        $this->call(ManagementSubElementSeeder::class);
        $this->call(ManagementTopicSeeder::class);
        $this->call(UraianSeeder::class);
        $this->call(ManagementPengawasanSeeder::class);
        $this->call(JenisKegiatanSeeder::class);
        $this->call(BeritaAcaraSeeder::class);
        $this->call(PetaSeeder::class);
    }
}

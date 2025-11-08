<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $level=[
            [
                'name' => 'Super Admin',
            ],
            [
                'name' => 'Admin',
            ],
            [
                'name' => 'Ketua',
            ],
            [
                'name' => 'Anggota',
            ],
            [
                'name' => 'Auditee',
            ],
            [
                'name' => 'Sekretaris',
            ],

        ];

        foreach($level as $key => $value){
            Level::create($value);
        }
    }
}

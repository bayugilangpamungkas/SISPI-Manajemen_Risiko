<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $post = [[
            'waktu' => 'Senin, 1 Juli 2024',
            'tempat' => 'Graha Polinema lt. 3',
            'id_unit_kerja' => 5,
            // 'anggota' => 'audit_test',
            'jenis' => '2',
            'judul' => 'Reviu Laporan Juni 2024',
            'deskripsi' => 'Merevieu Laporan Kuangan yang telah dikerjakan sebelumnya',
            'bidang' => 'Reviu, Audit, Monev',
            'tanggungjawab' => 'Ir. Wahiddin, ST., MT.IPM.ASEANEng.',
            'status_task' => 'pending',	
        ]];

        foreach($post as $key => $value){
            Post::create($value);
        }
    }
}

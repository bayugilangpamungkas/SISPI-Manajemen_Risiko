<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            [
                'name' => 'Super Admin',
                'username' => '198609232015041001',
                'password' => Hash::make('123456'),
                'nip' => '198609232015041001',
                'id_level' => 1,
                'id_unit_kerja' => 1,
                'email' => 'usmannurhasan@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Usman Nurhasan, S.Kom., MT',
                'username' => '198609232015041001',
                'password' => Hash::make('123456'),
                'nip' => '198609232015041001',
                'id_level' => 6,
                'id_unit_kerja' => 1,
                'email' => 'usmannurhasan@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Andriyani Dwi Saputri, SE',
                'username' => 'admin1',
                'password' => bcrypt('123456'),
                'nip' => '1',
                'id_level' => 2,
                'id_unit_kerja' => 2,
                'email' => 'andriyani@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Syamsul Arifin',
                'username' => 'admin2',
                'password' => bcrypt('123456'),
                'nip' => '1',
                'id_level' => 2,
                'id_unit_kerja' => 2,
                'email' => 'syamsul.ar@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Dr. Drs. Sumiadji, Ak., M.SA.',
                'username' => '196405121993031000',
                'password' => bcrypt('123456'),
                'nip' => '196405121993031000',
                'id_level' => 3,
                'id_unit_kerja' => 3,
                'email' => 'sumiadji@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Ir. Wahiddin, ST., MT.IPM.ASEANEng.',
                'username' => '197312072003121000',
                'password' => bcrypt('123456'),
                'nip' => '197312072003121000',
                'id_level' => 4,
                'id_unit_kerja' => 3,
                'email' => 'wahiddin@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Lina Budiarti, S.E., M.M.',
                'username' => '198909162014042001',
                'password' => bcrypt('123456'),
                'nip' => '198909162014042001',
                'id_level' => 4,
                'id_unit_kerja' => 4,
                'email' => 'linabudiarti@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Dr. Ir. Azam Muzakhim Imammuddin, M.T.',
                'username' => '196705041994031000',
                'password' => bcrypt('123456'),
                'nip' => '196705041994031000',
                'id_level' => 4,
                'id_unit_kerja' => 4,
                'email' => 'azam@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Lis Diana Mustafa, S.T., M.T.',
                'username' => '197805052001122003',
                'password' => bcrypt('123456'),
                'nip' => '197805052001122003',
                'id_level' => 4,
                'id_unit_kerja' => 4,
                'email' => 'lis.diana@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Drs. Mufid, M.T.',
                'username' => '196408221994031001',
                'password' => bcrypt('123456'),
                'nip' => '196408221994031001',
                'id_level' => 4,
                'id_unit_kerja' => 4,
                'email' => 'mufid@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Atik Andhayani, S.E., MSA',
                'username' => '197505032009032001',
                'password' => bcrypt('123456'),
                'nip' => '197505032009032001',
                'id_level' => 4,
                'id_unit_kerja' => 4,
                'email' => 'atik.andhayani@polinema.ac.id',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'audit_test',
                'username' => 'audit_test',
                'password' => bcrypt('123456'),
                'nip' => '111',
                'id_level' => 5,
                'id_unit_kerja' => 5,
                'email' => 'audit_test@gmail.com',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Tes Auditee',
                'username' => 'Auditee2',
                'password' => bcrypt('123456'),
                'nip' => '2222222222222',
                'id_level' => 5,
                'id_unit_kerja' => 1,
                'email' => 'auditee2@gmail.com',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'sekretaris_test',
                'username' => 'sekretaris_test',
                'password' => bcrypt('123456'),
                'nip' => '1111',
                'id_level' => 6,
                'id_unit_kerja' => 6,
                'email' => 'sekretaris@gmail.com',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
            [
                'name' => 'Test Super Admin',
                'username' => 'test',
                'password' => Hash::make('123'),
                'nip' => '123',
                'id_level' => 1,
                'id_unit_kerja' => 1,
                'email' => 'test@gmail.com',
                'email_verified_at' => now(),
                'is_approved' => true,
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}

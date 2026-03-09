<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'role' => 'pengelola_sistem',
            ],
            [
                'name' => 'Pengelola Gedung Teknik',
                'email' => 'pengelola.teknik@upr.ac.id',
                'role' => 'pengelola_gedung',
            ],
            [
                'name' => 'Dr. Budi Santoso',
                'email' => 'budi.santoso@upr.ac.id',
                'role' => 'peminjam',
            ],
            [
                'name' => 'Andi Mahasiswa',
                'email' => 'andi@student.upr.ac.id',
                'role' => 'peminjam',
            ],
            [
                'name' => 'BEM Universitas',
                'email' => 'bem@student.upr.ac.id',
                'role' => 'peminjam',
            ],
            [
                'name' => 'Tamu Eksternal',
                'email' => 'tamu@external.com',
                'role' => 'peminjam',
            ],
        ];

        foreach ($users as $data) {
            $roleId = Role::where('slug', $data['role'])->value('id');
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                    'role_id' => $roleId,
                ]
            );
        }
    }
}

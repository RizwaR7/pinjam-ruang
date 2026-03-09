<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Pengelola Sistem',
                'slug' => 'pengelola_sistem',
                'description' => 'Super Administrator — akses penuh ke seluruh sistem',
            ],
            [
                'name' => 'Pengelola Gedung',
                'slug' => 'pengelola_gedung',
                'description' => 'Pengelola Gedung/Fakultas/Unit — mengelola ruangan dan approval peminjaman',
            ],
            [
                'name' => 'Peminjam',
                'slug' => 'peminjam',
                'description' => 'Peminjam (Dosen/Mahasiswa/Ormawa/Tamu/Eksternal) — mengajukan dan melacak peminjaman ruangan',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name'], 'description' => $role['description']]
            );
        }
    }
}
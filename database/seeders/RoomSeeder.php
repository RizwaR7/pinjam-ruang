<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            // Universitas-level rooms
            [
                'name' => 'Aula Utama Universitas',
                'code' => 'AULA-01',
                'scope' => 'universitas',
                'faculty' => null,
                'capacity' => 500,
                'facilities' => 'Proyektor, Sound System, AC Central, Panggung',
                'building' => 'Gedung Rektorat',
                'floor' => '1',
                'location' => 'Gedung Rektorat, Lt. 1',
                'status' => 'tersedia',
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Rapat Senat',
                'code' => 'RRS-01',
                'scope' => 'universitas',
                'faculty' => null,
                'capacity' => 50,
                'facilities' => 'Proyektor, Teleconference, AC, Whiteboard',
                'building' => 'Gedung Rektorat',
                'floor' => '3',
                'location' => 'Gedung Rektorat, Lt. 3',
                'status' => 'tersedia',
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Seminar Universitas',
                'code' => 'RSU-01',
                'scope' => 'universitas',
                'faculty' => null,
                'capacity' => 150,
                'facilities' => 'Proyektor, Sound System, AC, Podium',
                'building' => 'Gedung Perpustakaan',
                'floor' => '2',
                'location' => 'Gedung Perpustakaan, Lt. 2',
                'status' => 'tersedia',
                'is_active' => true,
            ],

            // Fakultas Teknik rooms
            [
                'name' => 'Lab Jaringan Komputer',
                'code' => 'FT-LJK-01',
                'scope' => 'fakultas',
                'faculty' => 'Fakultas Teknik',
                'capacity' => 40,
                'facilities' => 'PC 40 unit, Proyektor, AC, Switch Cisco',
                'building' => 'Gedung Teknik',
                'floor' => '2',
                'location' => 'Gedung Teknik, Lt. 2',
                'status' => 'tersedia',
                'is_active' => true,
            ],
            [
                'name' => 'Lab Pemrograman',
                'code' => 'FT-LP-01',
                'scope' => 'fakultas',
                'faculty' => 'Fakultas Teknik',
                'capacity' => 35,
                'facilities' => 'PC 35 unit, Proyektor, AC',
                'building' => 'Gedung Teknik',
                'floor' => '3',
                'location' => 'Gedung Teknik, Lt. 3',
                'status' => 'dipakai',
                'is_active' => true,
            ],
            [
                'name' => 'Ruang Kelas Teknik A',
                'code' => 'FT-RK-A',
                'scope' => 'fakultas',
                'faculty' => 'Fakultas Teknik',
                'capacity' => 60,
                'facilities' => 'Proyektor, AC, Whiteboard',
                'building' => 'Gedung Teknik',
                'floor' => '1',
                'location' => 'Gedung Teknik, Lt. 1',
                'status' => 'tersedia',
                'is_active' => true,
            ],

            // Fakultas Ekonomi rooms
            [
                'name' => 'Ruang Kelas Ekonomi 1',
                'code' => 'FE-RK-01',
                'scope' => 'fakultas',
                'faculty' => 'Fakultas Ekonomi',
                'capacity' => 50,
                'facilities' => 'Proyektor, AC, Whiteboard',
                'building' => 'Gedung Ekonomi',
                'floor' => '1',
                'location' => 'Gedung Ekonomi, Lt. 1',
                'status' => 'tersedia',
                'is_active' => true,
            ],
            [
                'name' => 'Lab Akuntansi',
                'code' => 'FE-LA-01',
                'scope' => 'fakultas',
                'faculty' => 'Fakultas Ekonomi',
                'capacity' => 30,
                'facilities' => 'PC 30 unit, Proyektor, AC, Software Akuntansi',
                'building' => 'Gedung Ekonomi',
                'floor' => '2',
                'location' => 'Gedung Ekonomi, Lt. 2',
                'status' => 'maintenance',
                'is_active' => true,
            ],
        ];

        foreach ($rooms as $room) {
            Room::updateOrCreate(
                ['code' => $room['code']],
                $room
            );
        }
    }
}

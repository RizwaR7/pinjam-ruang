<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
    public function run()
    {
        $equipment = [
            [
                'name' => 'Proyektor Epson EB-X51',
                'code' => 'PRJ-001',
                'description' => 'Proyektor portable 3800 lumens untuk presentasi',
                'category' => 'audio_visual',
                'quantity' => 5,
                'is_available' => true,
                'condition' => 'baik',
                'location' => 'Gudang Perlengkapan Lt. 1',
            ],
            [
                'name' => 'Sound System Portable',
                'code' => 'SND-001',
                'description' => 'Sound system portable dengan 2 mic wireless',
                'category' => 'audio_visual',
                'quantity' => 3,
                'is_available' => true,
                'condition' => 'baik',
                'location' => 'Gudang Perlengkapan Lt. 1',
            ],
            [
                'name' => 'Layar Proyektor Tripod',
                'code' => 'LPR-001',
                'description' => 'Layar proyektor tripod 84 inch',
                'category' => 'audio_visual',
                'quantity' => 4,
                'is_available' => true,
                'condition' => 'baik',
                'location' => 'Gudang Perlengkapan Lt. 1',
            ],
            [
                'name' => 'Laptop Presentasi',
                'code' => 'LPT-001',
                'description' => 'Laptop Lenovo ThinkPad untuk presentasi',
                'category' => 'elektronik',
                'quantity' => 3,
                'is_available' => true,
                'condition' => 'baik',
                'location' => 'Ruang IT Lt. 2',
            ],
            [
                'name' => 'Whiteboard Portable',
                'code' => 'WBD-001',
                'description' => 'Whiteboard portable dengan stand',
                'category' => 'furniture',
                'quantity' => 6,
                'is_available' => true,
                'condition' => 'baik',
                'location' => 'Gudang Perlengkapan Lt. 1',
            ],
            [
                'name' => 'Meja Lipat',
                'code' => 'MJL-001',
                'description' => 'Meja lipat serbaguna 120x60 cm',
                'category' => 'furniture',
                'quantity' => 20,
                'is_available' => true,
                'condition' => 'baik',
                'location' => 'Gudang Perlengkapan Lt. 1',
            ],
            [
                'name' => 'Kursi Lipat',
                'code' => 'KRL-001',
                'description' => 'Kursi lipat untuk acara',
                'category' => 'furniture',
                'quantity' => 100,
                'is_available' => true,
                'condition' => 'baik',
                'location' => 'Gudang Perlengkapan Lt. 1',
            ],
            [
                'name' => 'Pointer Presenter',
                'code' => 'PTR-001',
                'description' => 'Laser pointer wireless dengan USB receiver',
                'category' => 'elektronik',
                'quantity' => 5,
                'is_available' => true,
                'condition' => 'baik',
                'location' => 'Ruang IT Lt. 2',
            ],
            [
                'name' => 'Kamera Webcam HD',
                'code' => 'WBC-001',
                'description' => 'Webcam Logitech C920 HD untuk video conference',
                'category' => 'elektronik',
                'quantity' => 3,
                'is_available' => true,
                'condition' => 'rusak_ringan',
                'location' => 'Ruang IT Lt. 2',
            ],
            [
                'name' => 'Extension Kabel',
                'code' => 'EXT-001',
                'description' => 'Extension kabel listrik 10 meter',
                'category' => 'lainnya',
                'quantity' => 10,
                'is_available' => true,
                'condition' => 'baik',
                'location' => 'Gudang Perlengkapan Lt. 1',
            ],
        ];

        foreach ($equipment as $item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            DB::table('equipment')->insert($item);
        }
    }
}

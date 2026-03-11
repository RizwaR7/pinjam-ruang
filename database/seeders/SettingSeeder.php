<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'fine_per_day',
                'value' => '5000',
                'label' => 'Denda Keterlambatan (per hari)',
                'type' => 'number',
            ],
            [
                'key' => 'va_bank_name',
                'value' => '',
                'label' => 'Nama Bank / Metode Pembayaran',
                'type' => 'text',
            ],
            [
                'key' => 'va_account_number',
                'value' => '',
                'label' => 'Nomor Rekening / VA',
                'type' => 'text',
            ],
            [
                'key' => 'va_account_holder',
                'value' => '',
                'label' => 'Atas Nama Rekening',
                'type' => 'text',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}

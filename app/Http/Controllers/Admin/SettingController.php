<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'fine_per_day' => 'required|numeric|min:0',
            'va_bank_name' => 'nullable|string|max:100',
            'va_account_number' => 'nullable|string|max:50',
            'va_account_holder' => 'nullable|string|max:100',
        ]);

        Setting::set('fine_per_day', $request->fine_per_day);
        Setting::set('va_bank_name', $request->va_bank_name);
        Setting::set('va_account_number', $request->va_account_number);
        Setting::set('va_account_holder', $request->va_account_holder);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}

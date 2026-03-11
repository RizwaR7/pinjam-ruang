@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 shadow-xl relative overflow-hidden">
            <div
                class="absolute -top-24 -right-24 w-64 h-64 bg-navy-500/20 rounded-full mix-blend-screen filter blur-[80px]">
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-1">⚙️ Pengaturan Sistem</h1>
                <p class="text-sm text-slate-500 font-medium">Atur tarif denda dan informasi pembayaran</p>
            </div>
        </div>

        @if(session('success'))
            <div
                class="p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Fine Settings --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-amber-50/30">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Pengaturan Denda
                        </h3>
                    </div>
                    <div class="p-8 space-y-4">
                        <div class="space-y-2">
                            <label for="fine_per_day" class="block text-sm font-bold text-slate-700">Tarif Denda per Hari
                                (Rp)</label>
                            <input type="number" id="fine_per_day" name="fine_per_day" min="0" step="500"
                                value="{{ old('fine_per_day', $settings['fine_per_day']->value ?? 5000) }}"
                                class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white focus:outline-none">
                            @error('fine_per_day')<p class="text-sm text-rose-500">{{ $message }}</p>@enderror
                            <p class="text-xs text-slate-400">Denda dihitung otomatis berdasarkan jumlah hari keterlambatan
                            </p>
                        </div>
                    </div>
                </div>

                {{-- VA / Payment Settings --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-cyan-50/30">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                            Informasi Pembayaran
                        </h3>
                    </div>
                    <div class="p-8 space-y-4">
                        <div class="space-y-2">
                            <label for="va_bank_name" class="block text-sm font-bold text-slate-700">Nama Bank /
                                Metode</label>
                            <input type="text" id="va_bank_name" name="va_bank_name"
                                value="{{ old('va_bank_name', $settings['va_bank_name']->value ?? '') }}"
                                placeholder="Contoh: Bank Mandiri / BCA / BRI"
                                class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white focus:outline-none">
                        </div>
                        <div class="space-y-2">
                            <label for="va_account_number" class="block text-sm font-bold text-slate-700">Nomor Rekening /
                                VA</label>
                            <input type="text" id="va_account_number" name="va_account_number"
                                value="{{ old('va_account_number', $settings['va_account_number']->value ?? '') }}"
                                placeholder="Contoh: 1234567890"
                                class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white focus:outline-none">
                        </div>
                        <div class="space-y-2">
                            <label for="va_account_holder" class="block text-sm font-bold text-slate-700">Atas Nama</label>
                            <input type="text" id="va_account_holder" name="va_account_holder"
                                value="{{ old('va_account_holder', $settings['va_account_holder']->value ?? '') }}"
                                placeholder="Contoh: Universitas / Bendahara"
                                class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-gold-500 focus:border-gold-500 focus:bg-white focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-navy-500 to-navy-700 rounded-xl hover:from-navy-400 hover:to-navy-600 transition-all shadow-lg shadow-navy-500/20 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection
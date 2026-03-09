@extends('layouts.admin')

@section('content')
    <div class="space-y-6 ">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.rooms.index') }}" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-teal-600 hover:border-teal-200 hover:bg-teal-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Tambah Ruangan Baru</h1>
                <nav class="flex mt-1"><ol class="inline-flex items-center space-x-2 text-xs text-slate-500 font-medium">
                    <li><a href="{{ route('admin.home') }}" class="hover:text-teal-600">Dashboard</a></li>
                    <li><span class="mx-1 text-slate-300">/</span></li>
                    <li><a href="{{ route('admin.rooms.index') }}" class="hover:text-teal-600">Ruangan</a></li>
                    <li><span class="mx-1 text-slate-300">/</span></li>
                    <li class="text-teal-600 font-semibold">Tambah</li>
                </ol></nav>
            </div>
        </div>

        <div class="flex justify-center">
            <div class="w-full max-w-4xl">
                <div class="bg-white border border-slate-100 rounded-3xl shadow-xl shadow-slate-200/40 overflow-hidden relative">
                    <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-gradient-to-br from-teal-500/10 to-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center gap-4 bg-slate-50/50 relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Informasi Ruangan</h2>
                            <p class="text-sm text-slate-500">Lengkapi data untuk menambahkan ruangan baru ke sistem.</p>
                        </div>
                    </div>

                    <div class="p-8 relative z-10">
                        <form action="{{ route('admin.rooms.store') }}" method="POST" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-bold text-slate-700">Nama Ruangan <span class="text-rose-500">*</span></label>
                                    <input type="text" id="name" name="name" class="block w-full px-4 py-3 bg-slate-50 border @error('name') border-rose-300 @else border-slate-200 focus:border-teal-500 focus:ring-teal-500 @enderror rounded-xl text-sm transition-all focus:bg-white focus:ring-2 focus:outline-none" value="{{ old('name') }}" placeholder="Contoh: Lab Jaringan Komputer">
                                    @error('name')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-2">
                                    <label for="code" class="block text-sm font-bold text-slate-700">Kode Ruangan <span class="text-rose-500">*</span></label>
                                    <input type="text" id="code" name="code" class="block w-full px-4 py-3 bg-slate-50 border @error('code') border-rose-300 @else border-slate-200 focus:border-teal-500 focus:ring-teal-500 @enderror rounded-xl text-sm font-mono transition-all focus:bg-white focus:ring-2 focus:outline-none" value="{{ old('code') }}" placeholder="Contoh: FT-LJK-01">
                                    @error('code')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="scope" class="block text-sm font-bold text-slate-700">Scope Kepemilikan <span class="text-rose-500">*</span></label>
                                    <select id="scope" name="scope" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white focus:outline-none" onchange="toggleFaculty()">
                                        <option value="universitas" {{ old('scope') === 'universitas' ? 'selected' : '' }}>Universitas</option>
                                        <option value="fakultas" {{ old('scope') === 'fakultas' ? 'selected' : '' }}>Fakultas</option>
                                    </select>
                                    @error('scope')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-2" id="facultyField" style="{{ old('scope', 'universitas') === 'universitas' ? 'display:none' : '' }}">
                                    <label for="faculty" class="block text-sm font-bold text-slate-700">Fakultas <span class="text-rose-500">*</span></label>
                                    <input type="text" id="faculty" name="faculty" list="facultyList" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white focus:outline-none" value="{{ old('faculty') }}" placeholder="Contoh: Fakultas Teknik">
                                    <datalist id="facultyList">
                                        @foreach($faculties as $fac)
                                            <option value="{{ $fac }}">
                                        @endforeach
                                    </datalist>
                                    @error('faculty')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="capacity" class="block text-sm font-bold text-slate-700">Kapasitas (Orang) <span class="text-rose-500">*</span></label>
                                    <input type="number" id="capacity" name="capacity" min="1" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white focus:outline-none" value="{{ old('capacity') }}" placeholder="Contoh: 40">
                                    @error('capacity')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-2">
                                    <label for="location" class="block text-sm font-bold text-slate-700">Lokasi</label>
                                    <input type="text" id="location" name="location" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white focus:outline-none" value="{{ old('location') }}" placeholder="Contoh: Gedung Teknik, Lt. 2">
                                    @error('location')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label for="building" class="block text-sm font-bold text-slate-700">Gedung</label>
                                    <input type="text" id="building" name="building" list="buildingList" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white focus:outline-none" value="{{ old('building') }}" placeholder="Contoh: Gedung Teknik">
                                    <datalist id="buildingList">
                                        @foreach($buildings as $b)
                                            <option value="{{ $b }}">
                                        @endforeach
                                    </datalist>
                                    @error('building')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-2">
                                    <label for="floor" class="block text-sm font-bold text-slate-700">Lantai</label>
                                    <input type="text" id="floor" name="floor" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white focus:outline-none" value="{{ old('floor') }}" placeholder="Contoh: 2">
                                    @error('floor')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-2">
                                    <label for="status" class="block text-sm font-bold text-slate-700">Status Ruangan <span class="text-rose-500">*</span></label>
                                    <select id="status" name="status" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white focus:outline-none">
                                        <option value="tersedia" {{ old('status', 'tersedia') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="dipakai" {{ old('status') === 'dipakai' ? 'selected' : '' }}>Sedang Dipakai</option>
                                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    </select>
                                    @error('status')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="facilities" class="block text-sm font-bold text-slate-700">Fasilitas</label>
                                <textarea id="facilities" name="facilities" rows="3" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 focus:bg-white focus:outline-none resize-y" placeholder="Contoh: Proyektor, AC, Whiteboard, PC 40 unit">{{ old('facilities') }}</textarea>
                                @error('facilities')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="is_active" name="is_active" class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="text-sm font-semibold text-slate-700">Ruangan aktif dan tersedia untuk dipinjam</label>
                            </div>

                            <div class="pt-6 mt-8 border-t border-slate-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                                <a href="{{ route('admin.rooms.index') }}" class="inline-flex justify-center items-center px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">Batal</a>
                                <button type="submit" class="inline-flex justify-center items-center px-6 py-2.5 text-sm font-semibold text-white bg-teal-600 rounded-xl hover:bg-teal-700 transition-colors shadow-sm shadow-teal-600/20">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                    Simpan Ruangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>@keyframes fade-in { 0% { opacity:0; transform:translateY(10px); } 100% { opacity:1; transform:translateY(0); } } . { animation: fade-in 0.5s cubic-bezier(0.16,1,0.3,1) forwards; }</style>
    <script>
        function toggleFaculty() {
            document.getElementById('facultyField').style.display = document.getElementById('scope').value === 'fakultas' ? '' : 'none';
        }
    </script>
@endsection

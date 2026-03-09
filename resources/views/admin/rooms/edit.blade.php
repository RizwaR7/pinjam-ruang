@extends('layouts.admin')

@section('content')
    <div class="space-y-6 ">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.rooms.index') }}" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-amber-600 hover:border-amber-200 hover:bg-amber-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Ruangan</h1>
                <nav class="flex mt-1"><ol class="inline-flex items-center space-x-2 text-xs text-slate-500 font-medium">
                    <li><a href="{{ route('admin.home') }}" class="hover:text-amber-600">Dashboard</a></li>
                    <li><span class="mx-1 text-slate-300">/</span></li>
                    <li><a href="{{ route('admin.rooms.index') }}" class="hover:text-amber-600">Ruangan</a></li>
                    <li><span class="mx-1 text-slate-300">/</span></li>
                    <li class="text-amber-600 font-semibold truncate max-w-[150px]">Edit: {{ $room->name }}</li>
                </ol></nav>
            </div>
        </div>

        <div class="flex justify-center">
            <div class="w-full max-w-4xl">
                <div class="bg-white border border-slate-100 rounded-3xl shadow-xl shadow-slate-200/40 overflow-hidden relative">
                    <div class="absolute top-0 right-0 -mt-16 -mr-16 w-64 h-64 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center gap-4 bg-slate-50/50 relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Edit: {{ $room->name }}</h2>
                            <p class="text-sm border border-slate-200 bg-white px-2 py-0.5 rounded text-slate-500 font-mono inline-block mt-1">{{ $room->code }}</p>
                        </div>
                    </div>

                    <div class="p-8 relative z-10">
                        <form action="{{ route('admin.rooms.update', $room) }}" method="POST" class="space-y-6">
                            @csrf @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-bold text-slate-700">Nama Ruangan <span class="text-rose-500">*</span></label>
                                    <input type="text" id="name" name="name" class="block w-full px-4 py-3 bg-slate-50 border @error('name') border-rose-300 @else border-slate-200 focus:border-amber-500 focus:ring-amber-500 @enderror rounded-xl text-sm transition-all focus:bg-white focus:ring-2 focus:outline-none" value="{{ old('name', $room->name) }}">
                                    @error('name')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-2">
                                    <label for="code" class="block text-sm font-bold text-slate-700">Kode Ruangan <span class="text-rose-500">*</span></label>
                                    <input type="text" id="code" name="code" class="block w-full px-4 py-3 bg-slate-50 border @error('code') border-rose-300 @else border-slate-200 focus:border-amber-500 focus:ring-amber-500 @enderror rounded-xl text-sm font-mono transition-all focus:bg-white focus:ring-2 focus:outline-none" value="{{ old('code', $room->code) }}">
                                    @error('code')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="scope" class="block text-sm font-bold text-slate-700">Scope <span class="text-rose-500">*</span></label>
                                    <select id="scope" name="scope" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white focus:outline-none" onchange="toggleFaculty()">
                                        <option value="universitas" {{ old('scope', $room->scope) === 'universitas' ? 'selected' : '' }}>Universitas</option>
                                        <option value="fakultas" {{ old('scope', $room->scope) === 'fakultas' ? 'selected' : '' }}>Fakultas</option>
                                    </select>
                                </div>
                                <div class="space-y-2" id="facultyField" style="{{ old('scope', $room->scope) === 'universitas' ? 'display:none' : '' }}">
                                    <label for="faculty" class="block text-sm font-bold text-slate-700">Fakultas <span class="text-rose-500">*</span></label>
                                    <input type="text" id="faculty" name="faculty" list="facultyList" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white focus:outline-none" value="{{ old('faculty', $room->faculty) }}">
                                    <datalist id="facultyList">@foreach($faculties as $fac)<option value="{{ $fac }}">@endforeach</datalist>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="capacity" class="block text-sm font-bold text-slate-700">Kapasitas <span class="text-rose-500">*</span></label>
                                    <input type="number" id="capacity" name="capacity" min="1" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white focus:outline-none" value="{{ old('capacity', $room->capacity) }}">
                                    @error('capacity')<p class="text-sm text-rose-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div class="space-y-2">
                                    <label for="location" class="block text-sm font-bold text-slate-700">Lokasi</label>
                                    <input type="text" id="location" name="location" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white focus:outline-none" value="{{ old('location', $room->location) }}">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label for="building" class="block text-sm font-bold text-slate-700">Gedung</label>
                                    <input type="text" id="building" name="building" list="buildingList" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white focus:outline-none" value="{{ old('building', $room->building) }}">
                                    <datalist id="buildingList">@foreach($buildings as $b)<option value="{{ $b }}">@endforeach</datalist>
                                </div>
                                <div class="space-y-2">
                                    <label for="floor" class="block text-sm font-bold text-slate-700">Lantai</label>
                                    <input type="text" id="floor" name="floor" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white focus:outline-none" value="{{ old('floor', $room->floor) }}">
                                </div>
                                <div class="space-y-2">
                                    <label for="status" class="block text-sm font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
                                    <select id="status" name="status" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white focus:outline-none">
                                        <option value="tersedia" {{ old('status', $room->status) === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="dipakai" {{ old('status', $room->status) === 'dipakai' ? 'selected' : '' }}>Sedang Dipakai</option>
                                        <option value="maintenance" {{ old('status', $room->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label for="facilities" class="block text-sm font-bold text-slate-700">Fasilitas</label>
                                <textarea id="facilities" name="facilities" rows="3" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white focus:outline-none resize-y">{{ old('facilities', $room->facilities) }}</textarea>
                            </div>

                            <div class="flex items-center gap-3">
                                <input type="checkbox" id="is_active" name="is_active" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500" {{ old('is_active', $room->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="text-sm font-semibold text-slate-700">Ruangan aktif</label>
                            </div>

                            <div class="pt-6 mt-8 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                                <a href="{{ route('admin.rooms.show', $room) }}" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-cyan-700 bg-cyan-50 border border-cyan-100 rounded-xl hover:bg-cyan-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Detail
                                </a>
                                <div class="flex gap-3">
                                    <a href="{{ route('admin.rooms.index') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">Batal</a>
                                    <button type="submit" class="inline-flex items-center px-6 py-2.5 text-sm font-bold text-white bg-amber-500 rounded-xl hover:bg-amber-600 transition-colors shadow-sm shadow-amber-500/20">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                        Update Ruangan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>@keyframes fade-in { 0% { opacity:0; transform:translateY(10px); } 100% { opacity:1; transform:translateY(0); } } . { animation: fade-in 0.5s cubic-bezier(0.16,1,0.3,1) forwards; }</style>
    <script>function toggleFaculty() { document.getElementById('facultyField').style.display = document.getElementById('scope').value === 'fakultas' ? '' : 'none'; }</script>
@endsection

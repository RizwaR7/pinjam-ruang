@extends('layouts.admin')

@section('content')
    <div class="space-y-6 ">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Manajemen User</h1>
                <p class="text-sm text-slate-500 mt-1">Kelola semua pengguna sistem SIRUANG</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-navy-900 bg-gold-500 rounded-xl hover:bg-gold-400 transition-all shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah User
            </a>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="p-4 text-sm text-success-800 bg-success-50 border border-success-200 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5 text-success-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 text-sm text-danger-800 bg-danger-50 border border-danger-200 rounded-xl">{{ session('error') }}</div>
        @endif

        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3" hx-target="#table-container" hx-select="#table-container" hx-swap="outerHTML swap:200ms settle:200ms" hx-push-url="true">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                        class="block w-full px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-400 focus:outline-none transition-all">
                </div>
                <select name="role" class="px-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:ring-2 focus:ring-gold-400 focus:border-gold-400 focus:outline-none" onchange="this.form.submit()">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-navy-500 rounded-xl hover:bg-navy-600 transition-colors">Filter</button>
                @if(request()->anyFilled(['search','role']))
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition-colors text-center">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div id="table-container" class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden table-transition">
            <!-- Desktop Table -->
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left min-w-max">
                    <thead><tr class="text-xs text-slate-500 font-bold uppercase tracking-widest border-b border-slate-100 bg-slate-50/50">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Tanggal Daftar</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-navy-500 to-navy-700 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                            <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-navy-50 text-navy-700 border border-navy-100">
                                        {{ $user->role?->name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="p-2 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2 rounded-lg text-danger-600 hover:bg-danger-50 transition-colors" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400">Tidak ada user ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($users as $user)
                    <div class="p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-navy-500 to-navy-700 text-white flex items-center justify-center font-bold text-sm shrink-0">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pl-13">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-navy-50 text-navy-700 border border-navy-100">{{ $user->role?->name ?? '—' }}</span>
                                <span class="text-xs text-slate-400">{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 rounded-lg text-navy-600 hover:bg-navy-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin hapus user {{ $user->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-danger-600 hover:bg-danger-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">Tidak ada user ditemukan.</div>
                @endforelse
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
    <style>@keyframes fade-in{0%{opacity:0;transform:translateY(10px);}100%{opacity:1;transform:translateY(0);}}.{animation:fade-in .5s cubic-bezier(.16,1,.3,1) forwards;}</style>
@endsection

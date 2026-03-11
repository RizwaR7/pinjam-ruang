@extends('layouts.admin')

@section('content')
    <div class="space-y-8 ">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 shadow-xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-navy-500/20 rounded-full mix-blend-screen filter blur-[80px] animate-pulse-slow"></div>
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-1">Approval Peminjaman</h1>
                <nav class="flex"><ol class="inline-flex items-center space-x-2 text-sm text-slate-500 font-medium">
                    <li><a href="{{ route('admin.home') }}" class="hover:text-navy-600">Dashboard</a></li>
                    <li><span class="mx-1">/</span></li>
                    <li class="text-navy-600 font-semibold">Peminjaman</li>
                </ol></nav>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php $statItems = [
                ['label' => 'Menunggu', 'count' => $stats['pending'], 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Disetujui', 'count' => $stats['approved'], 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Ditolak', 'count' => $stats['rejected'], 'color' => 'rose', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Selesai', 'count' => $stats['finished'], 'color' => 'sky', 'icon' => 'M5 13l4 4L19 7'],
            ]; @endphp
            @foreach($statItems as $item)
                <div class="relative group rounded-2xl bg-white border border-slate-100 p-5 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden transform hover:-translate-y-0.5">
                    <div class="absolute inset-x-0 bottom-0 h-0.5 bg-{{ $item['color'] }}-400 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-{{ $item['color'] }}-50 border border-{{ $item['color'] }}-100 flex items-center justify-center text-{{ $item['color'] }}-500 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-800">{{ $item['count'] }}</p>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $item['label'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Table -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden table-transition" id="table-container">
            <div class="px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
                <div>
                    <h4 class="text-xl font-bold text-slate-800 mb-1">Daftar Peminjaman</h4>
                    <p class="text-sm text-slate-500">Kelola persetujuan peminjaman ruangan</p>
                </div>
                <form method="GET" action="{{ route('admin.bookings.index') }}" class="flex gap-2 flex-wrap w-full sm:w-auto" hx-target="#table-container" hx-select="#table-container" hx-swap="outerHTML swap:200ms settle:200ms" hx-push-url="true">
                    <select name="status" class="px-3 py-2 border border-slate-200 rounded-xl text-sm bg-white focus:ring-2 focus:ring-gold-500" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        <option value="finished" {{ request('status') === 'finished' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div>
                        <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:ring-2 focus:ring-gold-500 focus:border-gold-500" placeholder="Cari...">
                    </div>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center px-4 py-2 border border-slate-200 text-sm font-medium rounded-xl text-slate-700 bg-white hover:bg-slate-50">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Desktop Table -->
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left border-collapse min-w-max">
                    <thead><tr class="bg-white border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-widest">
                        <th class="px-8 py-5 w-16 text-center">#</th>
                        <th class="px-6 py-5">Peminjam</th>
                        <th class="px-6 py-5">Ruangan</th>
                        <th class="px-6 py-5">Tanggal</th>
                        <th class="px-6 py-5">Waktu</th>
                        <th class="px-6 py-5">Tujuan</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 text-center">Aksi</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($bookings as $i => $booking)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-8 py-4 text-center text-slate-400 font-medium">{{ $bookings->firstItem() + $i }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-500 to-navy-500 text-white flex items-center justify-center font-bold text-xs">{{ strtoupper(substr($booking->user->name, 0, 1)) }}</div>
                                        <span class="font-semibold text-slate-800">{{ $booking->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($booking->room)
                                        <span class="font-medium text-slate-800">{{ $booking->room->name }}</span>
                                        <span class="block text-xs text-slate-400 font-mono">{{ $booking->room->code }}</span>
                                    @else
                                        <span class="font-medium text-slate-800">Alat/Fasilitas</span>
                                        <span class="block text-xs text-slate-400 font-mono">Tanpa Ruangan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">{{ $booking->booking_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-slate-600 font-mono text-xs">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                                <td class="px-6 py-4 text-slate-500 truncate max-w-[180px]">{{ $booking->purpose }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php $c = ['pending'=>'amber','approved'=>'emerald','rejected'=>'rose','finished'=>'sky'][$booking->status] ?? 'slate'; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200">{{ $booking->status_label }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-navy-600 bg-navy-50 border border-navy-100 rounded-lg hover:bg-navy-100 transition-colors">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-8 py-16 text-center">
                                <div class="w-16 h-16 mx-auto rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <h5 class="text-slate-800 font-bold mb-1">Tidak ada data peminjaman</h5>
                                <p class="text-sm text-slate-500">Belum ada peminjaman yang diajukan.</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($bookings as $booking)
                    @php $c = ['pending'=>'amber','approved'=>'emerald','rejected'=>'rose','finished'=>'sky'][$booking->status] ?? 'slate'; @endphp
                    <a href="{{ route('admin.bookings.show', $booking) }}" class="block p-4 hover:bg-slate-50 transition-colors active:bg-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-500 to-navy-700 text-white flex items-center justify-center font-bold text-xs shrink-0">{{ strtoupper(substr($booking->user->name, 0, 1)) }}</div>
                                <div>
                                    @if($booking->room)
                                        <p class="text-xs text-slate-400">{{ $booking->room->name }} · <span class="font-mono">{{ $booking->room->code }}</span></p>
                                    @else
                                        <p class="text-xs text-slate-400">Peminjaman Fasilitas/Alat Luar</p>
                                    @endif
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200 shrink-0">{{ $booking->status_label }}</span>
                        </div>
                        <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-500 pl-10 mb-1">
                            <span>📅 {{ $booking->booking_date->format('d M Y') }}</span>
                            <span class="font-mono">🕐 {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-400 pl-10 line-clamp-1">{{ $booking->purpose }}</p>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">Belum ada peminjaman yang diajukan.</div>
                @endforelse
            </div>

            @if($bookings->hasPages())
                <div class="px-6 sm:px-8 py-4 border-t border-slate-100 bg-slate-50/50">{{ $bookings->links('pagination::tailwind') }}</div>
            @endif
        </div>
    </div>
    <style>
        @keyframes fade-in { 0% { opacity:0; transform:translateY(10px); } 100% { opacity:1; transform:translateY(0); } }
        . { animation: fade-in 0.5s cubic-bezier(0.16,1,0.3,1) forwards; }
        @keyframes pulse-slow { 0%,100% { opacity:0.2; } 50% { opacity:0.3; } }
        .animate-pulse-slow { animation: pulse-slow 8s ease-in-out infinite; }
    </style>
@endsection

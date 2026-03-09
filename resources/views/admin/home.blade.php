@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <!-- Welcome -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-navy-600 via-navy-500 to-navy-700 p-8 shadow-xl">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-gold-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-navy-300/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-navy-100 mt-2 max-w-xl">Panel pengelola Sistem Peminjaman Ruangan Universitas.</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach([
                ['label' => 'Total Ruangan', 'value' => $stats['total_rooms'], 'from' => 'from-navy-500', 'to' => 'to-navy-600', 'bg' => 'bg-navy-50', 'text' => 'text-navy-600', 'border' => 'border-navy-100', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['label' => 'Ruangan Tersedia', 'value' => $stats['rooms_available'], 'from' => 'from-success-500', 'to' => 'to-success-600', 'bg' => 'bg-success-50', 'text' => 'text-success-600', 'border' => 'border-success-100', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Pending Approval', 'value' => $stats['pending_bookings'], 'from' => 'from-gold-500', 'to' => 'to-gold-600', 'bg' => 'bg-gold-50', 'text' => 'text-gold-700', 'border' => 'border-gold-100', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label' => 'Booking Hari Ini', 'value' => $stats['approved_today'], 'from' => 'from-sky-500', 'to' => 'to-sky-600', 'bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'border' => 'border-sky-100', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ] as $stat)
                <div class="relative group rounded-2xl bg-white border border-slate-100 p-6 shadow-sm hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-black text-slate-800">{{ $stat['value'] }}</p>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mt-1">{{ $stat['label'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl {{ $stat['bg'] }} {{ $stat['border'] }} border flex items-center justify-center {{ $stat['text'] }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"></path></svg>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Today's Bookings -->
        @if($todayBookings->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
            <div class="px-6 sm:px-8 py-5 sm:py-6 border-b border-slate-100 bg-success-50/50">
                <h3 class="text-lg font-bold text-slate-800">🗓️ Booking Hari Ini</h3>
                <p class="text-sm text-slate-500">Ruangan yang sedang digunakan hari ini</p>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach($todayBookings as $booking)
                <div class="px-6 sm:px-8 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/80 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-success-100 border border-success-200 flex items-center justify-center text-success-600">
                            <i data-feather="clock" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">{{ $booking->room->name }}</p>
                            <p class="text-sm text-slate-500">{{ $booking->purpose }} — {{ $booking->user->name }}</p>
                        </div>
                    </div>
                    <span class="text-sm font-mono font-semibod text-slate-600 bg-slate-100 px-3 py-1 rounded-lg">
                        {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Recent Bookings -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
            <div class="px-6 sm:px-8 py-5 sm:py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Peminjaman Terbaru</h3>
                    <p class="text-sm text-slate-500 hidden sm:block">5 peminjaman terakhir yang masuk</p>
                </div>
                <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-navy-600 hover:text-navy-800 transition-colors">Lihat Semua →</a>
            </div>

            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left min-w-max">
                    <thead><tr class="text-xs text-slate-500 font-bold uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-4">Peminjam</th>
                        <th class="px-6 py-4">Ruangan</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr></thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($recentBookings as $booking)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-8 py-4 font-semibold text-slate-800">{{ $booking->user->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $booking->room->name }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $booking->booking_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-slate-600 font-mono">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php $c = ['pending'=>'gold','approved'=>'success','rejected'=>'danger','finished'=>'sky','cancelled'=>'slate'][$booking->status] ?? 'slate'; @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200">{{ $booking->status_label }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-8 py-12 text-center text-slate-400">Belum ada peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile -->
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($recentBookings as $booking)
                    @php $c = ['pending'=>'gold','approved'=>'success','rejected'=>'danger','finished'=>'sky','cancelled'=>'slate'][$booking->status] ?? 'slate'; @endphp
                    <div class="p-4 hover:bg-slate-50/80 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-500 to-navy-700 text-white flex items-center justify-center font-bold text-xs shrink-0">{{ strtoupper(substr($booking->user->name, 0, 1)) }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $booking->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $booking->room->name }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200">{{ $booking->status_label }}</span>
                        </div>
                        <p class="text-xs text-slate-500 pl-10">{{ $booking->booking_date->format('d M Y') }} • {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</p>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-sm">Belum ada peminjaman.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

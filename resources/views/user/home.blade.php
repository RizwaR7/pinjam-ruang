@extends('layouts.user')

@section('content')
    <div class="space-y-8">
        <!-- Welcome -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-cyan-600 via-teal-500 to-cyan-700 p-8 shadow-xl">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-teal-300/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Halo, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-cyan-100 mt-2">Sistem Peminjaman Ruangan Universitas</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Total', 'value' => $stats['total'], 'bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'clipboard'],
                ['label' => 'Menunggu', 'value' => $stats['pending'], 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'border' => 'border-amber-200', 'icon' => 'clock'],
                ['label' => 'Disetujui', 'value' => $stats['approved'], 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'border' => 'border-emerald-200', 'icon' => 'check-circle'],
                ['label' => 'Ditolak', 'value' => $stats['rejected'], 'bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'border' => 'border-rose-200', 'icon' => 'x-circle'],
            ] as $stat)
                <div class="rounded-2xl {{ $stat['bg'] }} border {{ $stat['border'] }} p-5 text-center">
                    <div class="flex justify-center mb-2"><i data-feather="{{ $stat['icon'] }}" class="w-6 h-6 {{ $stat['text'] }}"></i></div>
                    <p class="text-2xl font-black {{ $stat['text'] }}">{{ $stat['value'] }}</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Upcoming Bookings -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800">📅 Peminjaman Mendatang</h3>
                    <a href="{{ route('bookings.index') }}" class="text-sm font-semibold text-cyan-600 hover:text-cyan-800">Semua →</a>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($upcomingBookings as $booking)
                        <a href="{{ route('bookings.show', $booking) }}" class="block px-6 py-4 hover:bg-slate-50/80 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <p class="font-semibold text-slate-800 text-sm">{{ $booking->room->name }}</p>
                                @php $c = $booking->status === 'approved' ? 'emerald' : 'amber'; @endphp
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200">{{ $booking->status_label }}</span>
                            </div>
                            <p class="text-xs text-slate-500">{{ $booking->booking_date->format('d M Y') }} • {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</p>
                            <p class="text-xs text-slate-400 mt-1">{{ $booking->purpose }}</p>
                        </a>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><i data-feather="calendar" class="w-6 h-6 text-slate-400"></i></div>
                            <p class="text-sm text-slate-400">Belum ada peminjaman mendatang.</p>
                            <a href="{{ route('bookings.create') }}" class="inline-flex items-center mt-3 text-sm font-semibold text-cyan-600 hover:text-cyan-800">
                                <i data-feather="plus-circle" class="w-4 h-4 mr-1"></i> Ajukan Peminjaman
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-slate-800">🔔 Notifikasi</h3>
                        @if($unreadCount > 0)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500 text-white">{{ $unreadCount }}</span>
                        @endif
                    </div>
                    <a href="{{ route('notifications.index') }}" class="text-sm font-semibold text-cyan-600 hover:text-cyan-800">Semua →</a>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse($recentNotifications as $notif)
                        <div class="px-6 py-4 {{ $notif->is_read ? '' : 'bg-cyan-50/30' }} hover:bg-slate-50/80 transition-colors">
                            <div class="flex items-start gap-3">
                                @php
                                    $iconColor = ['success'=>'text-emerald-500','danger'=>'text-rose-500','warning'=>'text-amber-500','info'=>'text-sky-500'][$notif->type] ?? 'text-slate-500';
                                @endphp
                                <i data-feather="{{ $notif->icon ?? 'bell' }}" class="w-5 h-5 mt-0.5 {{ $iconColor }} shrink-0"></i>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800">{{ $notif->title }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3"><i data-feather="bell-off" class="w-6 h-6 text-slate-400"></i></div>
                            <p class="text-sm text-slate-400">Belum ada notifikasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('bookings.create') }}" class="group rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-600 p-6 text-white hover:shadow-xl hover:shadow-cyan-500/20 transition-all transform hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><i data-feather="plus-circle" class="w-5 h-5"></i></div>
                    <div>
                        <p class="font-bold">Ajukan Peminjaman</p>
                        <p class="text-xs text-cyan-100">Pinjam ruangan baru</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('calendar.index') }}" class="group rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 p-6 text-white hover:shadow-xl hover:shadow-violet-500/20 transition-all transform hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><i data-feather="calendar" class="w-5 h-5"></i></div>
                    <div>
                        <p class="font-bold">Lihat Jadwal</p>
                        <p class="text-xs text-violet-100">Cek ketersediaan ruangan</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('bookings.index') }}" class="group rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white hover:shadow-xl hover:shadow-amber-500/20 transition-all transform hover:-translate-y-0.5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center"><i data-feather="list" class="w-5 h-5"></i></div>
                    <div>
                        <p class="font-bold">Riwayat</p>
                        <p class="text-xs text-amber-100">Lihat semua peminjaman</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection

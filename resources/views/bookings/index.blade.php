@extends('layouts.user')

@section('content')
    <div class="space-y-8 ">
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 shadow-xl relative overflow-hidden">
            <div
                class="absolute -top-24 -right-24 w-64 h-64 bg-cyan-500/20 rounded-full mix-blend-screen filter blur-[80px]">
            </div>
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-1">Riwayat Peminjaman</h1>
                <p class="text-sm text-slate-500 font-medium">Daftar peminjaman ruangan Anda</p>
            </div>
            <div class="relative z-10">
                <a href="{{ route('bookings.create') }}"
                    class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-cyan-600 to-teal-600 rounded-xl hover:from-cyan-500 hover:to-teal-500 transition-all shadow-lg hover:shadow-cyan-500/30 transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ajukan Peminjaman
                </a>
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

        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
            <!-- Desktop Table -->
            <div class="overflow-x-auto hidden md:block">
                <table class="w-full text-left min-w-max">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-widest">
                            <th class="px-6 py-5 text-center">#</th>
                            <th class="px-6 py-5">Ruangan</th>
                            <th class="px-6 py-5">Tanggal</th>
                            <th class="px-6 py-5">Waktu</th>
                            <th class="px-6 py-5">Tujuan</th>
                            <th class="px-6 py-5 text-center">Status</th>
                            <th class="px-6 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($bookings as $i => $booking)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 text-center text-slate-400">{{ $bookings->firstItem() + $i }}</td>
                                <td class="px-6 py-4">
                                    @if($booking->room)
                                        <span class="font-semibold text-slate-800">{{ $booking->room->name }}</span>
                                        <span class="block text-xs text-slate-400 font-mono">{{ $booking->room->code }}</span>
                                    @else
                                        <span class="font-semibold text-slate-800">Peminjaman Fasilitas/Alat</span>
                                        <span class="block text-xs text-slate-400 font-mono">Tanpa Ruangan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">
                                    @if($booking->end_date && $booking->end_date->gt($booking->booking_date))
                                        {{ $booking->booking_date->format('d M') }} - {{ $booking->end_date->format('d M Y') }}
                                        <span class="block text-xs text-slate-400">({{ $booking->booking_date->diffInDays($booking->end_date) + 1 }} hari)</span>
                                    @else
                                        {{ $booking->booking_date->format('d M Y') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-600 font-mono text-xs">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 text-slate-500 truncate max-w-[200px]">{{ $booking->purpose }}</td>
                                <td class="px-6 py-4 text-center">
                                    @php $c = ['pending' => 'amber', 'approved' => 'emerald', 'rejected' => 'rose', 'finished' => 'sky'][$booking->status] ?? 'slate'; @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200">{{ $booking->status_label }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('bookings.show', $booking) }}"
                                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-cyan-600 bg-cyan-50 border border-cyan-100 rounded-lg hover:bg-cyan-100 transition-colors">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-16 text-center">
                                    <div
                                        class="w-16 h-16 mx-auto rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h5 class="text-slate-800 font-bold mb-1">Belum Ada Peminjaman</h5>
                                    <p class="text-sm text-slate-500 mb-4">Ajukan peminjaman pertama Anda sekarang!</p>
                                    <a href="{{ route('bookings.create') }}"
                                        class="inline-flex items-center px-4 py-2 text-sm font-semibold text-cyan-600 bg-cyan-50 rounded-xl hover:bg-cyan-100 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Ajukan Peminjaman
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden divide-y divide-slate-100">
                @forelse($bookings as $booking)
                    @php $c = ['pending' => 'amber', 'approved' => 'emerald', 'rejected' => 'rose', 'finished' => 'sky'][$booking->status] ?? 'slate'; @endphp
                    <a href="{{ route('bookings.show', $booking) }}"
                        class="block p-4 hover:bg-slate-50 transition-colors active:bg-slate-100">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                @if($booking->room)
                                    <p class="text-sm font-bold text-slate-800">{{ $booking->room->name }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $booking->room->code }}</p>
                                @else
                                    <p class="text-sm font-bold text-slate-800">Peminjaman Fasilitas/Alat</p>
                                    <p class="text-xs text-slate-400 font-mono">Tanpa Ruangan</p>
                                @endif
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200 shrink-0">{{ $booking->status_label }}</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 mb-1.5">
                            @if($booking->end_date && $booking->end_date->gt($booking->booking_date))
                                <span>📅 {{ $booking->booking_date->format('d M') }} - {{ $booking->end_date->format('d M Y') }} ({{ $booking->booking_date->diffInDays($booking->end_date) + 1 }} hari)</span>
                            @else
                                <span>📅 {{ $booking->booking_date->format('d M Y') }}</span>
                            @endif
                            <span class="font-mono">🕐
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                        </div>
                        <p class="text-xs text-slate-400 line-clamp-1">{{ $booking->purpose }}</p>
                    </a>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-slate-400 text-sm mb-3">Belum ada peminjaman.</p>
                        <a href="{{ route('bookings.create') }}" class="text-sm font-semibold text-cyan-600">Ajukan Peminjaman
                            →</a>
                    </div>
                @endforelse
            </div>

            @if($bookings->hasPages())
                <div class="px-6 sm:px-8 py-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $bookings->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>
    <style>
        @keyframes fade-in {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        . {
            animation: fade-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
@endsection
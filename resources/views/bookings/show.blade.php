@extends('layouts.user')

@section('content')
    <div class="space-y-6 ">
        <div class="flex items-center gap-4">
            <a href="{{ route('bookings.index') }}"
                class="flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-cyan-600 hover:border-cyan-200 hover:bg-cyan-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Detail Peminjaman</h1>
                <p class="text-sm text-slate-500 mt-1">Status dan detail peminjaman #{{ $booking->id }}</p>
            </div>
        </div>

        <div class="flex justify-center">
            <div class="w-full max-w-3xl">
                <div class="bg-white border border-slate-100 rounded-3xl shadow-xl shadow-slate-200/40 overflow-hidden">
                    <!-- Status Banner -->
                    @php $c = ['pending' => 'amber', 'approved' => 'emerald', 'rejected' => 'rose', 'finished' => 'sky'][$booking->status] ?? 'slate'; @endphp
                    <div class="px-8 py-5 bg-{{ $c }}-50 border-b border-{{ $c }}-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($booking->status === 'pending')
                                <svg class="w-6 h-6 text-{{ $c }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($booking->status === 'approved')
                                <svg class="w-6 h-6 text-{{ $c }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($booking->status === 'rejected')
                                <svg class="w-6 h-6 text-{{ $c }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-{{ $c }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                    </path>
                                </svg>
                            @endif
                            <div>
                                <h3 class="text-lg font-bold text-{{ $c }}-800">{{ $booking->status_label }}</h3>
                                <p class="text-xs text-{{ $c }}-600">
                                    @if($booking->status === 'pending') Menunggu persetujuan admin
                                    @elseif($booking->status === 'approved') Peminjaman Anda telah disetujui
                                    @elseif($booking->status === 'rejected') Peminjaman Anda ditolak
                                    @else Peminjaman telah selesai
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-white text-{{ $c }}-700 border border-{{ $c }}-200 shadow-sm">#{{ $booking->id }}</span>
                    </div>

                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Ruangan</label>
                                <p class="text-slate-800 font-semibold text-lg">{{ $booking->room->name }}</p>
                                <p class="text-xs font-mono text-slate-500">{{ $booking->room->code }} ·
                                    {{ ucfirst($booking->room->scope) }}{{ $booking->room->faculty ? ' · ' . $booking->room->faculty : '' }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Tanggal</label>
                                <p class="text-slate-800 font-semibold text-lg">
                                    {{ $booking->booking_date->format('l, d F Y') }}</p>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Waktu</label>
                                <p class="text-slate-800 font-semibold font-mono text-lg">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} –
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Lokasi</label>
                                <p class="text-slate-700 font-medium">{{ $booking->room->location ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Tujuan
                                Penggunaan</label>
                            <p class="text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                {{ $booking->purpose }}</p>
                        </div>

                        <!-- Additional Info -->
                        @if($booking->participant_count || $booking->contact_phone)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($booking->participant_count)
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Jumlah Peserta</label>
                                <p class="text-slate-700 font-medium">{{ $booking->participant_count }} orang</p>
                            </div>
                            @endif
                            @if($booking->contact_phone)
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">No. HP</label>
                                <p class="text-slate-700 font-medium">{{ $booking->contact_phone }}</p>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Equipment -->
                        @if($booking->equipment->isNotEmpty())
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Fasilitas Tambahan</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($booking->equipment as $eq)
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="w-8 h-8 rounded-lg bg-cyan-100 border border-cyan-200 flex items-center justify-center">
                                        <i data-feather="tool" class="w-4 h-4 text-cyan-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-700">{{ $eq->name }}</p>
                                        <p class="text-xs text-slate-400">Jumlah: {{ $eq->pivot->quantity }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($booking->notes)
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Catatan</label>
                                <p class="text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                    {{ $booking->notes }}</p>
                            </div>
                        @endif

                        <!-- Permit File -->
                        @if($booking->permit_file)
                        <div class="space-y-1">
                            <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Surat Izin</label>
                            <a href="{{ asset('storage/' . $booking->permit_file) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-cyan-600 hover:bg-cyan-50 hover:border-cyan-200 transition-colors">
                                <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Lihat Surat Izin
                            </a>
                        </div>
                        @endif

                        @if($booking->rejection_reason)
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-rose-500">Alasan
                                    Penolakan</label>
                                <p class="text-rose-700 bg-rose-50 p-4 rounded-xl border border-rose-200">
                                    {{ $booking->rejection_reason }}</p>
                            </div>
                        @endif

                        @if($booking->approver)
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Diproses Oleh</label>
                                <p class="text-slate-700 font-medium">{{ $booking->approver->name }} <span
                                        class="text-xs text-slate-400">· {{ $booking->approved_at->format('d M Y H:i') }}</span>
                                </p>
                            </div>
                        @endif

                        <div
                            class="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                            <span>Diajukan: {{ $booking->created_at->format('d M Y H:i') }}</span>
                            <a href="{{ route('bookings.index') }}"
                                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors shadow-sm">Kembali
                                ke Daftar</a>
                        </div>
                    </div>
                </div>
            </div>
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

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
                    @php $c = ['pending' => 'amber', 'approved' => 'emerald', 'rejected' => 'rose', 'finished' => 'sky', 'return_requested' => 'cyan'][$booking->status] ?? 'slate'; @endphp
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
                            @elseif($booking->status === 'return_requested')
                                <svg class="w-6 h-6 text-{{ $c }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
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
                                    @elseif($booking->status === 'return_requested') Menunggu konfirmasi pengembalian oleh admin
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
                                @if($booking->room)
                                    <p class="text-slate-800 font-semibold text-lg">{{ $booking->room->name }}</p>
                                    <p class="text-xs font-mono text-slate-500">{{ $booking->room->code }} ·
                                        {{ ucfirst($booking->room->scope) }}{{ $booking->room->faculty ? ' · ' . $booking->room->faculty : '' }}
                                    </p>
                                @else
                                    <p class="text-slate-800 font-semibold text-lg">Peminjaman Fasilitas/Alat</p>
                                    <p class="text-xs font-mono text-slate-500">Tanpa Ruangan</p>
                                @endif
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
                                <p class="text-slate-700 font-medium">{{ $booking->room ? ($booking->room->location ?? '-') : '-' }}</p>
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

                        {{-- Return Deadline --}}
                        @if($booking->return_deadline && in_array($booking->status, ['approved', 'return_requested', 'finished']))
                            <div class="p-4 rounded-xl border {{ $booking->isOverdue() ? 'bg-rose-50 border-rose-200' : 'bg-cyan-50 border-cyan-200' }}">
                                <div class="flex items-center gap-2 mb-1">
                                    <svg class="w-4 h-4 {{ $booking->isOverdue() ? 'text-rose-500' : 'text-cyan-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-sm font-bold {{ $booking->isOverdue() ? 'text-rose-700' : 'text-cyan-700' }}">Batas Pengembalian</span>
                                </div>
                                <p class="text-sm {{ $booking->isOverdue() ? 'text-rose-600' : 'text-cyan-600' }}">
                                    {{ $booking->return_deadline->format('l, d F Y H:i') }}
                                    @if($booking->isOverdue())
                                        <span class="font-bold text-rose-700 ml-1">(Terlambat {{ $booking->days_late }} hari)</span>
                                    @endif
                                </p>
                                @if($booking->returned_at)
                                    <p class="text-xs text-slate-500 mt-1">Dikembalikan: {{ $booking->returned_at->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        @endif

                        {{-- Return Request Button --}}
                        @if($booking->status === 'approved')
                            <div class="pt-4 border-t border-slate-100">
                                <form action="{{ route('bookings.request-return', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengajukan pengembalian?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-cyan-600 rounded-xl hover:from-cyan-400 hover:to-cyan-500 transition-all shadow-lg shadow-cyan-500/20">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Ajukan Pengembalian
                                    </button>
                                </form>
                            </div>
                        @endif

                        {{-- Fine Info --}}
                        @if($booking->fine_amount > 0)
                            <div class="p-5 rounded-xl bg-amber-50 border border-amber-200 space-y-3">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    <h4 class="font-bold text-amber-800">Denda Keterlambatan</h4>
                                </div>
                                <p class="text-2xl font-black text-amber-700">Rp {{ number_format($booking->fine_amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-amber-600">Terlambat {{ $booking->days_late }} hari × Rp {{ number_format(\App\Models\Setting::get('fine_per_day', 5000), 0, ',', '.') }}/hari</p>

                                @if($booking->fine_status === 'unpaid' && $vaInfo)
                                    <div class="mt-3 p-3 rounded-lg bg-white border border-amber-100 space-y-1">
                                        <p class="text-xs font-bold text-slate-600 uppercase">Info Pembayaran</p>
                                        @if($vaInfo['bank'])
                                            <p class="text-sm text-slate-700">Bank: <span class="font-bold">{{ $vaInfo['bank'] }}</span></p>
                                        @endif
                                        @if($vaInfo['number'])
                                            <p class="text-sm text-slate-700">No. Rek: <span class="font-mono font-bold">{{ $vaInfo['number'] }}</span></p>
                                        @endif
                                        @if($vaInfo['holder'])
                                            <p class="text-sm text-slate-700">A/N: <span class="font-bold">{{ $vaInfo['holder'] }}</span></p>
                                        @endif
                                    </div>

                                    {{-- Payment Upload Form --}}
                                    <form action="{{ route('bookings.pay-fine', $booking) }}" method="POST" enctype="multipart/form-data" class="mt-3 space-y-2">
                                        @csrf
                                        <label class="text-xs font-bold text-slate-600">Upload Bukti Pembayaran</label>
                                        <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" required
                                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                                        @error('proof_file')<p class="text-xs text-rose-500">{{ $message }}</p>@enderror
                                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-bold text-white bg-amber-500 rounded-xl hover:bg-amber-600 transition-colors shadow-sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                            Upload Bukti
                                        </button>
                                    </form>
                                @elseif($booking->fine_status === 'paid')
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 rounded-full">✅ Denda Telah Lunas</span>
                                @endif
                            </div>

                            {{-- Payment History --}}
                            @if($booking->finePayments->isNotEmpty())
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Riwayat Pembayaran</label>
                                    @foreach($booking->finePayments as $payment)
                                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-700">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                                                <p class="text-xs text-slate-400">{{ $payment->created_at->format('d M Y H:i') }}</p>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $payment->status_badge }}-100 text-{{ $payment->status_badge }}-700 border border-{{ $payment->status_badge }}-200">{{ $payment->status_label }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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

@extends('layouts.admin')

@section('content')
    <div class="space-y-6 ">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.bookings.index') }}" class="flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-navy-600 hover:border-navy-200 hover:bg-navy-50 transition-all shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Detail Peminjaman</h1>
                <nav class="flex mt-1"><ol class="inline-flex items-center space-x-2 text-xs text-slate-500 font-medium">
                    <li><a href="{{ route('admin.home') }}" class="hover:text-navy-600">Dashboard</a></li>
                    <li><span class="mx-1 text-slate-300">/</span></li>
                    <li><a href="{{ route('admin.bookings.index') }}" class="hover:text-navy-600">Peminjaman</a></li>
                    <li><span class="mx-1 text-slate-300">/</span></li>
                    <li class="text-navy-600 font-semibold">#{{ $booking->id }}</li>
                </ol></nav>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center shadow-sm" role="alert">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 flex items-center shadow-sm" role="alert">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Booking Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <h3 class="text-lg font-bold text-slate-800">Informasi Peminjaman</h3>
                        @php $c = ['pending'=>'amber','approved'=>'emerald','rejected'=>'rose','finished'=>'sky','return_requested'=>'cyan'][$booking->status] ?? 'slate'; @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-{{ $c }}-100 text-{{ $c }}-700 border border-{{ $c }}-200">{{ $booking->status_label }}</span>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Ruangan</label>
                                @if($booking->room)
                                    <p class="text-slate-800 font-semibold text-lg">{{ $booking->room->name }}</p>
                                    <p class="text-xs font-mono text-slate-500">{{ $booking->room->code }} · {{ ucfirst($booking->room->scope) }}{{ $booking->room->faculty ? ' · ' . $booking->room->faculty : '' }}</p>
                                @else
                                    <p class="text-slate-800 font-semibold text-lg">Peminjaman Fasilitas/Alat Luar</p>
                                    <p class="text-xs font-mono text-slate-500">Tanpa Ruangan</p>
                                @endif
                            </div>
                            <div class="space-y-1"><label class="text-xs font-bold uppercase tracking-widest text-slate-400">Peminjam</label><div class="flex items-center gap-3 mt-1"><div class="w-10 h-10 rounded-full bg-gradient-to-br from-navy-500 to-navy-500 text-white flex items-center justify-center font-bold text-sm">{{ strtoupper(substr($booking->user->name, 0, 1)) }}</div><div><p class="text-slate-800 font-semibold">{{ $booking->user->name }}</p><p class="text-xs text-slate-500">{{ $booking->user->email }}{{ $booking->user->role ? ' · ' . $booking->user->role->name : '' }}</p></div></div></div>
                            @php
                                $startDate = $booking->booking_date;
                                $endDate   = $booking->end_date ?? $startDate;
                                $days      = $startDate->diffInDays($endDate) + 1;
                                $isMulti   = $days > 1;
                            @endphp
                            <div class="space-y-1 {{ $isMulti ? 'md:col-span-2' : '' }}">
                                <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Tanggal</label>
                                @if($isMulti)
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mt-1">
                                        <div class="flex items-center gap-3">
                                            <div class="text-center">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Mulai</p>
                                                <p class="text-slate-800 font-semibold">{{ $startDate->format('D, d M Y') }}</p>
                                            </div>
                                            <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                            <div class="text-center">
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Selesai</p>
                                                <p class="text-slate-800 font-semibold">{{ $endDate->format('D, d M Y') }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold shrink-0">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            {{ $days }} Hari
                                        </span>
                                    </div>
                                @else
                                    <p class="text-slate-800 font-semibold">{{ $startDate->format('l, d F Y') }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">1 hari</p>
                                @endif
                            </div>
                            <div class="space-y-1"><label class="text-xs font-bold uppercase tracking-widest text-slate-400">Waktu</label><p class="text-slate-800 font-semibold font-mono">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p></div>
                            <div class="space-y-1 md:col-span-2"><label class="text-xs font-bold uppercase tracking-widest text-slate-400">Tujuan Penggunaan</label><p class="text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100">{{ $booking->purpose }}</p></div>
                            @if($booking->participant_count || $booking->contact_phone)
                                <div class="space-y-1"><label class="text-xs font-bold uppercase tracking-widest text-slate-400">Jumlah Peserta</label><p class="text-slate-800 font-medium">{{ $booking->participant_count ?? '-' }} orang</p></div>
                                <div class="space-y-1"><label class="text-xs font-bold uppercase tracking-widest text-slate-400">No. HP</label><p class="text-slate-800 font-medium">{{ $booking->contact_phone ?? '-' }}</p></div>
                            @endif
                            @if($booking->equipment->isNotEmpty())
                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Fasilitas Tambahan</label>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($booking->equipment as $eq)
                                            <span class="inline-flex items-center px-3 py-1.5 bg-cyan-50 text-cyan-700 border border-cyan-200 rounded-lg text-xs font-semibold">
                                                <i data-feather="tool" class="w-3 h-3 mr-1.5"></i>{{ $eq->name }} (×{{ $eq->pivot->quantity }})
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            @if($booking->notes)
                                <div class="space-y-1 md:col-span-2"><label class="text-xs font-bold uppercase tracking-widest text-slate-400">Catatan</label><p class="text-slate-700 bg-slate-50 p-4 rounded-xl border border-slate-100">{{ $booking->notes }}</p></div>
                            @endif
                            @if($booking->permit_file)
                                <div class="space-y-1 md:col-span-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Surat Izin</label>
                                    <a href="{{ asset('storage/' . $booking->permit_file) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-cyan-600 hover:bg-cyan-50 hover:border-cyan-200 transition-colors">
                                        <i data-feather="file-text" class="w-4 h-4 mr-2"></i> Lihat Surat Izin
                                    </a>
                                </div>
                            @endif
                            @if($booking->rejection_reason)
                                <div class="space-y-1 md:col-span-2"><label class="text-xs font-bold uppercase tracking-widest text-rose-400">Alasan Penolakan</label><p class="text-rose-700 bg-rose-50 p-4 rounded-xl border border-rose-200">{{ $booking->rejection_reason }}</p></div>
                            @endif
                            @if($booking->approver)
                                <div class="space-y-1 md:col-span-2"><label class="text-xs font-bold uppercase tracking-widest text-slate-400">Diproses Oleh</label><p class="text-slate-700 font-medium">{{ $booking->approver->name }} <span class="text-xs text-slate-400 font-normal">· {{ $booking->approved_at->format('d M Y H:i') }}</span></p></div>
                            @endif
                            {{-- Return Deadline --}}
                            @if($booking->return_deadline)
                                <div class="md:col-span-2 p-4 rounded-xl border {{ $booking->isOverdue() ? 'bg-rose-50 border-rose-200' : 'bg-cyan-50 border-cyan-200' }}">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-4 h-4 {{ $booking->isOverdue() ? 'text-rose-500' : 'text-cyan-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-sm font-bold {{ $booking->isOverdue() ? 'text-rose-700' : 'text-cyan-700' }}">Batas Pengembalian: {{ $booking->return_deadline->format('d M Y H:i') }}</span>
                                        @if($booking->isOverdue())
                                            <span class="ml-2 px-2 py-0.5 text-xs font-bold text-rose-700 bg-rose-100 rounded-full">Terlambat {{ $booking->days_late }} hari</span>
                                        @endif
                                    </div>
                                    @if($booking->returned_at)
                                        <p class="text-xs text-slate-500 mt-1">Dikembalikan: {{ $booking->returned_at->format('d M Y H:i') }}</p>
                                    @endif
                                </div>
                            @endif
                            {{-- Fine Info --}}
                            @if($booking->fine_amount > 0)
                                <div class="md:col-span-2 p-4 rounded-xl bg-amber-50 border border-amber-200">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-bold text-amber-800">Denda Keterlambatan</h4>
                                            <p class="text-xl font-black text-amber-700">Rp {{ number_format($booking->fine_amount, 0, ',', '.') }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $booking->fine_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $booking->fine_status === 'paid' ? '✅ Lunas' : '⏳ Belum Lunas' }}</span>
                                    </div>
                                </div>
                            @endif
                            {{-- Fine Payments --}}
                            @if($booking->finePayments->isNotEmpty())
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-slate-400">Bukti Pembayaran Denda</label>
                                    @foreach($booking->finePayments as $fp)
                                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ asset('storage/' . $fp->proof_file) }}" target="_blank" class="text-cyan-600 hover:underline text-sm font-semibold">📎 Lihat Bukti</a>
                                                <span class="text-xs text-slate-400">{{ $fp->created_at->format('d M Y H:i') }}</span>
                                            </div>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $fp->status_badge }}-100 text-{{ $fp->status_badge }}-700">{{ $fp->status_label }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Panel -->
            <div class="space-y-6">
                @if($booking->status === 'pending')
                    <!-- Approve -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-emerald-50/30">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Setujui
                            </h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-slate-600 mb-4">Setujui peminjaman ruangan ini. Jadwal akan otomatis terkonfirmasi.</p>
                            <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl hover:from-emerald-400 hover:to-teal-500 transition-all shadow-lg shadow-emerald-500/20 transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Approve Peminjaman
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Reject -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-rose-50/30">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Tolak
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('admin.bookings.reject', $booking) }}" method="POST" class="space-y-4">
                                @csrf @method('PATCH')
                                <div class="space-y-2">
                                    <label for="rejection_reason" class="text-sm font-bold text-slate-700">Alasan Penolakan <span class="text-rose-500">*</span></label>
                                    <textarea id="rejection_reason" name="rejection_reason" rows="3" class="block w-full px-4 py-3 bg-slate-50 border @error('rejection_reason') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:bg-white focus:outline-none" placeholder="Jelaskan alasan penolakan...">{{ old('rejection_reason') }}</textarea>
                                    @error('rejection_reason')<p class="text-sm text-rose-500">{{ $message }}</p>@enderror
                                </div>
                                <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white bg-rose-500 rounded-xl hover:bg-rose-600 transition-all shadow-sm">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Reject Peminjaman
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($booking->status === 'return_requested')
                    {{-- Confirm Return Panel --}}
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
                        <div class="p-6 border-b border-slate-100 bg-cyan-50/30">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Konfirmasi Pengembalian
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <p class="text-sm text-slate-600">
                                Peminjam mengajukan pengembalian pada <strong>{{ $booking->return_requested_at ? $booking->return_requested_at->format('d M Y H:i') : '-' }}</strong>.
                                @if($booking->isOverdue())
                                    <br><span class="text-rose-600 font-bold">⚠️ Terlambat {{ $booking->days_late }} hari — denda Rp {{ number_format($booking->calculateFine(), 0, ',', '.') }}</span>
                                @endif
                            </p>
                            <form action="{{ route('admin.bookings.confirm-return', $booking) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian ini?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-3 text-sm font-bold text-white bg-gradient-to-r from-cyan-500 to-teal-600 rounded-xl hover:from-cyan-400 hover:to-teal-500 transition-all shadow-lg shadow-cyan-500/20">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Konfirmasi Pengembalian
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-8 text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-{{ $c }}-50 border border-{{ $c }}-100 flex items-center justify-center text-{{ $c }}-500 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-2">Peminjaman {{ $booking->status_label }}</h3>
                        <p class="text-sm text-slate-500">Peminjaman ini telah diproses dan tidak dapat diubah lagi.</p>
                    </div>
                @endif

                <!-- Room Quick Info -->
                @if($booking->room)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Info Ruangan</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between"><span class="text-sm text-slate-500">Kapasitas</span><span class="text-sm font-bold text-slate-800">{{ $booking->room->capacity }} orang</span></div>
                        <div class="flex justify-between"><span class="text-sm text-slate-500">Lokasi</span><span class="text-sm font-bold text-slate-800">{{ $booking->room->location ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-sm text-slate-500">Scope</span><span class="text-sm font-bold text-slate-800">{{ ucfirst($booking->room->scope) }}</span></div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    <style>@keyframes fade-in { 0% { opacity:0; transform:translateY(10px); } 100% { opacity:1; transform:translateY(0); } } . { animation: fade-in 0.5s cubic-bezier(0.16,1,0.3,1) forwards; }</style>
@endsection

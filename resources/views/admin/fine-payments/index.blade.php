@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-1">💳 Verifikasi Pembayaran Denda</h1>
                <p class="text-sm text-slate-500 font-medium">Kelola bukti pembayaran denda keterlambatan</p>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4">
            @foreach([
                ['label' => 'Menunggu', 'count' => $stats['pending'], 'color' => 'amber'],
                ['label' => 'Terverifikasi', 'count' => $stats['verified'], 'color' => 'emerald'],
                ['label' => 'Ditolak', 'count' => $stats['rejected'], 'color' => 'rose'],
            ] as $item)
                <div class="rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-{{ $item['color'] }}-50 border border-{{ $item['color'] }}-100 flex items-center justify-center text-{{ $item['color'] }}-500">
                            <span class="text-lg font-black">{{ $item['count'] }}</span>
                        </div>
                        <p class="text-sm font-semibold text-slate-600">{{ $item['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left min-w-max">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-bold uppercase tracking-widest">
                            <th class="px-6 py-5">#</th>
                            <th class="px-6 py-5">Peminjam</th>
                            <th class="px-6 py-5">Booking</th>
                            <th class="px-6 py-5">Jumlah</th>
                            <th class="px-6 py-5">Bukti</th>
                            <th class="px-6 py-5 text-center">Status</th>
                            <th class="px-6 py-5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($payments as $i => $payment)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 text-slate-400">{{ $payments->firstItem() + $i }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800">{{ $payment->booking->user->name ?? '-' }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.bookings.show', $payment->booking_id) }}" class="text-cyan-600 hover:underline font-mono text-xs">#{{ $payment->booking_id }}</a>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ asset('storage/' . $payment->proof_file) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-cyan-600 bg-cyan-50 border border-cyan-100 rounded-lg hover:bg-cyan-100 transition-colors">
                                        <i data-feather="file-text" class="w-3 h-3 mr-1"></i> Lihat
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $payment->status_badge }}-100 text-{{ $payment->status_badge }}-700 border border-{{ $payment->status_badge }}-200">{{ $payment->status_label }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($payment->status === 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <form action="{{ route('admin.fine-payments.verify', $payment) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-emerald-500 rounded-lg hover:bg-emerald-600 transition-colors" onclick="return confirm('Verifikasi pembayaran ini?')">
                                                    <i data-feather="check" class="w-3 h-3 mr-1"></i> Verifikasi
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.fine-payments.reject', $payment) }}" method="POST" onsubmit="return prompt('Alasan penolakan:') ? (this.querySelector('[name=notes]').value = prompt('Alasan penolakan:'), true) : false">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="notes" value="">
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white bg-rose-500 rounded-lg hover:bg-rose-600 transition-colors">
                                                    <i data-feather="x" class="w-3 h-3 mr-1"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">
                                            {{ $payment->verifier->name ?? '-' }}<br>{{ $payment->verified_at?->format('d M Y H:i') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-8 py-16 text-center text-slate-400">Belum ada pembayaran denda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">{{ $payments->links('pagination::tailwind') }}</div>
            @endif
        </div>
    </div>
@endsection

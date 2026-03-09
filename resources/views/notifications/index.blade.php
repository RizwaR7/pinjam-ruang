@extends('layouts.user')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">🔔 Notifikasi</h1>
                <p class="text-sm text-slate-500 mt-1">Pemberitahuan terkait peminjaman ruangan</p>
            </div>
            @if($notifications->where('is_read', false)->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 text-sm font-semibold text-cyan-700 bg-cyan-50 border border-cyan-200 rounded-xl hover:bg-cyan-100 transition-colors">
                        <i data-feather="check" class="w-4 h-4 mr-1.5"></i> Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center">
                <i data-feather="check-circle" class="w-5 h-5 mr-3"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-slate-100 shadow-lg overflow-hidden">
            <div class="divide-y divide-slate-50">
                @forelse($notifications as $notif)
                    <div
                        class="px-6 py-4 flex items-start gap-4 {{ $notif->is_read ? '' : 'bg-cyan-50/30 border-l-4 border-l-cyan-400' }} hover:bg-slate-50/80 transition-colors">
                        @php
                            $iconColor = ['success' => 'text-emerald-500 bg-emerald-50', 'danger' => 'text-rose-500 bg-rose-50', 'warning' => 'text-amber-500 bg-amber-50', 'info' => 'text-sky-500 bg-sky-50'][$notif->type] ?? 'text-slate-500 bg-slate-50';
                        @endphp
                        <div class="w-10 h-10 rounded-xl {{ $iconColor }} flex items-center justify-center shrink-0">
                            <i data-feather="{{ $notif->icon ?? 'bell' }}" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $notif->title }}</p>
                                    <p class="text-sm text-slate-600 mt-1">{{ $notif->message }}</p>
                                </div>
                                @if(!$notif->is_read)
                                    <form action="{{ route('notifications.read', $notif) }}" method="POST" class="shrink-0">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-cyan-600 hover:text-cyan-800 font-medium">Tandai
                                            dibaca</button>
                                    </form>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-16 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                            <i data-feather="bell-off" class="w-8 h-8 text-slate-400"></i>
                        </div>
                        <p class="text-slate-500 font-medium">Belum ada notifikasi</p>
                        <p class="text-sm text-slate-400 mt-1">Notifikasi akan muncul saat ada update peminjaman</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="flex justify-center">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
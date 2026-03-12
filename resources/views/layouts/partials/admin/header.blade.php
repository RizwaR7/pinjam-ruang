<!-- Top Header -->
<header
    class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-slate-200/60 h-16 flex items-center justify-between px-6">
    <!-- Left: Mobile menu -->
    <div class="flex items-center gap-4">
        <button @click.stop="mobileMenu = !mobileMenu"
            class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-navy-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Right: User menu -->
    <div class="flex items-center gap-3">
        <!-- Notifications -->
        <div class="relative" x-data="{ 
            open: false, 
            count: 0,
            fetchCount() {
                fetch('{{ route('api.notifications.unread-count') }}')
                    .then(r => r.json())
                    .then(data => this.count = data.count);
            }
        }" x-init="fetchCount(); setInterval(() => fetchCount(), 30000)">
            <button @click="open = !open" 
                class="p-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-navy-600 transition-colors relative">
                <i data-feather="bell" class="w-5 h-5"></i>
                <span x-show="count > 0" x-text="count" 
                    class="absolute top-1.5 right-1.5 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white"></span>
            </button>

            <!-- Notification Dropdown -->
            <div x-show="open" @click.outside="open = false" x-transition
                class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 py-2 z-50">
                <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-widest">Notifikasi</h3>
                    <a href="{{ route('notifications.index') }}" class="text-[10px] font-bold text-navy-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="max-h-64 overflow-y-auto" id="notification-items">
                    <div x-show="count === 0" class="px-4 py-8 text-center text-xs text-slate-400">
                        Tidak ada notikasi baru
                    </div>
                </div>
            </div>
        </div>

        <div class="w-px h-6 bg-slate-200 mx-1"></div>

        <div class="flex items-center gap-3" x-data="{ open: false }">
        <button @click.stop="open = !open"
            class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-100 transition-colors">
            <div
                class="w-8 h-8 rounded-full bg-gradient-to-br from-navy-500 to-navy-700 text-white flex items-center justify-center font-bold text-xs overflow-hidden">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                @endif
            </div>
            <div class="hidden sm:block text-left">
                <p class="text-sm font-semibold text-slate-700 leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-slate-400">{{ Auth::user()->role?->name ?? 'User' }}</p>
            </div>
            <svg class="w-4 h-4 text-slate-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown -->
        <div x-show="open" @click.outside="open = false" x-transition
            class="absolute right-6 top-14 w-56 bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 py-2 z-50">
            <div class="px-4 py-3 border-b border-slate-100">
                <p class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
            </div>

            <div class="py-1">
                <a href="{{ route('admin.profile.edit') }}"
                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-navy-600 transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil Saya
                </a>
                <a href="{{ route('admin.home') }}"
                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-navy-600 transition-colors">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard Utama
                </a>
            </div>

            <div class="border-t border-slate-100 py-1">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout Sistem
                </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </div>
</header>
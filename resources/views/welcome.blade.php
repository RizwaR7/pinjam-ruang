<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SIPERA') }} — Booking Ruangan Universitas</title>
    @vite(['resources/css/app.css'])
    
    <!-- AlpineJS & Feather Icons -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- AlpineJS & Feather Icons -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>

    <!-- Dark Mode Script -->
    <script>
        // Init logic before Alpine loads to prevent flicker
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .page-fade-out { opacity: 0; transition: opacity 0.25s ease-out; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 font-sans antialiased overflow-x-hidden min-h-screen flex flex-col transition-colors duration-300" 
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
          toggleTheme() {
              this.darkMode = !this.darkMode;
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
                  localStorage.setItem('theme', 'dark');
              } else {
                  document.documentElement.classList.remove('dark');
                  localStorage.setItem('theme', 'light');
              }
          }
      }">

    <!-- Navbar -->
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-lg border-b border-slate-200/60 dark:border-slate-800/60 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-blue-600 dark:bg-blue-500 text-white font-bold shadow-md shadow-blue-500/20 transition-colors duration-300">
                    <i data-feather="calendar" class="w-5 h-5"></i>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-800 dark:text-white transition-colors duration-300">SIPERA</span>
            </a>
            
            <div class="flex items-center gap-4">
                <!-- Theme Toggle Button -->
                <button @click="toggleTheme()" class="p-2 mr-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors" title="Toggle Dark/Light Mode">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg x-show="darkMode" x-cloak class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>
                @auth
                    <a href="{{ in_array(auth()->user()->role?->slug, ['pengelola_sistem', 'pengelola_gedung']) ? route('admin.home') : route('home') }}"
                       class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 dark:bg-blue-500 rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                        Dashboard Saya
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors hidden sm:block">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-blue-600 dark:bg-blue-500 rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 transition-all shadow-md shadow-blue-500/20">
                            Daftar Peminjam
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden bg-white dark:bg-slate-900 transition-colors duration-300">
        <!-- Background decoration -->
        <div class="absolute inset-x-0 top-0 h-[600px] bg-gradient-to-b from-blue-50/50 dark:from-blue-900/20 to-white/0 dark:to-slate-900/0 pointer-events-none"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-400/10 dark:bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-20 -left-20 w-72 h-72 bg-indigo-500/10 dark:bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 text-center">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800/50 text-sm text-blue-700 dark:text-blue-400 font-semibold mb-6">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                Sistem Peminjaman Ruangan Universitas
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight leading-tight mb-6 text-slate-900 dark:text-white">
                Temukan & Pesan Ruangan<br class="hidden md:block"/>
                <span class="text-blue-600 dark:text-blue-400">Terbaik Untukmu</span>
            </h1>
            <p class="text-lg md:text-xl text-slate-500 dark:text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                Platform digital resmi untuk mencari jadwal, ketersediaan alat, dan meminjam ruangan di lingkungan kampus dengan cepat dan transparan.
            </p>

            <!-- Search Bar -->
            <form action="{{ route('welcome') }}" method="GET" class="max-w-3xl mx-auto bg-white dark:bg-slate-800 p-2 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row gap-2 transition-colors duration-300">
                <div class="flex-1 flex items-center px-4 py-2 border-r border-slate-100 dark:border-slate-700">
                    <i data-feather="search" class="text-slate-400 w-5 h-5 mr-3"></i>
                    <input type="text" name="building" value="{{ request('building') }}" placeholder="Cari nama gedung..." class="w-full bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 placeholder-slate-400 py-2 outline-none">
                </div>
                <div class="flex-1 flex items-center px-4 py-2 sm:border-r border-slate-100 dark:border-slate-700">
                    <i data-feather="map-pin" class="text-slate-400 w-5 h-5 mr-3"></i>
                    <select name="scope" class="w-full bg-transparent border-none focus:ring-0 text-slate-700 dark:text-slate-200 outline-none appearance-none font-medium cursor-pointer py-2">
                        <option value="" class="dark:bg-slate-800">Semua Lingkup</option>
                        <option value="universitas" class="dark:bg-slate-800" {{ request('scope') === 'universitas' ? 'selected' : '' }}>Universitas</option>
                        <option value="fakultas" class="dark:bg-slate-800" {{ request('scope') === 'fakultas' ? 'selected' : '' }}>Fakultas</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-bold py-3 px-8 rounded-xl transition-colors shrink-0">
                    Cari Ruangan
                </button>
            </form>
        </div>
    </section>

    <!-- Marketplace / Catalog Section -->
    <main class="flex-1 max-w-7xl mx-auto px-6 py-12 w-full">
        
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">
                @if(request('building') || request('scope'))
                    Hasil Pencarian Ruangan
                @else
                    Katalog Ruangan Terpopuler
                @endif
            </h2>
            <span class="text-sm font-medium text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 rounded-full shadow-sm">{{ $rooms->total() }} Ruangan ditemukan</span>
        </div>

        @if($rooms->isEmpty())
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 p-12 text-center max-w-2xl mx-auto shadow-sm transition-colors duration-300">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-feather="search" class="w-10 h-10 text-slate-300 dark:text-slate-500"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Tidak ada ruangan ditemukan</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-6">Ruangan dengan kriteria pencarian Anda tidak tersedia. Silakan coba mengubah filter gedung atau lingkup.</p>
                <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800/50 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    Reset Pencarian
                </a>
            </div>
        @else
            <!-- Grid of Rooms -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($rooms as $room)
                    <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-none hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                        
                        <!-- Room Header (Like an image placeholder wrapper) -->
                        <div class="h-32 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700 p-6 flex flex-col justify-between relative overflow-hidden">
                            <!-- Abstract decoration -->
                            <div class="absolute -right-4 -top-8 w-24 h-24 bg-blue-100 dark:bg-blue-500/20 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500 ease-out"></div>
                            
                            <div class="flex justify-between items-start relative z-10">
                                <span class="px-2.5 py-1 text-[10px] font-bold tracking-wider uppercase rounded-lg border 
                                    {{ $room->scope === 'universitas' ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800/50' : 'bg-cyan-50 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 border-cyan-200 dark:border-cyan-800/50' }}">
                                    {{ $room->scope }}
                                </span>
                                
                                @if($room->status === 'tersedia')
                                    <span class="flex items-center gap-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-1 rounded-lg border border-emerald-200 dark:border-emerald-800/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Tersedia
                                    </span>
                                @elseif($room->status === 'dipakai')
                                    <span class="flex items-center gap-1.5 text-xs font-semibold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2 py-1 rounded-lg border border-amber-200 dark:border-amber-800/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Dipakai
                                    </span>
                                @else
                                    <span class="flex items-center gap-1.5 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/30 px-2 py-1 rounded-lg border border-rose-200 dark:border-rose-800/50">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Maintenance
                                    </span>
                                @endif
                            </div>
                            
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white line-clamp-1 relative z-10" title="{{ $room->name }}">{{ $room->name }}</h3>
                        </div>

                        <!-- Content Body -->
                        <div class="p-6 flex-1 flex flex-col">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Gedung</p>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 line-clamp-1" title="{{ $room->building }}">{{ $room->building ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Lantai / Kode</p>
                                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $room->floor ?? '-' }} <span class="text-slate-400 dark:text-slate-600 px-1">•</span> <span class="font-mono text-xs">{{ $room->code }}</span></p>
                                </div>
                            </div>
                            
                            <div class="mb-5 flex-1">
                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Fasilitas Utama</p>
                                <p class="text-sm text-slate-600 dark:text-slate-400 line-clamp-2">{{ $room->facilities ?: 'Tidak ada informasi fasilitas.' }}</p>
                            </div>

                            <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700/50 pt-4 mt-auto">
                                <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                    <i data-feather="users" class="w-4 h-4"></i>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $room->capacity }} <span class="font-normal text-slate-500 dark:text-slate-500 text-xs">kursi</span></span>
                                </div>
                                <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                    Pesan <i data-feather="chevron-right" class="w-4 h-4 ml-0.5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $rooms->links('pagination::tailwind') }}
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 mt-auto transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8 border-b border-slate-800 pb-8">
                <div class="md:col-span-2">
                    <a href="/" class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-600 dark:bg-blue-500 text-white font-bold transition-colors duration-300">
                            <i data-feather="calendar" class="w-4 h-4"></i>
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-white">SIPERA</span>
                    </a>
                    <p class="text-slate-400 text-sm max-w-sm mb-6 leading-relaxed">
                        Sistem Informasi Peminjaman Ruangan Universitas. Platform digital cerdas untuk mendukung efisiensi akademik dan kegiatan mahasiswa.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-blue-400 dark:hover:text-blue-300 transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-blue-400 dark:hover:text-blue-300 transition-colors">Panduan Penggunaan</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-blue-400 dark:hover:text-blue-300 transition-colors">Daftar Akun</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-gold-400 dark:hover:text-gold-300 transition-colors mt-2 text-xs opacity-75 inline-block">Portal Admin</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-bold mb-4">Bantuan</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-blue-400 dark:hover:text-blue-300 transition-colors">FAQ</a></li>
                        <li><a href="#" class="hover:text-blue-400 dark:hover:text-blue-300 transition-colors">Hubungi Admin</a></li>
                        <li><a href="#" class="hover:text-blue-400 dark:hover:text-blue-300 transition-colors">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Universitas. All rights reserved.</p>
                <div class="flex items-center gap-1 font-medium">
                    Dibuat dengan <i data-feather="heart" class="w-3 h-3 text-rose-500 dark:text-rose-400 mx-1"></i> untuk Universitas
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            feather.replace();
            
            // Simple page transitions
            const links = document.querySelectorAll('a[href]:not([target="_blank"]):not([href^="#"]):not([href^="javascript:"])');
            links.forEach(link => {
                link.addEventListener('click', e => {
                    if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
                    if (link.origin !== window.location.origin) return;
                    e.preventDefault();
                    document.body.classList.add('page-fade-out');
                    setTimeout(() => { window.location.href = link.href; }, 200);
                });
            });
        });
        window.addEventListener('pageshow', (e) => {
            if (e.persisted) document.body.classList.remove('page-fade-out');
        });
    </script>
</body>
</html>

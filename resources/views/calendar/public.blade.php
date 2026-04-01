<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jadwal Ruangan — {{ config('app.name', 'SIRUANG') }}</title>
    @vite(['resources/css/app.css'])
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen">
    <!-- Navbar -->
    <nav class="fixed top-0 inset-x-0 z-50 bg-white/90 backdrop-blur-lg border-b border-slate-200/60 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('welcome') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-blue-600 text-white font-bold shadow-md shadow-blue-500/20">
                    <i data-feather="calendar" class="w-5 h-5"></i>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-800">SIRUANG</span>
            </a>
            
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ in_array(auth()->user()->role?->slug, ['pengelola_sistem', 'pengelola_gedung']) ? route('admin.home') : route('home') }}"
                       class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-500/20 flex items-center gap-2">
                        Dashboard
                        <i data-feather="arrow-right" class="w-4 h-4"></i>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-500/20">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="pt-20 pb-8 px-4 sm:px-6 max-w-7xl mx-auto" x-data="publicCalendar()">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                        <span class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white">
                            <i data-feather="calendar" class="w-6 h-6"></i>
                        </span>
                        Jadwal Penggunaan Ruangan
                    </h1>
                    <p class="text-slate-500 mt-2 text-sm sm:text-base">Lihat jadwal pemakaian ruangan kampus secara real-time tanpa perlu login</p>
                </div>
                
                <!-- Month Navigation -->
                <div class="flex items-center gap-2 bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm">
                    <button @click="prevMonth()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-all">
                        <i data-feather="chevron-left" class="w-5 h-5"></i>
                    </button>
                    <div class="px-4 text-center min-w-[160px]">
                        <span x-text="monthYearTitle" class="text-sm font-bold text-slate-700"></span>
                    </div>
                    <button @click="nextMonth()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-all">
                        <i data-feather="chevron-right" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i data-feather="home" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800" x-text="stats.totalRooms">0</p>
                        <p class="text-xs font-medium text-slate-500">Total Ruangan</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <i data-feather="check-circle" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800" x-text="stats.availableToday">0</p>
                        <p class="text-xs font-medium text-slate-500">Tersedia Hari Ini</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                        <i data-feather="clock" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800" x-text="stats.inUseToday">0</p>
                        <p class="text-xs font-medium text-slate-500">Sedang Dipakai</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                        <i data-feather="calendar" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-black text-slate-800" x-text="stats.bookingsThisMonth">0</p>
                        <p class="text-xs font-medium text-slate-500">Jadwal Bulan Ini</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Filter -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                    <h3 class="flex items-center gap-2 text-xs font-black text-slate-500 uppercase tracking-widest mb-3">
                        <i data-feather="filter" class="w-4 h-4"></i> Filter
                    </h3>
                    <div class="space-y-3">
                        <select x-model="filterBuilding" @change="filterRoomsByBuilding()"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:border-blue-500 outline-none">
                            <option value="">Semua Gedung</option>
                            @foreach($buildings as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                        <select x-model="filterRoom"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:border-blue-500 outline-none">
                            <option value="">Semua Ruangan</option>
                            <template x-for="room in filteredRooms" :key="room.id">
                                <option :value="room.id" x-text="room.name"></option>
                            </template>
                        </select>
                        <button @click="loadEvents()"
                            class="w-full py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all flex items-center justify-center gap-2">
                            <i data-feather="search" class="w-4 h-4"></i>
                            Terapkan Filter
                        </button>
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                    <h3 class="flex items-center gap-2 text-xs font-black text-slate-500 uppercase tracking-widest mb-3">
                        <i data-feather="clock" class="w-4 h-4"></i> Hari Ini
                    </h3>
                    <div class="space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
                        <template x-if="todayEvents.length === 0">
                            <div class="py-6 text-center">
                                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mx-auto mb-2">
                                    <i data-feather="check" class="w-6 h-6 text-emerald-600"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-500">Tidak ada jadwal hari ini</p>
                            </div>
                        </template>
                        <template x-for="ev in todayEvents" :key="ev.id">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-blue-50 hover:border-blue-200 transition-all">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-slate-800 truncate" x-text="ev.room"></p>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="ev.start_time + ' - ' + ev.end_time"></p>
                                    </div>
                                    <span class="shrink-0 text-[10px] font-bold px-2 py-1 rounded-lg"
                                        :class="ev.status === 'in_use' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'"
                                        x-text="ev.status === 'in_use' ? 'Sedang Dipakai' : 'Terjadwal'"></span>
                                </div>
                                <p class="text-xs text-slate-600 mt-2 truncate" x-text="ev.title"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Legend -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                    <h3 class="flex items-center gap-2 text-xs font-black text-slate-500 uppercase tracking-widest mb-3">
                        <i data-feather="info" class="w-4 h-4"></i> Keterangan
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                            <span class="text-slate-600">Terjadwal (Approved)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="text-slate-600">Sedang Dipakai (In Use)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-slate-600">Hari Ini</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-slate-300"></span>
                            <span class="text-slate-600">Sudah Lewat</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <!-- Weekday Header -->
                <div class="grid grid-cols-7 bg-slate-50 border-b border-slate-100">
                    <template x-for="day in ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']">
                        <div class="py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wide" x-text="day"></div>
                    </template>
                </div>

                <!-- Calendar Body -->
                <div class="grid grid-cols-7 min-h-[500px]">
                    <template x-for="(cell, idx) in calendarCells" :key="idx">
                        <div class="relative border-b border-r border-slate-100 p-2 min-h-[100px] cursor-pointer transition-all"
                            :class="{
                                'bg-blue-50/50': cell.isToday,
                                'bg-slate-50/30': cell.isPast && !cell.isToday,
                                'hover:bg-slate-50': !cell.isToday
                            }"
                            @click="cell.date && showDayModal(cell.date)">
                            
                            <!-- Date Number -->
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-sm font-bold"
                                    :class="{
                                        'bg-blue-600 text-white w-7 h-7 flex items-center justify-center rounded-lg': cell.isToday,
                                        'text-slate-300': !cell.date,
                                        'text-slate-400': cell.isPast && !cell.isToday,
                                        'text-slate-700': cell.date && !cell.isPast && !cell.isToday
                                    }"
                                    x-text="cell.day || ''"></span>
                                <span x-show="cell.events && cell.events.length > 0" 
                                    class="text-[10px] font-bold text-slate-400"
                                    x-text="cell.events.length + ' jadwal'"></span>
                            </div>

                            <!-- Events -->
                            <div class="space-y-1">
                                <template x-for="(ev, evIdx) in (cell.events || []).slice(0, 3)" :key="ev.id">
                                    <div class="px-1.5 py-1 text-[10px] font-bold rounded truncate"
                                        :class="ev.status === 'in_use' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'">
                                        <span x-text="ev.start_time"></span> · <span x-text="ev.room_code || ev.room"></span>
                                    </div>
                                </template>
                                <template x-if="cell.events && cell.events.length > 3">
                                    <div class="text-[10px] font-bold text-slate-400 pl-1" x-text="'+' + (cell.events.length - 3) + ' lagi'"></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Day Detail Modal -->
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg transform transition-all"
                    @click.away="showModal = false">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-black text-slate-800" x-text="modalTitle"></h3>
                            <p class="text-xs font-medium text-slate-500 mt-0.5" x-text="modalEvents.length + ' jadwal penggunaan'"></p>
                        </div>
                        <button @click="showModal = false" class="p-2 hover:bg-slate-100 rounded-xl transition-colors">
                            <i data-feather="x" class="w-5 h-5 text-slate-400"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        <template x-if="modalEvents.length === 0">
                            <div class="py-12 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                                    <i data-feather="check-circle" class="w-8 h-8 text-emerald-600"></i>
                                </div>
                                <p class="text-lg font-bold text-slate-800 mb-1">Tidak Ada Jadwal</p>
                                <p class="text-sm text-slate-500">Semua ruangan tersedia pada tanggal ini</p>
                            </div>
                        </template>

                        <div class="space-y-3">
                            <template x-for="ev in modalEvents" :key="ev.id">
                                <div class="p-4 rounded-2xl border border-slate-100 hover:bg-slate-50 transition-all">
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div>
                                            <h4 class="text-sm font-black text-slate-800" x-text="ev.room"></h4>
                                            <p class="text-xs text-slate-500" x-text="ev.building"></p>
                                        </div>
                                        <span class="shrink-0 text-[10px] font-bold px-2.5 py-1 rounded-lg"
                                            :class="ev.status === 'in_use' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'"
                                            x-text="ev.status === 'in_use' ? 'Sedang Dipakai' : 'Terjadwal'"></span>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs">
                                        <div class="flex items-center gap-1.5 text-slate-600">
                                            <i data-feather="clock" class="w-3.5 h-3.5"></i>
                                            <span x-text="ev.start_time + ' - ' + ev.end_time"></span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-slate-600">
                                            <i data-feather="users" class="w-3.5 h-3.5"></i>
                                            <span x-text="'Kapasitas: ' + ev.capacity"></span>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-slate-100">
                                        <p class="text-xs text-slate-600" x-text="'Keperluan: ' + ev.title"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 rounded-b-3xl">
                        @auth
                        <a href="{{ route('bookings.create') }}" 
                            class="w-full inline-flex items-center justify-center gap-2 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all">
                            <i data-feather="plus" class="w-4 h-4"></i>
                            Ajukan Peminjaman
                        </a>
                        @else
                        <a href="{{ route('login') }}" 
                            class="w-full inline-flex items-center justify-center gap-2 py-3 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all">
                            <i data-feather="log-in" class="w-4 h-4"></i>
                            Login untuk Mengajukan Peminjaman
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto py-6 border-t border-slate-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm text-slate-500">&copy; {{ date('Y') }} {{ config('app.name') }}. Universitas Palangka Raya</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => feather.replace());
        
        function publicCalendar() {
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const allRooms = @json($rooms);
            
            return {
                currentDate: new Date(),
                events: [],
                calendarCells: [],
                filterBuilding: '',
                filterRoom: '',
                filteredRooms: allRooms,
                showModal: false,
                modalTitle: '',
                modalEvents: [],
                stats: {
                    totalRooms: {{ $rooms->count() }},
                    availableToday: 0,
                    inUseToday: 0,
                    bookingsThisMonth: 0
                },

                get monthYearTitle() {
                    return monthNames[this.currentDate.getMonth()] + ' ' + this.currentDate.getFullYear();
                },

                get todayEvents() {
                    const today = new Date().toISOString().split('T')[0];
                    return this.events.filter(e => e.date === today);
                },

                init() {
                    this.loadEvents();
                },

                filterRoomsByBuilding() {
                    if (this.filterBuilding) {
                        this.filteredRooms = allRooms.filter(r => r.building === this.filterBuilding);
                    } else {
                        this.filteredRooms = allRooms;
                    }
                    this.filterRoom = '';
                },

                async loadEvents() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth();
                    const start = `${year}-${String(month + 1).padStart(2, '0')}-01`;
                    const lastDay = new Date(year, month + 1, 0).getDate();
                    const end = `${year}-${String(month + 1).padStart(2, '0')}-${lastDay}`;

                    let url = `{{ route('api.public.calendar-events') }}?start=${start}&end=${end}`;
                    if (this.filterBuilding) url += `&building=${encodeURIComponent(this.filterBuilding)}`;
                    if (this.filterRoom) url += `&room_id=${this.filterRoom}`;

                    try {
                        const res = await fetch(url);
                        this.events = await res.json();
                        this.renderCalendar();
                        this.updateStats();
                    } catch (e) {
                        this.events = [];
                        this.renderCalendar();
                    }
                },

                updateStats() {
                    const today = new Date().toISOString().split('T')[0];
                    const todayBookings = this.events.filter(e => e.date === today);
                    const bookedRoomIds = [...new Set(todayBookings.map(e => e.room))];
                    
                    this.stats.inUseToday = todayBookings.filter(e => e.status === 'in_use').length;
                    this.stats.availableToday = this.stats.totalRooms - bookedRoomIds.length;
                    this.stats.bookingsThisMonth = this.events.length;
                },

                renderCalendar() {
                    const year = this.currentDate.getFullYear();
                    const month = this.currentDate.getMonth();
                    const firstDay = new Date(year, month, 1).getDay();
                    const lastDate = new Date(year, month + 1, 0).getDate();
                    const startDay = firstDay === 0 ? 6 : firstDay - 1;
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);

                    let cells = [];

                    // Empty cells before first day
                    for (let i = 0; i < startDay; i++) {
                        cells.push({ day: null, date: null, events: [], isToday: false, isPast: false });
                    }

                    // Day cells
                    for (let d = 1; d <= lastDate; d++) {
                        const cellDate = new Date(year, month, d);
                        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                        const dayEvents = this.events.filter(e => e.date === dateStr);
                        const isToday = today.getTime() === cellDate.getTime();
                        const isPast = cellDate < today && !isToday;

                        cells.push({
                            day: d,
                            date: dateStr,
                            events: dayEvents,
                            isToday,
                            isPast
                        });
                    }

                    // Fill remaining cells to complete the grid
                    while (cells.length % 7 !== 0) {
                        cells.push({ day: null, date: null, events: [], isToday: false, isPast: false });
                    }

                    this.calendarCells = cells;
                    this.$nextTick(() => feather.replace());
                },

                showDayModal(dateStr) {
                    const d = new Date(dateStr);
                    this.modalTitle = d.getDate() + ' ' + monthNames[d.getMonth()] + ' ' + d.getFullYear();
                    this.modalEvents = this.events.filter(e => e.date === dateStr);
                    this.showModal = true;
                    this.$nextTick(() => feather.replace());
                },

                prevMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1);
                    this.loadEvents();
                },

                nextMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1);
                    this.loadEvents();
                }
            };
        }
    </script>
</body>
</html>

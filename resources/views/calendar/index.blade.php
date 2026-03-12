@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.user')

@section('content')
    <div class="space-y-6 max-w-[1600px] mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white/80 backdrop-blur-xl p-6 rounded-3xl border border-white shadow-xl shadow-slate-200/50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-navy-600 to-navy-800 flex items-center justify-center text-white shadow-lg shadow-navy-200">
                    <i data-feather="calendar" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Jadwal & Kalender</h1>
                    <p class="text-sm text-slate-500 font-medium">Monitoring ketersediaan dan pemakaian ruangan secara real-time.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 bg-slate-100/80 p-1.5 rounded-2xl border border-slate-200/60">
                <button onclick="prevMonth()" class="p-2 rounded-xl hover:bg-white hover:text-navy-600 transition-all hover:shadow-sm text-slate-500">
                    <i data-feather="chevron-left" class="w-5 h-5"></i>
                </button>
                <div class="px-6 py-1 min-w-[160px] text-center">
                    <span id="calendar-title" class="text-sm font-bold text-slate-700 uppercase tracking-widest"></span>
                </div>
                <button onclick="nextMonth()" class="p-2 rounded-xl hover:bg-white hover:text-navy-600 transition-all hover:shadow-sm text-slate-500">
                    <i data-feather="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">
            <!-- Sidebar: Filters & Quick Tools -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Advanced Filter -->
                <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-white shadow-xl shadow-slate-200/50 p-6 space-y-5">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-feather="filter" class="w-4 h-4 text-navy-500"></i>
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-widest">Filter Ruangan</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Nama Gedung</label>
                            <select id="filter-building"
                                class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-navy-500/10 focus:border-navy-500 transition-all outline-none font-medium text-slate-700">
                                <option value="">Semua Gedung</option>
                                @foreach($buildings as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Ruangan Spesifik</label>
                            <select id="filter-room"
                                class="w-full px-4 py-3 bg-slate-50/50 border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-navy-500/10 focus:border-navy-500 transition-all outline-none font-medium text-slate-700">
                                <option value="">Semua Ruangan</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" data-building="{{ $room->building }}">{{ $room->name }} ({{ $room->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <button onclick="loadEvents()"
                            class="w-full py-4 bg-navy-600 text-white text-sm font-bold rounded-2xl hover:bg-navy-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-navy-100">
                            Apply Filter
                        </button>
                    </div>
                </div>

                <!-- Check Availability -->
                <div class="bg-slate-900 rounded-3xl shadow-xl shadow-slate-300/50 p-6 relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-colors"></div>
                    
                    <h3 class="text-xs font-bold text-navy-200 uppercase tracking-widest mb-4 flex items-center gap-2 relative">
                        <i data-feather="search" class="w-4 h-4"></i>
                        Cek Ketersediaan
                    </h3>
                    
                    <div class="space-y-4 relative">
                        <div class="space-y-1.5">
                            <select id="avail-room"
                                class="w-full px-4 py-3 bg-white/10 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-gold-400/30 focus:bg-white/20 transition-all outline-none backdrop-blur-sm">
                                <option value="" class="bg-navy-900">Pilih Ruangan...</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" class="bg-navy-900">{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <input type="date" id="avail-date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 bg-white/10 border border-white/10 rounded-2xl text-sm text-white focus:ring-2 focus:ring-gold-400/30 focus:bg-white/20 transition-all outline-none backdrop-blur-sm [color-scheme:dark]">
                        </div>
                        <button onclick="checkAvailability()"
                            class="w-full py-4 bg-gold-400 text-navy-900 text-sm font-bold rounded-2xl hover:bg-gold-300 transition-all shadow-lg shadow-gold-400/20">
                            Check Status
                        </button>
                    </div>
                    <div id="avail-result" class="hidden relative mt-4"></div>
                </div>

                <!-- Legend -->
                <div class="bg-white/50 backdrop-blur px-6 py-4 rounded-2xl border border-slate-100 text-[11px] flex flex-wrap gap-4 font-bold text-slate-500 uppercase tracking-wider">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-emerald-500/20"></span> Tersedia
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 border border-amber-500/20"></span> Terisi
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-navy-500/20 border border-navy-500/40"></span> Masa Lalu
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="xl:col-span-3 space-y-4">
                <div class="bg-white rounded-[2rem] border border-white shadow-2xl shadow-slate-200/50 overflow-hidden">
                    <!-- Weekday Header -->
                    <div class="grid grid-cols-7 bg-slate-50/80 border-b border-slate-100 backdrop-blur">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $day)
                            <div class="px-2 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Calendar Body -->
                    <div id="calendar-body" class="grid grid-cols-7 border-collapse bg-white">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- Events Modal/Panel logic -->
                <div id="day-events" class="hidden transform transition-all duration-300 translate-y-4 opacity-0">
                    <div class="bg-navy-900 rounded-[2rem] shadow-2xl p-8 relative overflow-hidden">
                        <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-navy-800 rounded-full opacity-50"></div>
                        
                        <div class="flex items-center justify-between mb-6 relative">
                            <div>
                                <h3 id="day-events-title" class="text-xl font-bold text-white tracking-tight"></h3>
                                <p class="text-navy-300 text-xs font-medium uppercase tracking-widest mt-1">Daftar Agenda Penggunaan Ruangan</p>
                            </div>
                            <button onclick="document.getElementById('day-events').classList.add('hidden')" class="p-2 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all">
                                <i data-feather="x" class="w-5 h-5"></i>
                            </button>
                        </div>
                        
                        <div id="day-events-list" class="grid grid-cols-1 md:grid-cols-2 gap-4 relative"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Move Inside Content for HTMX -->
    <script>
        // Ensure scripts re-run on HTMX swap
        (function() {
            let currentDate = new Date();
            let events = [];
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

            window.loadEvents = function() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                const start = `${year}-${String(month + 1).padStart(2, '0')}-01`;
                const lastDay = new Date(year, month + 1, 0).getDate();
                const end = `${year}-${String(month + 1).padStart(2, '0')}-${lastDay}`;

                let url = `{{ route('api.calendar-events') }}?start=${start}&end=${end}`;
                const building = document.getElementById('filter-building').value;
                const room = document.getElementById('filter-room').value;
                if (building) url += `&building=${encodeURIComponent(building)}`;
                if (room) url += `&room_id=${room}`;

                fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } })
                    .then(r => r.json())
                    .then(data => { events = data; renderCalendar(); })
                    .catch(() => { events = []; renderCalendar(); });
            }

            window.renderCalendar = function() {
                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();
                document.getElementById('calendar-title').textContent = `${monthNames[month]} ${year}`;

                const firstDay = new Date(year, month, 1).getDay();
                const lastDate = new Date(year, month + 1, 0).getDate();
                const startDay = firstDay === 0 ? 6 : firstDay - 1;
                const today = new Date();
                today.setHours(0,0,0,0);

                let html = '';
                // Empty cells
                for (let i = 0; i < startDay; i++) {
                    html += '<div class="aspect-square border-b border-r border-slate-50 bg-slate-50/30 p-2"></div>';
                }
                // Day cells
                for (let d = 1; d <= lastDate; d++) {
                    const cellDate = new Date(year, month, d);
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    const dayEvents = events.filter(e => e.date === dateStr);
                    const isToday = today.getTime() === cellDate.getTime();
                    const isPast = cellDate < today && !isToday;

                    const hasEvents = dayEvents.length > 0;

                    html += `
                    <div class="relative aspect-square border-b border-r border-slate-50 p-2 group cursor-pointer transition-all duration-300
                                ${isToday ? 'bg-navy-50/30' : 'hover:bg-slate-50'}
                                ${isPast ? 'opacity-80' : ''}"
                         onclick="showDayEvents('${dateStr}')">
                        
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-xs font-black transition-all
                                ${isToday ? 'bg-navy-600 text-white w-7 h-7 flex items-center justify-center rounded-xl shadow-lg shadow-navy-200' : 'text-slate-400 group-hover:text-navy-600'}">
                                ${d}
                            </span>
                            ${hasEvents ? '<span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse mt-1"></span>' : ''}
                        </div>

                        <div class="space-y-1 overflow-hidden">
                            ${dayEvents.slice(0, 2).map(ev => `
                                <div class="px-1.5 py-0.5 rounded-lg border text-[9px] font-bold truncate leading-tight transition-transform group-hover:scale-[1.02]
                                    ${ev.status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-amber-50 text-amber-700 border-amber-100'}">
                                    ${ev.start_time} ${ev.room_code || ev.room}
                                </div>
                            `).join('')}
                            ${dayEvents.length > 2 ? `<div class="text-[8px] font-black text-navy-400 uppercase tracking-tighter ml-1">+${dayEvents.length - 2} AGENDA</div>` : ''}
                        </div>
                    </div>`;
                }
                document.getElementById('calendar-body').innerHTML = html;
                if (typeof feather !== "undefined") feather.replace();
            }

            window.showDayEvents = function(dateStr) {
                const dayEvents = events.filter(e => e.date === dateStr);
                const panel = document.getElementById('day-events');
                const title = document.getElementById('day-events-title');
                const list = document.getElementById('day-events-list');

                const d = new Date(dateStr);
                title.textContent = `${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;

                if (dayEvents.length === 0) {
                    list.innerHTML = '<div class="col-span-full py-12 text-center"><div class="text-navy-400/30 font-black text-5xl mb-4 italic">KOSONG</div><p class="text-navy-300 font-medium">Tidak ada agenda peminjaman ruangan yang terdaftar.</p></div>';
                } else {
                    list.innerHTML = dayEvents.map(ev => {
                        const statusColor = ev.status === 'approved' ? 'bg-emerald-400/10 text-emerald-400 border-emerald-400/20' : 'bg-gold-400/10 text-gold-400 border-gold-400/20';
                        return `
                        <div class="p-5 rounded-3xl bg-white/5 border border-white/5 hover:bg-white/[0.08] transition-all group/item">
                            <div class="flex justify-between items-start gap-3 mb-3">
                                <div>
                                    <h4 class="text-sm font-bold text-white group-hover/item:text-gold-400 transition-colors uppercase tracking-tight">${ev.room}</h4>
                                    <p class="text-[10px] font-bold text-navy-400 uppercase tracking-widest mt-0.5">${ev.building} • ${ev.room_code}</p>
                                </div>
                                <span class="text-[10px] font-black px-2.5 py-1 rounded-xl border ${statusColor} uppercase tracking-widest">${ev.status_label}</span>
                            </div>
                            
                            <div class="flex items-center gap-2 mb-4">
                                <span class="px-2 py-1 bg-navy-800 text-navy-200 rounded-lg text-xs font-mono font-medium">${ev.start_time} - ${ev.end_time}</span>
                            </div>

                            <div class="flex items-center gap-3 border-t border-white/5 pt-4">
                                <div class="w-8 h-8 rounded-full bg-navy-800 flex items-center justify-center text-[10px] font-black text-navy-300">
                                    ${ev.user.charAt(0).toUpperCase()}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-navy-100 truncate">${ev.title}</p>
                                    <p class="text-[10px] font-medium text-navy-400 capitalize">${ev.user}</p>
                                </div>
                            </div>
                        </div>`;
                    }).join('');
                }
                
                panel.classList.remove('hidden');
                setTimeout(() => {
                    panel.style.opacity = '1';
                    panel.style.transform = 'translateY(0)';
                }, 10);
                if (typeof feather !== "undefined") feather.replace();
            }

            window.prevMonth = function() { currentDate.setMonth(currentDate.getMonth() - 1); loadEvents(); }
            window.nextMonth = function() { currentDate.setMonth(currentDate.getMonth() + 1); loadEvents(); }
            
            window.checkAvailability = function() {
                const roomId = document.getElementById('avail-room').value;
                const date = document.getElementById('avail-date').value;
                const result = document.getElementById('avail-result');
                if (!roomId || !date) { result.innerHTML = '<p class="text-xs text-rose-400 font-bold mt-2">Lengkapi form!</p>'; result.classList.remove('hidden'); return; }

                fetch(`{{ route('api.room-availability') }}?room_id=${roomId}&date=${date}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                })
                    .then(r => r.json())
                    .then(data => {
                        let html = `<div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur shadow-2xl animate-in fade-in slide-in-from-top-2 duration-300">`;
                        html += `<h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">${data.room.name}</h4>`;
                        html += `<p class="text-[10px] font-bold text-navy-400 uppercase tracking-widest">Cap: ${data.room.capacity} Orang • ${data.room.status}</p>`;
                        
                        if (data.bookings.length === 0) {
                            html += '<div class="mt-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-[11px] font-black text-emerald-400 text-center uppercase tracking-widest">✅ Tersedia Sepenuhnya</div>';
                        } else {
                            html += '<div class="mt-4 space-y-2">';
                            data.bookings.forEach(b => {
                                html += `<div class="flex items-center justify-between p-2.5 bg-navy-800/50 rounded-xl border border-white/5">
                                            <span class="text-[10px] font-mono font-bold text-navy-300">${b.start_time}-${b.end_time}</span>
                                            <span class="text-[10px] font-black text-gold-400 uppercase tracking-tighter truncate max-w-[100px]">${b.purpose}</span>
                                         </div>`;
                            });
                            html += '</div>';
                        }
                        html += '</div>';
                        result.innerHTML = html;
                        result.classList.remove('hidden');
                    });
            }

            // Init
            loadEvents();
            
            // Re-bind feather icons
            if (typeof feather !== "undefined") feather.replace();

            // Filter logic
            document.getElementById('filter-building').addEventListener('change', function () {
                const building = this.value;
                const roomSelect = document.getElementById('filter-room');
                Array.from(roomSelect.options).forEach(opt => {
                    if (!opt.value) return;
                    opt.style.display = !building || opt.dataset.building === building ? '' : 'none';
                });
                roomSelect.value = '';
            });
        })();
    </script>
@endsection
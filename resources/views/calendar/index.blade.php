@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.user')

@section('content')
    <div class="space-y-6 max-w-[1600px] mx-auto pb-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-navy-600 flex items-center justify-center text-white shadow-lg shadow-navy-100">
                    <i data-feather="calendar" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight">Jadwal & Kalender</h1>
                    <p class="text-sm text-slate-400 font-medium">Monitoring ketersediaan ruangan secara real-time.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 bg-slate-50 p-1.5 rounded-2xl border border-slate-100">
                <button onclick="prevMonth()" class="p-2 rounded-xl hover:bg-white hover:text-navy-600 transition-all hover:shadow-sm text-slate-400">
                    <i data-feather="chevron-left" class="w-5 h-5"></i>
                </button>
                <div class="px-6 py-1 min-w-[160px] text-center">
                    <span id="calendar-title" class="text-sm font-bold text-slate-600 uppercase tracking-widest"></span>
                </div>
                <button onclick="nextMonth()" class="p-2 rounded-xl hover:bg-white hover:text-navy-600 transition-all hover:shadow-sm text-slate-400">
                    <i data-feather="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">
            <!-- Sidebar: Filters & Quick Tools -->
            <div class="xl:col-span-1 space-y-6">
                <!-- Advanced Filter -->
                <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 p-6 space-y-5">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-feather="filter" class="w-4 h-4 text-navy-600"></i>
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Filter Ruangan</h3>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Gedung</label>
                            <select id="filter-building"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-navy-500/5 focus:border-navy-500 transition-all outline-none font-bold text-slate-700">
                                <option value="">Semua Gedung</option>
                                @foreach($buildings as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Ruangan</label>
                            <select id="filter-room"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-navy-500/5 focus:border-navy-500 transition-all outline-none font-bold text-slate-700">
                                <option value="">Semua Ruangan</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" data-building="{{ $room->building }}">{{ $room->name }} ({{ $room->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <button onclick="loadEvents()"
                            class="w-full py-4 bg-navy-600 text-white text-sm font-bold rounded-2xl hover:bg-navy-700 transition-all shadow-lg shadow-navy-200 flex items-center justify-center gap-2">
                            Terapkan Filter
                        </button>
                    </div>
                </div>

                <!-- Check Availability -->
                <div class="bg-white rounded-[2rem] border-2 border-slate-50 shadow-2xl shadow-slate-200/50 p-6 relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-gold-400/5 rounded-full blur-2xl group-hover:bg-gold-400/10 transition-colors"></div>
                    
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2 relative">
                        <i data-feather="search" class="w-4 h-4 text-gold-500"></i>
                        Cek Ketersediaan
                    </h3>
                    
                    <div class="space-y-4 relative">
                        <div class="space-y-1.5 text-slate-700">
                            <select id="avail-room"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-gold-400/5 focus:border-gold-400 transition-all outline-none font-bold">
                                <option value="">Pilih Ruangan...</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <input type="date" id="avail-date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-4 focus:ring-gold-400/5 focus:border-gold-400 transition-all outline-none font-bold text-slate-700">
                        </div>
                        <button onclick="checkAvailability()"
                            class="w-full py-4 bg-gold-400 text-white text-sm font-bold rounded-2xl hover:bg-gold-500 transition-all shadow-lg shadow-gold-100">
                            Cek Status
                        </button>
                    </div>
                    <div id="avail-result" class="hidden relative mt-4"></div>
                </div>

                <!-- Legend -->
                <div class="bg-white px-6 py-4 rounded-2xl border border-slate-50 text-[10px] flex flex-wrap gap-4 font-black text-slate-400 uppercase tracking-widest">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Tersedia
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-gold-400"></span> Terisi
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-slate-200"></span> Lampau
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="xl:col-span-3 space-y-4">
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl shadow-slate-200/40 overflow-x-auto lg:overflow-hidden">
                    <div class="min-w-[700px] lg:min-w-0">
                        <!-- Weekday Header -->
                        <div class="grid grid-cols-7 bg-slate-50/50 border-b border-slate-100">
                            @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                                <div class="px-2 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    {{ $day }}
                                </div>
                            @endforeach
                        </div>

                        <!-- Calendar Body -->
                        <div id="calendar-body" class="grid grid-cols-7 bg-white">
                            <!-- Filled by JS -->
                        </div>
                    </div>
                </div>

                <!-- Removed old day-events panel -->
            </div>
        </div>
    </div>

    <!-- Event Modal (Popup) -->
    <div id="event-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

        <!-- Modal Position -->
        <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
            <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white border border-slate-100 shadow-[0_20px_50px_rgba(0,0,0,0.1)] transition-all w-full max-w-2xl animate-in zoom-in-95 duration-200">
                <!-- Header -->
                <div class="px-8 pt-8 pb-4 flex items-center justify-between border-b border-slate-50">
                    <div>
                        <h3 id="modal-date-title" class="text-2xl font-black text-slate-800 tracking-tight"></h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Agenda Penggunaan Ruangan</p>
                    </div>
                    <button onclick="closeModal()" class="p-3 bg-slate-50 hover:bg-slate-100 text-slate-400 rounded-2xl transition-all">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <div id="modal-events-list" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 py-6 bg-slate-50 flex justify-end rounded-b-[2.5rem]">
                    <button onclick="closeModal()" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 text-xs font-black rounded-xl hover:bg-slate-100 transition-all uppercase tracking-widest">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>

    <!-- Scripts Inside Content -->
    <script>
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
                for (let i = 0; i < startDay; i++) {
                    html += '<div class="aspect-square border-b border-r border-slate-50 bg-slate-50/[0.3] p-2"></div>';
                }
                for (let d = 1; d <= lastDate; d++) {
                    const cellDate = new Date(year, month, d);
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    const dayEvents = events.filter(e => e.date === dateStr);
                    const isToday = today.getTime() === cellDate.getTime();
                    const isPast = cellDate < today && !isToday;

                    html += `
                    <div class="relative aspect-square border-b border-r border-slate-50 p-2 group cursor-pointer transition-all duration-300
                                ${isToday ? 'bg-navy-50/50' : 'hover:bg-slate-50'}
                                ${isPast ? 'bg-slate-50/20' : ''}"
                         onclick="showDayEvents('${dateStr}')">
                        
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-[11px] font-black transition-all
                                ${isToday ? 'bg-navy-600 text-white w-7 h-7 flex items-center justify-center rounded-xl shadow-lg shadow-navy-100' : 'text-slate-300 group-hover:text-navy-600'}">
                                ${d}
                            </span>
                            ${dayEvents.length > 0 ? '<span class="w-1.5 h-1.5 rounded-full bg-gold-400 animate-pulse mt-1"></span>' : ''}
                        </div>

                        <div class="space-y-1 overflow-hidden">
                            ${dayEvents.slice(0, 2).map(ev => `
                                <div class="px-2 py-0.5 rounded-lg border text-[8px] font-black truncate shadow-sm
                                    ${ev.status === 'approved' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gold-50 text-gold-600 border-gold-100'}">
                                    ${ev.start_time} ${ev.room_code || ev.room}
                                </div>
                            `).join('')}
                            ${dayEvents.length > 2 ? `<div class="text-[7px] font-black text-slate-300 uppercase tracking-tighter ml-1">+${dayEvents.length - 2} Lagi</div>` : ''}
                        </div>
                    </div>`;
                }
                document.getElementById('calendar-body').innerHTML = html;
                if (typeof feather !== "undefined") feather.replace();
            }

            window.showDayEvents = function(dateStr) {
                const dayEvents = events.filter(e => e.date === dateStr);
                const modal = document.getElementById('event-modal');
                const title = document.getElementById('modal-date-title');
                const list = document.getElementById('modal-events-list');

                const d = new Date(dateStr);
                title.textContent = `${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;

                if (dayEvents.length === 0) {
                    list.innerHTML = '<div class="py-16 text-center"><div class="text-slate-100 font-black text-7xl mb-4 italic tracking-tighter">KOSONG</div><p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Tidak ada reservasi untuk tanggal ini</p></div>';
                } else {
                    list.innerHTML = dayEvents.map(ev => {
                        const statusColor = ev.status === 'approved' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gold-50 text-gold-600 border-gold-100';
                        return `
                        <div class="p-6 rounded-[2rem] bg-slate-50 border border-slate-100 hover:bg-white hover:shadow-xl hover:shadow-slate-100 transition-all group/item">
                            <div class="flex justify-between items-start gap-3 mb-4">
                                <div>
                                    <h4 class="text-lg font-black text-slate-800 tracking-tight uppercase">${ev.room}</h4>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">${ev.building} • ${ev.room_code}</p>
                                </div>
                                <span class="text-[10px] font-black px-3 py-1 rounded-xl border ${statusColor} uppercase tracking-widest">${ev.status_label}</span>
                            </div>
                            
                            <div class="flex items-center gap-2 mb-6">
                                <span class="px-3 py-1 bg-white border border-slate-100 text-slate-600 rounded-xl text-xs font-mono font-bold">${ev.start_time} - ${ev.end_time}</span>
                            </div>

                            <div class="flex items-center gap-3 border-t border-slate-100 pt-5">
                                <div class="w-10 h-10 rounded-full bg-navy-600 flex items-center justify-center text-xs font-black text-white shadow-lg shadow-navy-100">
                                    ${ev.user.charAt(0).toUpperCase()}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate leading-none">${ev.title}</p>
                                    <p class="text-[11px] font-medium text-slate-400 mt-1 capitalize">${ev.user}</p>
                                </div>
                            </div>
                        </div>`;
                    }).join('');
                }
                
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                if (typeof feather !== "undefined") feather.replace();
            }

            window.closeModal = function() {
                const modal = document.getElementById('event-modal');
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            window.prevMonth = function() { currentDate.setMonth(currentDate.getMonth() - 1); loadEvents(); }
            window.nextMonth = function() { currentDate.setMonth(currentDate.getMonth() + 1); loadEvents(); }
            
            window.checkAvailability = function() {
                const roomId = document.getElementById('avail-room').value;
                const date = document.getElementById('avail-date').value;
                const result = document.getElementById('avail-result');
                if (!roomId || !date) { result.innerHTML = '<p class="text-xs text-rose-500 font-black mt-2">Mohon lengkapi form.</p>'; result.classList.remove('hidden'); return; }

                fetch(`{{ route('api.room-availability') }}?room_id=${roomId}&date=${date}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                })
                    .then(r => r.json())
                    .then(data => {
                        let html = `<div class="p-5 rounded-3xl bg-white border-2 border-slate-50 shadow-2xl animate-in zoom-in-95 duration-300">`;
                        html += `<h4 class="text-sm font-black text-slate-800 uppercase tracking-tight">${data.room.name}</h4>`;
                        html += `<p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Kapasitas: ${data.room.capacity} • ${data.room.status}</p>`;
                        
                        if (data.bookings.length === 0) {
                            html += '<div class="mt-5 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-xs font-black text-emerald-600 text-center uppercase tracking-widest">✅ Tersedia</div>';
                        } else {
                            html += '<div class="mt-5 space-y-2">';
                            data.bookings.forEach(b => {
                                html += `<div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                            <span class="text-[11px] font-mono font-bold text-slate-500">${b.start_time}-${b.end_time}</span>
                                            <span class="text-[11px] font-black text-gold-500 uppercase tracking-tight truncate max-w-[100px]">${b.purpose}</span>
                                         </div>`;
                            });
                            html += '</div>';
                        }
                        html += '</div>';
                        result.innerHTML = html;
                        result.classList.remove('hidden');
                    });
            }

            loadEvents();
            if (typeof feather !== "undefined") feather.replace();

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
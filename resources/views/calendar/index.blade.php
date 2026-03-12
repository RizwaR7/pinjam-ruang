@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.user')

@section('content')
    <div class="h-[calc(100vh-8rem)] flex flex-col space-y-4 max-w-[1600px] mx-auto pb-4 overflow-hidden">
        <!-- Compact Header -->
        <div class="flex items-center justify-between bg-white px-6 py-3 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-navy-600 flex items-center justify-center text-white">
                    <i data-feather="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-lg font-black text-slate-800 tracking-tight">Kalender Ruangan</h1>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Real-time Monitoring</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 bg-slate-50 p-1 rounded-xl border border-slate-100">
                <button onclick="prevMonth()" class="p-1.5 rounded-lg hover:bg-white hover:text-navy-600 transition-all text-slate-400">
                    <i data-feather="chevron-left" class="w-4 h-4"></i>
                </button>
                <div class="px-4 text-center min-w-[140px]">
                    <span id="calendar-title" class="text-sm font-black text-slate-600 uppercase tracking-widest"></span>
                </div>
                <button onclick="nextMonth()" class="p-1.5 rounded-lg hover:bg-white hover:text-navy-600 transition-all text-slate-400">
                    <i data-feather="chevron-right" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <div class="flex-1 grid grid-cols-1 xl:grid-cols-4 gap-4 min-h-0">
            <!-- Sidebar: Compact Tools -->
            <div class="xl:col-span-1 flex flex-col gap-4 min-h-0">
                <!-- Advanced Filter -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 space-y-3">
                    <div class="flex items-center gap-2">
                        <i data-feather="filter" class="w-3.5 h-3.5 text-navy-600"></i>
                        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Filter</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-2">
                        <select id="filter-building"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs focus:border-navy-500 outline-none font-bold text-slate-700">
                            <option value="">Semua Gedung</option>
                            @foreach($buildings as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>

                        <select id="filter-room"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs focus:border-navy-500 outline-none font-bold text-slate-700">
                            <option value="">Semua Ruangan</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" data-building="{{ $room->building }}">{{ $room->name }}</option>
                            @endforeach
                        </select>

                        <button onclick="loadEvents()"
                            class="w-full py-2.5 bg-navy-600 text-xs font-black rounded-xl hover:bg-navy-700 transition-all uppercase tracking-widest">
                            Terapkan
                        </button>
                    </div>
                </div>

                <!-- Check Availability (Compact) -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex-1 flex flex-col min-h-0">
                    <div class="flex items-center gap-2 mb-3">
                        <i data-feather="search" class="w-3.5 h-3.5 text-gold-500"></i>
                        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Cek Slot</h3>
                    </div>
                    
                    <div class="space-y-2">
                        <select id="avail-room" class="w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold text-slate-700">
                            <option value="">Pilih Ruangan...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" id="avail-date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-xs font-bold text-slate-700">
                        <button onclick="checkAvailability()"
                            class="w-full py-2.5 bg-gold-400 text-white text-xs font-black rounded-xl hover:bg-gold-500 transition-all uppercase tracking-widest">
                            Cek
                        </button>
                    </div>
                    
                    <div id="avail-result" class="mt-3 flex-1 overflow-y-auto custom-scrollbar hidden"></div>
                </div>

                <!-- Legend -->
                <div class="bg-white p-3 rounded-xl border border-slate-100 text-[10px] flex justify-between font-black text-slate-400 uppercase tracking-widest">
                    <div class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Okay</div>
                    <div class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-gold-400"></span> Full</div>
                    <div class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> Past</div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="xl:col-span-3 flex flex-col min-h-0 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <!-- Weekday Header -->
                <div class="grid grid-cols-7 bg-slate-50/50 border-b border-slate-100">
                    @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                        <div class="py-3 text-center text-[11px] font-black text-slate-400 uppercase tracking-widest">
                            {{ $day }}
                        </div>
                    @endforeach
                </div>

                <!-- Calendar Body -->
                <div id="calendar-body" class="flex-1 grid grid-cols-7 bg-white min-h-0">
                    <!-- Filled by JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Event Modal (Popup) -->
    <div id="event-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-3xl bg-white border border-slate-100 shadow-2xl transition-all w-full max-w-lg animate-in zoom-in-95 duration-200">
                <div class="px-6 py-4 flex items-center justify-between border-b border-slate-50">
                    <div>
                        <h3 id="modal-date-title" class="text-lg font-black text-slate-800 tracking-tight"></h3>
                        <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Agenda Penggunaan</p>
                    </div>
                    <button onclick="closeModal()" class="p-2 bg-slate-50 hover:bg-slate-100 text-slate-400 rounded-xl transition-all">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="p-6">
                    <div id="modal-events-list" class="space-y-3 max-h-[50vh] overflow-y-auto pr-1 custom-scrollbar"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>

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
                    html += '<div class="border-b border-r border-slate-50 bg-slate-50/[0.2]"></div>';
                }
                for (let d = 1; d <= lastDate; d++) {
                    const cellDate = new Date(year, month, d);
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    const dayEvents = events.filter(e => e.date === dateStr);
                    const isToday = today.getTime() === cellDate.getTime();
                    const isPast = cellDate < today && !isToday;

                    html += `
                    <div class="relative border-b border-r border-slate-50 p-1.5 group cursor-pointer transition-all min-h-0
                                ${isToday ? 'bg-navy-50/50' : 'hover:bg-slate-50'}
                                ${isPast ? 'bg-slate-50/10' : ''}"
                         onclick="showDayEvents('${dateStr}')">
                        
                        <div class="flex justify-between items-start mb-0.5">
                            <span class="text-xs font-black 
                                ${isToday ? 'bg-navy-600 text-white w-5 h-5 flex items-center justify-center rounded-lg' : 'text-slate-300 group-hover:text-navy-600'}">
                                ${d}
                            </span>
                        </div>

                        <div class="space-y-0.5 overflow-hidden">
                            ${dayEvents.slice(0, 3).map(ev => `
                                <div class="px-1 text-[9px] font-black truncate rounded border
                                    ${ev.status === 'approved' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gold-50 text-gold-600 border-gold-100'}">
                                    ${ev.start_time.split(':')[0]}… ${ev.room_code || ev.room.substring(0,5)}
                                </div>
                            `).join('')}
                            ${dayEvents.length > 3 ? `<div class="text-[8px] font-black text-slate-300 uppercase ml-0.5">+${dayEvents.length - 3}</div>` : ''}
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
                    list.innerHTML = '<div class="py-10 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kosong</div>';
                } else {
                    list.innerHTML = dayEvents.map(ev => {
                        const statusColor = ev.status === 'approved' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-gold-50 text-gold-600 border-gold-100';
                        return `
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-white transition-all">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-xs font-black text-slate-800 uppercase">${ev.room}</h4>
                                <span class="text-[8px] font-black px-2 py-0.5 rounded-lg border ${statusColor} uppercase">${ev.status_label}</span>
                            </div>
                            <p class="text-[9px] font-bold text-slate-500 mb-2">${ev.start_time} - ${ev.end_time}</p>
                            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                                <div class="w-6 h-6 rounded-full bg-navy-600 flex items-center justify-center text-[8px] font-black text-white">${ev.user.charAt(0).toUpperCase()}</div>
                                <p class="text-[10px] font-bold text-slate-700 truncate">${ev.title}</p>
                            </div>
                        </div>`;
                    }).join('');
                }
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                if (typeof feather !== "undefined") feather.replace();
            }

            window.closeModal = function() {
                document.getElementById('event-modal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            window.prevMonth = function() { currentDate.setMonth(currentDate.getMonth() - 1); loadEvents(); }
            window.nextMonth = function() { currentDate.setMonth(currentDate.getMonth() + 1); loadEvents(); }
            
            window.checkAvailability = function() {
                const roomId = document.getElementById('avail-room').value;
                const date = document.getElementById('avail-date').value;
                const result = document.getElementById('avail-result');
                if (!roomId || !date) return;

                fetch(`{{ route('api.room-availability') }}?room_id=${roomId}&date=${date}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                })
                    .then(r => r.json())
                    .then(data => {
                        let html = `<div class="p-3 bg-slate-50 rounded-xl border border-slate-100">`;
                        if (data.bookings.length === 0) {
                            html += '<div class="text-[9px] font-black text-emerald-600 text-center uppercase tracking-widest">✅ Tersedia</div>';
                        } else {
                            html += '<div class="space-y-1">';
                            data.bookings.forEach(b => {
                                html += `<div class="flex justify-between text-[8px] font-bold text-slate-500"><span>${b.start_time}-${b.end_time}</span><span class="text-gold-500">${b.purpose}</span></div>`;
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
        })();
    </script>
@endsection
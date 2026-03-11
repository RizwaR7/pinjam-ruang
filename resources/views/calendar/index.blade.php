@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.user')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">📅 Jadwal & Kalender</h1>
                <p class="text-sm text-slate-500 mt-1">Lihat jadwal pemakaian ruangan dan cek ketersediaan</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Sidebar: Filters -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Building Filter -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Filter</h3>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600">Gedung</label>
                        <select id="filter-building"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                            <option value="">Semua Gedung</option>
                            @foreach($buildings as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600">Ruangan</label>
                        <select id="filter-room"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                            <option value="">Semua Ruangan</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" data-building="{{ $room->building }}">{{ $room->name }}
                                    ({{ $room->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button onclick="loadEvents()"
                        class="w-full px-4 py-2 bg-cyan-600 text-white text-sm font-semibold rounded-lg hover:bg-cyan-700 transition-colors">
                        Terapkan Filter
                    </button>
                </div>

                <!-- Room Availability Check -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 space-y-4">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Cek Ketersediaan</h3>
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600">Ruangan</label>
                        <select id="avail-room"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none">
                            <option value="">Pilih Ruangan</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-slate-600">Tanggal</label>
                        <input type="date" id="avail-date" min="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none"
                            value="{{ date('Y-m-d') }}">
                    </div>
                    <button onclick="checkAvailability()"
                        class="w-full px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors">
                        Cek Ketersediaan
                    </button>
                    <div id="avail-result" class="hidden"></div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <!-- Calendar Header -->
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                        <button onclick="prevMonth()" class="p-2 rounded-lg hover:bg-slate-200 transition-colors">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                </path>
                            </svg>
                        </button>
                        <h2 id="calendar-title" class="text-lg font-bold text-slate-800"></h2>
                        <button onclick="nextMonth()" class="p-2 rounded-lg hover:bg-slate-200 transition-colors">
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>

                    <!-- Day Headers -->
                    <div class="grid grid-cols-7 border-b border-slate-100">
                        @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                            <div class="px-2 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                {{ $day }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Calendar Body -->
                    <div id="calendar-body" class="grid grid-cols-7 min-h-[500px]">
                        <!-- Filled by JS -->
                    </div>
                </div>

                <!-- Events List for Selected Day -->
                <div id="day-events" class="mt-4 hidden">
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                            <h3 id="day-events-title" class="text-sm font-bold text-slate-700"></h3>
                        </div>
                        <div id="day-events-list" class="divide-y divide-slate-50"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentDate = new Date();
        let events = [];
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        function loadEvents() {
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

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            document.getElementById('calendar-title').textContent = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const lastDate = new Date(year, month + 1, 0).getDate();
            const startDay = firstDay === 0 ? 6 : firstDay - 1;
            const today = new Date();

            let html = '';
            // Empty cells before first day
            for (let i = 0; i < startDay; i++) {
                html += '<div class="border-b border-r border-slate-50 p-2 min-h-[80px] bg-slate-25"></div>';
            }
            // Day cells
            for (let d = 1; d <= lastDate; d++) {
                const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const dayEvents = events.filter(e => e.date === dateStr);
                const isToday = today.getFullYear() === year && today.getMonth() === month && today.getDate() === d;

                const hasEvents = dayEvents.length > 0;

                html += `<div class="border-b border-r border-slate-50 p-2 min-h-[80px] cursor-pointer transition-colors
                ${isToday ? 'bg-cyan-50/50' : ''}
                ${hasEvents ? 'bg-amber-50 hover:bg-amber-100' : 'hover:bg-cyan-50/30'}"
                onclick="showDayEvents('${dateStr}')">`; html += `<span class="text-xs font-bold ${isToday ? 'bg-cyan-600 text-white px-1.5 py-0.5 rounded-full' : 'text-slate-600'}">${d}</span>`;

                if (dayEvents.length > 0) {
                    html += `<div class="w-1.5 h-1.5 bg-red-500 rounded-full mt-1"></div>`;
                }
                dayEvents.slice(0, 3).forEach(ev => {
                    const color = ev.status === 'approved' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200';
                    html += `<div class="mt-1 px-1.5 py-0.5 text-[10px] font-medium rounded border ${color} truncate">${ev.start_time} ${ev.room_code || ev.room}</div>`;
                });
                if (dayEvents.length > 3) {
                    html += `<div class="mt-1 text-[10px] text-slate-400 font-medium">+${dayEvents.length - 3} lagi</div>`;
                }
                html += '</div>';
            }
            document.getElementById('calendar-body').innerHTML = html;
        }

        function showDayEvents(dateStr) {
            const dayEvents = events.filter(e => e.date === dateStr);
            const panel = document.getElementById('day-events');
            const title = document.getElementById('day-events-title');
            const list = document.getElementById('day-events-list');

            const d = new Date(dateStr);
            title.textContent = `Jadwal ${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()} (${dayEvents.length} booking)`;

            if (dayEvents.length === 0) {
                list.innerHTML = '<div class="px-6 py-8 text-center text-sm text-slate-400">Tidak ada booking pada tanggal ini</div>';
            } else {
                list.innerHTML = dayEvents.map(ev => {
                    const statusColor = ev.status === 'approved' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-amber-100 text-amber-700 border-amber-200';
                    return `<div class="px-6 py-3 flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-800">${ev.room} (${ev.room_code})</p>
                                        <p class="text-xs text-slate-400">${ev.building}</p>
                                        <p class="text-xs text-slate-500 truncate">${ev.title} — ${ev.user}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-0.5 rounded">${ev.start_time} - ${ev.end_time}</span>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full border ${statusColor}">${ev.status_label}</span>
                                    </div>
                                </div>`;
                }).join('');
            }
            panel.classList.remove('hidden');
        }

        window.prevMonth = function () {
            currentDate.setMonth(currentDate.getMonth() - 1);
            loadEvents();
        }

        window.nextMonth = function () {
            currentDate.setMonth(currentDate.getMonth() + 1);
            loadEvents();
        }
        function checkAvailability() {
            const roomId = document.getElementById('avail-room').value;
            const date = document.getElementById('avail-date').value;
            const result = document.getElementById('avail-result');
            if (!roomId || !date) { result.innerHTML = '<p class="text-xs text-rose-500 mt-2">Pilih ruangan dan tanggal</p>'; result.classList.remove('hidden'); return; }

            fetch(`{{ route('api.room-availability') }}?room_id=${roomId}&date=${date}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
            })
                .then(r => r.json())
                .then(data => {
                    let html = `<div class="mt-3 p-3 rounded-lg bg-slate-50 border border-slate-200">`;
                    html += `<p class="text-sm font-semibold text-slate-700">${data.room.name}</p>`;
                    html += `<p class="text-xs text-slate-500">Status: ${data.room.status} • Kapasitas: ${data.room.capacity}</p>`;
                    if (data.bookings.length === 0) {
                        html += '<p class="text-xs text-emerald-600 font-semibold mt-2">✅ Ruangan tersedia sepanjang hari!</p>';
                    } else {
                        html += '<p class="text-xs text-amber-600 font-semibold mt-2">Jadwal yang sudah ada:</p>';
                        data.bookings.forEach(b => {
                            const statusColor = b.status === 'approved' ? 'text-emerald-600' : 'text-amber-600';
                            html += `<div class="flex items-center justify-between mt-1"><span class="text-xs text-slate-600">${b.start_time} - ${b.end_time}</span><span class="text-[10px] ${statusColor} font-medium">${b.purpose}</span></div>`;
                        });
                    }
                    html += '</div>';
                    result.innerHTML = html;
                    result.classList.remove('hidden');
                });
        }

        // Filter building -> update rooms
        document.getElementById('filter-building').addEventListener('change', function () {
            const building = this.value;
            const roomSelect = document.getElementById('filter-room');
            Array.from(roomSelect.options).forEach(opt => {
                if (!opt.value) return;
                opt.style.display = !building || opt.dataset.building === building ? '' : 'none';
            });
            roomSelect.value = '';
        });

        document.addEventListener('DOMContentLoaded', loadEvents);
    </script>
@endpush
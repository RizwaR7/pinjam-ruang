@extends(auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.user')

@section('content')
<div class="h-[calc(100vh-7rem)] flex flex-col gap-4 max-w-[1600px] mx-auto overflow-hidden" id="cal-root">

    {{-- ── TOP BAR ─────────────────────────────────────────── --}}
    <div class="flex items-center justify-between bg-gradient-to-r from-navy-700 via-navy-600 to-indigo-600 px-6 py-3.5 rounded-2xl shadow-lg shadow-navy-500/20 relative overflow-hidden">
        {{-- decorative blobs --}}
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-1/3 w-32 h-16 bg-indigo-400/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="flex items-center gap-3 relative z-10">
            <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur flex items-center justify-center border border-white/20 shadow-inner">
                <i data-feather="calendar" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h1 class="text-base font-black text-white tracking-tight leading-none">Kalender Ruangan</h1>
                <p class="text-[10px] text-navy-200 font-semibold uppercase tracking-widest mt-0.5">Real-time Monitoring</p>
            </div>
        </div>

        {{-- Month navigator --}}
        <div class="flex items-center gap-1 bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl p-1 relative z-10">
            <button onclick="prevMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg text-white/70 hover:bg-white/20 hover:text-white transition-all">
                <i data-feather="chevron-left" class="w-4 h-4"></i>
            </button>
            <div class="px-4 min-w-[150px] text-center">
                <span id="calendar-title" class="text-sm font-black text-white uppercase tracking-widest"></span>
            </div>
            <button onclick="nextMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg text-white/70 hover:bg-white/20 hover:text-white transition-all">
                <i data-feather="chevron-right" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Today button --}}
        <button onclick="goToday()" class="relative z-10 flex items-center gap-2 px-4 py-2 bg-white/15 hover:bg-white/25 border border-white/25 text-white text-xs font-bold rounded-xl transition-all">
            <i data-feather="target" class="w-3.5 h-3.5"></i> Hari Ini
        </button>
    </div>

    {{-- ── MAIN BODY ───────────────────────────────────────── --}}
    <div class="flex-1 grid grid-cols-1 xl:grid-cols-[280px_1fr] gap-4 min-h-0">

        {{-- ── SIDEBAR ──────────────────────────────────────── --}}
        <div class="flex flex-col gap-3 min-h-0">

            {{-- Filter Card --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-50 bg-gradient-to-r from-navy-50 to-indigo-50/50 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-navy-600 flex items-center justify-center">
                        <i data-feather="sliders" class="w-3 h-3 text-white"></i>
                    </div>
                    <span class="text-xs font-black text-navy-700 uppercase tracking-widest">Filter</span>
                </div>
                <div class="p-4 space-y-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Gedung</label>
                        <select id="filter-building" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-navy-400 focus:border-navy-400 outline-none transition-all">
                            <option value="">Semua Gedung</option>
                            @foreach($buildings as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ruangan</label>
                        <select id="filter-room" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-navy-400 focus:border-navy-400 outline-none transition-all">
                            <option value="">Semua Ruangan</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" data-building="{{ $room->building }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button onclick="loadEvents()" class="w-full py-2.5 bg-gradient-to-r from-navy-600 to-indigo-600 text-white text-xs font-black rounded-xl hover:from-navy-500 hover:to-indigo-500 transition-all shadow-md shadow-navy-500/20 flex items-center justify-center gap-2">
                        <i data-feather="refresh-cw" class="w-3.5 h-3.5"></i> Terapkan Filter
                    </button>
                </div>
            </div>

            {{-- Check Availability --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex-1 flex flex-col min-h-0">
                <div class="px-4 py-3 border-b border-slate-50 bg-gradient-to-r from-amber-50 to-yellow-50/50 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-amber-500 flex items-center justify-center">
                        <i data-feather="search" class="w-3 h-3 text-white"></i>
                    </div>
                    <span class="text-xs font-black text-amber-700 uppercase tracking-widest">Cek Ketersediaan</span>
                </div>
                <div class="p-4 space-y-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ruangan</label>
                        <select id="avail-room" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-amber-400 outline-none transition-all">
                            <option value="">Pilih Ruangan...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tanggal</label>
                        <input type="date" id="avail-date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-amber-400 outline-none transition-all">
                    </div>
                    <button onclick="checkAvailability()" class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-yellow-500 text-white text-xs font-black rounded-xl hover:from-amber-400 hover:to-yellow-400 transition-all shadow-md shadow-amber-500/20 flex items-center justify-center gap-2">
                        <i data-feather="zap" class="w-3.5 h-3.5"></i> Cek Sekarang
                    </button>
                </div>
                <div id="avail-result" class="mx-4 mb-4 hidden flex-1 overflow-y-auto rounded-xl border text-xs custom-scrollbar"></div>
            </div>

            {{-- Legend --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Keterangan</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-400 shadow-sm shadow-emerald-300 shrink-0"></span>
                        <span class="text-xs font-semibold text-slate-600">Disetujui / Terjadwal</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-amber-400 shadow-sm shadow-amber-300 shrink-0"></span>
                        <span class="text-xs font-semibold text-slate-600">Menunggu Persetujuan</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-navy-500 shadow-sm shadow-navy-300 shrink-0"></span>
                        <span class="text-xs font-semibold text-slate-600">Hari Ini</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="w-3 h-3 rounded-full bg-slate-200 shrink-0"></span>
                        <span class="text-xs font-semibold text-slate-400">Hari Lampau</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CALENDAR GRID ─────────────────────────────────── --}}
        <div class="flex flex-col min-h-0 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            {{-- Weekday header --}}
            <div class="grid grid-cols-7 border-b border-slate-100">
                @foreach([['Sen','Mon'],['Sel','Tue'],['Rab','Wed'],['Kam','Thu'],['Jum','Fri'],['Sab','Sat'],['Min','Sun']] as [$short, $en])
                    <div class="py-3 text-center @if($en === 'Sat' || $en === 'Sun') bg-slate-50/70 @endif">
                        <span class="text-[10px] font-black uppercase tracking-widest
                            @if($en === 'Sat') text-indigo-400
                            @elseif($en === 'Sun') text-rose-400
                            @else text-slate-400
                            @endif">{{ $short }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Calendar body (filled by JS) --}}
            <div id="calendar-body" class="flex-1 grid grid-cols-7 content-start min-h-0 overflow-hidden"></div>

            {{-- Footer: mini stats --}}
            <div class="border-t border-slate-100 bg-slate-50/50 px-6 py-2 flex items-center justify-between">
                <div class="flex items-center gap-4 text-[10px] font-bold text-slate-400">
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-400"></span>Disetujui: <span id="stat-approved" class="text-slate-600">-</span></span>
                    <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Pending: <span id="stat-pending" class="text-slate-600">-</span></span>
                </div>
                <div id="cal-loading" class="hidden items-center gap-2 text-[10px] font-bold text-navy-500">
                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Memuat...
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── EVENT MODAL ─────────────────────────────────────────── --}}
<div id="event-modal" class="fixed inset-0 z-[100] hidden" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4 pointer-events-none">
        <div class="relative pointer-events-auto w-full max-w-lg transform transition-all duration-200 scale-95 opacity-0" id="modal-card">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-navy-700 to-indigo-600 rounded-t-3xl px-6 py-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-black text-navy-200 uppercase tracking-widest mb-1">Agenda Penggunaan</p>
                        <h3 id="modal-date-title" class="text-xl font-black text-white tracking-tight"></h3>
                    </div>
                    <button onclick="closeModal()" class="w-8 h-8 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl flex items-center justify-center transition-all">
                        <i data-feather="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="mt-2 flex items-center gap-2" id="modal-badge-row"></div>
            </div>
            {{-- Modal Body --}}
            <div class="bg-white rounded-b-3xl border border-slate-100 shadow-2xl shadow-slate-300/40">
                <div id="modal-events-list" class="p-5 space-y-3 max-h-[55vh] overflow-y-auto custom-scrollbar"></div>
                <div class="px-5 pb-5 pt-2 border-t border-slate-50 flex justify-end">
                    <button onclick="closeModal()" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition-all">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    /* Calendar cell hover shimmer */
    #calendar-body > div {
        transition: background 0.15s ease, transform 0.1s ease;
    }
    #calendar-body > div:hover {
        z-index: 1;
    }

    /* Modal animation */
    #event-modal.open #modal-card {
        transform: scale(1);
        opacity: 1;
    }

    @keyframes fadeSlideIn {
        from { opacity: 0; transform: translateY(6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .event-item-anim {
        animation: fadeSlideIn 0.2s ease forwards;
    }
</style>

<script>
(function () {
    let currentDate = new Date();
    let events = [];
    const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    const weekdayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    /* ── Load events from API ──────────────────────── */
    window.loadEvents = function () {
        const yr = currentDate.getFullYear();
        const mo = currentDate.getMonth();
        const start = `${yr}-${String(mo + 1).padStart(2, '0')}-01`;
        const lastDay = new Date(yr, mo + 1, 0).getDate();
        const end   = `${yr}-${String(mo + 1).padStart(2, '0')}-${lastDay}`;

        let url = `{{ route('api.calendar-events') }}?start=${start}&end=${end}`;
        const building = document.getElementById('filter-building').value;
        const room     = document.getElementById('filter-room').value;
        if (building) url += `&building=${encodeURIComponent(building)}`;
        if (room)     url += `&room_id=${room}`;

        const loader = document.getElementById('cal-loading');
        loader.classList.remove('hidden');
        loader.classList.add('flex');

        fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } })
            .then(r => r.json())
            .then(data => { events = data; renderCalendar(); })
            .catch(() => { events = []; renderCalendar(); })
            .finally(() => { loader.classList.add('hidden'); loader.classList.remove('flex'); });
    };

    /* ── Render calendar grid ──────────────────────── */
    window.renderCalendar = function () {
        const yr = currentDate.getFullYear();
        const mo = currentDate.getMonth();
        document.getElementById('calendar-title').textContent = `${monthNames[mo]} ${yr}`;

        const firstDay  = new Date(yr, mo, 1).getDay();
        const lastDate  = new Date(yr, mo + 1, 0).getDate();
        const startDay  = firstDay === 0 ? 6 : firstDay - 1;
        const today     = new Date(); today.setHours(0,0,0,0);

        let approved = 0, pending = 0;

        let html = '';

        // Empty leading cells
        for (let i = 0; i < startDay; i++) {
            const colIdx = i % 7;
            const isWeekend = colIdx === 5 || colIdx === 6;
            html += `<div class="border-b border-r border-slate-50 min-h-[80px] ${isWeekend ? 'bg-slate-50/40' : 'bg-white/50'}"></div>`;
        }

        // Day cells
        for (let d = 1; d <= lastDate; d++) {
            const colIdx   = (startDay + d - 1) % 7;
            const isWeekend = colIdx === 5 || colIdx === 6;
            const cellDate = new Date(yr, mo, d);
            const dateStr  = `${yr}-${String(mo + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
            const dayEvents = events.filter(e => e.date === dateStr);
            const isToday  = today.getTime() === cellDate.getTime();
            const isPast   = cellDate < today && !isToday;

            dayEvents.forEach(e => { if (e.status === 'approved') approved++; else pending++; });

            const eventCount = dayEvents.length;
            const bgClass = isToday
                ? 'bg-navy-50/60 hover:bg-navy-50'
                : isPast
                    ? (isWeekend ? 'bg-slate-50/70' : 'bg-slate-50/30')
                    : (isWeekend ? 'bg-indigo-50/20 hover:bg-indigo-50/40' : 'hover:bg-slate-50/80');

            const dotsHtml = eventCount > 0 ? `
                <div class="flex gap-0.5 mt-0.5 flex-wrap">
                    ${dayEvents.slice(0, 4).map(e => `
                        <span class="w-1.5 h-1.5 rounded-full ${e.status === 'approved' ? 'bg-emerald-400' : 'bg-amber-400'}"></span>
                    `).join('')}
                    ${eventCount > 4 ? `<span class="text-[8px] font-black text-slate-300">+${eventCount - 4}</span>` : ''}
                </div>
            ` : '';

            const pillsHtml = dayEvents.slice(0, 2).map(ev => `
                <div class="flex items-center gap-1 px-1.5 py-[2px] text-[8px] font-bold truncate rounded-md
                    ${ev.status === 'approved'
                        ? 'bg-emerald-100 text-emerald-700 border border-emerald-200'
                        : 'bg-amber-100 text-amber-700 border border-amber-200'}">
                    <span class="w-1 h-1 rounded-full flex-shrink-0 ${ev.status === 'approved' ? 'bg-emerald-400' : 'bg-amber-400'}"></span>
                    <span class="truncate">${ev.start_time.substring(0,5)} ${ev.room_code || (ev.room || '').substring(0,6)}</span>
                </div>
            `).join('');

            const moreHtml = dayEvents.length > 2
                ? `<div class="text-[8px] font-black text-slate-400 mt-0.5 pl-0.5">+${dayEvents.length - 2} lainnya</div>`
                : '';

            html += `
            <div class="relative border-b border-r border-slate-100 min-h-[80px] p-1.5 cursor-pointer group transition-all ${bgClass}
                        ${isWeekend ? 'border-indigo-50' : ''}"
                 onclick="showDayEvents('${dateStr}')">

                <div class="flex items-start justify-between mb-1">
                    <span class="text-[11px] font-black transition-all
                        ${isToday
                            ? 'bg-gradient-to-br from-navy-600 to-indigo-600 text-white w-6 h-6 flex items-center justify-center rounded-lg shadow-md shadow-navy-400/30 text-[10px]'
                            : isPast
                                ? 'text-slate-300'
                                : isWeekend
                                    ? 'text-indigo-300 group-hover:text-indigo-500'
                                    : 'text-slate-400 group-hover:text-navy-600'}">
                        ${d}
                    </span>
                    ${eventCount > 0 ? `
                    <span class="text-[7px] font-black px-1.5 py-0.5 rounded-md
                        ${eventCount >= 3
                            ? 'bg-rose-100 text-rose-500 border border-rose-200'
                            : 'bg-slate-100 text-slate-400'}">
                        ${eventCount}
                    </span>` : ''}
                </div>

                <div class="space-y-0.5">
                    ${pillsHtml}
                    ${moreHtml}
                </div>
            </div>`;
        }

        // Trailing empty cells
        const totalCells = Math.ceil((startDay + lastDate) / 7) * 7;
        for (let i = startDay + lastDate; i < totalCells; i++) {
            const colIdx = i % 7;
            const isWeekend = colIdx === 5 || colIdx === 6;
            html += `<div class="border-b border-r border-slate-50 min-h-[80px] ${isWeekend ? 'bg-slate-50/40' : ''}"></div>`;
        }

        document.getElementById('calendar-body').innerHTML = html;
        document.getElementById('stat-approved').textContent = approved;
        document.getElementById('stat-pending').textContent = pending;
        if (typeof feather !== 'undefined') feather.replace();
    };

    /* ── Day events modal ──────────────────────────── */
    window.showDayEvents = function (dateStr) {
        const dayEvents = events.filter(e => e.date === dateStr);
        const d = new Date(dateStr + 'T00:00:00');
        const dayName = weekdayNames[d.getDay()];
        const dateDisplay = `${dayName}, ${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;

        document.getElementById('modal-date-title').textContent = dateDisplay;

        // Badge row
        const approvedCount = dayEvents.filter(e => e.status === 'approved').length;
        const pendingCount  = dayEvents.filter(e => e.status !== 'approved').length;
        let badgeHtml = '';
        if (approvedCount) badgeHtml += `<span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-1 bg-emerald-500/20 text-emerald-200 rounded-lg border border-emerald-400/30"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>${approvedCount} Disetujui</span>`;
        if (pendingCount)  badgeHtml += `<span class="inline-flex items-center gap-1 text-[9px] font-bold px-2 py-1 bg-amber-500/20 text-amber-200 rounded-lg border border-amber-400/30"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>${pendingCount} Pending</span>`;
        if (!dayEvents.length) badgeHtml = `<span class="text-[9px] font-bold text-navy-200">Tidak ada agenda</span>`;
        document.getElementById('modal-badge-row').innerHTML = badgeHtml;

        // Events list
        const list = document.getElementById('modal-events-list');
        if (dayEvents.length === 0) {
            list.innerHTML = `
            <div class="py-10 text-center">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-bold text-slate-400">Ruangan kosong di hari ini</p>
                <p class="text-[10px] text-slate-300 mt-1">Tidak ada jadwal peminjaman</p>
            </div>`;
        } else {
            list.innerHTML = dayEvents.map((ev, idx) => {
                const isApproved = ev.status === 'approved';
                const delay = idx * 40;
                return `
                <div class="event-item-anim group p-4 rounded-2xl border transition-all hover:shadow-md
                    ${isApproved
                        ? 'bg-emerald-50/50 border-emerald-100 hover:border-emerald-200 hover:bg-white'
                        : 'bg-amber-50/50 border-amber-100 hover:border-amber-200 hover:bg-white'}"
                    style="animation-delay: ${delay}ms">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-black text-slate-800 leading-tight truncate">${ev.room}</h4>
                            <p class="text-[10px] text-slate-400 font-mono mt-0.5">${ev.room_code || ''}</p>
                        </div>
                        <span class="shrink-0 inline-flex items-center gap-1 text-[9px] font-black px-2.5 py-1 rounded-xl border
                            ${isApproved
                                ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
                                : 'bg-amber-100 text-amber-700 border-amber-200'}">
                            <span class="w-1.5 h-1.5 rounded-full ${isApproved ? 'bg-emerald-500' : 'bg-amber-500'}"></span>
                            ${ev.status_label}
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-500 mb-3">
                        <div class="flex items-center gap-1.5 font-mono font-bold">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            ${ev.start_time} – ${ev.end_time}
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 pt-3 border-t ${isApproved ? 'border-emerald-100' : 'border-amber-100'}">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-navy-500 to-indigo-600 flex items-center justify-center text-[10px] font-black text-white shadow-sm shrink-0">
                            ${(ev.user || '?').charAt(0).toUpperCase()}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-bold text-slate-700 truncate">${ev.title || ev.user}</p>
                            <p class="text-[9px] text-slate-400 truncate">${ev.user}</p>
                        </div>
                    </div>
                </div>`;
            }).join('');
        }

        const modal = document.getElementById('event-modal');
        const card  = document.getElementById('modal-card');
        modal.classList.remove('hidden');
        modal.classList.add('open');
        setTimeout(() => { card.style.transform = 'scale(1)'; card.style.opacity = '1'; }, 10);
        document.body.style.overflow = 'hidden';
        if (typeof feather !== 'undefined') feather.replace();
    };

    window.closeModal = function () {
        const modal = document.getElementById('event-modal');
        const card  = document.getElementById('modal-card');
        card.style.transform = 'scale(0.95)';
        card.style.opacity = '0';
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }, 180);
    };

    window.prevMonth = function () { currentDate.setMonth(currentDate.getMonth() - 1); loadEvents(); };
    window.nextMonth = function () { currentDate.setMonth(currentDate.getMonth() + 1); loadEvents(); };
    window.goToday   = function () { currentDate = new Date(); loadEvents(); };

    /* ── Check availability ────────────────────────── */
    window.checkAvailability = function () {
        const roomId = document.getElementById('avail-room').value;
        const date   = document.getElementById('avail-date').value;
        const result = document.getElementById('avail-result');
        if (!roomId || !date) return;

        result.innerHTML = '<div class="p-4 text-center text-[10px] font-bold text-slate-400">Memuat...</div>';
        result.classList.remove('hidden');

        fetch(`{{ route('api.room-availability') }}?room_id=${roomId}&date=${date}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
        })
        .then(r => r.json())
        .then(data => {
            if (data.bookings.length === 0) {
                result.innerHTML = `
                <div class="p-4 text-center border border-emerald-100 bg-emerald-50 rounded-xl">
                    <span class="text-2xl">✅</span>
                    <p class="text-xs font-black text-emerald-600 mt-1">Tersedia!</p>
                    <p class="text-[9px] text-emerald-500 mt-0.5">Tidak ada peminjaman di tanggal ini.</p>
                </div>`;
            } else {
                const rows = data.bookings.map(b => `
                    <div class="flex items-center justify-between py-2 border-b border-amber-100 last:border-0">
                        <span class="font-mono text-[9px] font-bold text-slate-600">${b.start_time}–${b.end_time}</span>
                        <span class="text-[9px] text-amber-600 font-semibold truncate max-w-[100px]">${b.purpose}</span>
                    </div>`).join('');
                result.innerHTML = `
                <div class="p-3 border border-amber-100 bg-amber-50 rounded-xl">
                    <p class="text-[9px] font-black text-amber-600 uppercase tracking-wide mb-2">⚠️ Ada ${data.bookings.length} Peminjaman</p>
                    ${rows}
                </div>`;
            }
        })
        .catch(() => {
            result.innerHTML = '<div class="p-3 text-[9px] font-bold text-rose-500 bg-rose-50 rounded-xl border border-rose-100">Gagal memuat data.</div>';
        });
    };

    /* ── Filter building cascades to room list ──────────────── */
    document.getElementById('filter-building').addEventListener('change', function () {
        const val = this.value;
        document.querySelectorAll('#filter-room option').forEach(opt => {
            if (!opt.value) return;
            opt.style.display = (!val || opt.dataset.building === val) ? '' : 'none';
        });
        document.getElementById('filter-room').value = '';
    });

    /* ── Keyboard ESC to close modal ──────── */
    document.addEventListener('keydown', e => { if (e.key === 'Escape') window.closeModal(); });

    loadEvents();
    if (typeof feather !== 'undefined') feather.replace();
})();
</script>
@endsection
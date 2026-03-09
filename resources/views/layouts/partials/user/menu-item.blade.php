@if($menu->children && $menu->children->count() > 0)
    @php
        $isActiveParent = false;
        foreach ($menu->children as $child) {
            if ($child->route_name && request()->routeIs($child->route_name . '*')) {
                $isActiveParent = true;
                break;
            }
        }
    @endphp
    <div x-data="{ open: {{ $isActiveParent ? 'true' : 'false' }} }">
        <button @click="open = !open"
            class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium transition-all group {{ $isActiveParent ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
            <span class="flex items-center gap-3">
                @if($menu->icon)
                    <i data-feather="{{ $menu->icon }}"
                        class="w-[18px] h-[18px] {{ $isActiveParent ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                @endif
                <span>{{ $menu->name }}</span>
            </span>
            <svg class="w-4 h-4 transition-transform duration-300 {{ $isActiveParent ? 'text-blue-500' : 'text-slate-400' }}"
                :class="open && 'rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <div x-show="open" x-collapse.duration.300ms class="ml-4 mt-1 space-y-1 border-l-2 border-slate-100 pl-3">
            @foreach($menu->children as $child)
                @include('layouts.partials.user.menu-item', ['menu' => $child])
            @endforeach
        </div>
    </div>
@else
    @php
        $href = '#';
        $isActive = false;
        if ($menu->route_name && \Illuminate\Support\Facades\Route::has($menu->route_name)) {
            $href = route($menu->route_name);
            $isActive = request()->routeIs($menu->route_name . '*');
        } elseif ($menu->url ?? false) {
            $href = $menu->url;
        }
    @endphp
    <a href="{{ $href }}"
        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all {{ $isActive ? 'text-blue-700 bg-blue-50 font-bold shadow-sm shadow-blue-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        @if($menu->icon)
            <i data-feather="{{ $menu->icon }}"
                class="w-[18px] h-[18px] {{ $isActive ? 'text-blue-600' : 'text-slate-400' }}"></i>
        @else
            <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-blue-500' : 'bg-slate-300' }}"></span>
        @endif
        <span>{{ $menu->name }}</span>
    </a>
@endif
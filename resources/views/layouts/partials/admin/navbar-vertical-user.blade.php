<!-- User Sidebar -->
<aside
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transform transition-transform duration-300 lg:translate-x-0 shadow-xl lg:shadow-none"
    :class="mobileMenu ? 'translate-x-0' : '-translate-x-full'" @click.outside="mobileMenu = false">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-6 h-16 border-b border-slate-100">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-600 text-white font-bold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                </path>
            </svg>
        </div>
        <span class="text-lg font-extrabold text-slate-800 tracking-tight">SIRUANG</span>
        <span class="ml-auto text-[10px] font-bold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">USER</span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        @foreach(($dynamicMenus ?? collect()) as $menu)
            @include('layouts.partials.user.menu-item', ['menu' => $menu])
        @endforeach
    </nav>
</aside>

<!-- Mobile overlay -->
<div x-show="mobileMenu" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
    @click="mobileMenu = false"></div>
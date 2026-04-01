<!-- Admin Sidebar -->
<aside
    class="fixed inset-y-0 left-0 z-50 w-64 bg-navy-900 transform transition-transform duration-300 lg:translate-x-0 shadow-xl lg:shadow-none"
    :class="mobileMenu ? 'translate-x-0' : '-translate-x-full'" @click.outside="mobileMenu = false">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-6 h-16 border-b border-navy-400/30">
        <div class="w-8 h-8 rounded-lg flex items-center justify-center">
            @include('layouts.partials.admin.logo')
        </div>
        <span class="text-lg font-extrabold text-white tracking-tight">SIRUANG</span>
        <span class="ml-auto text-[10px] font-bold bg-gold-500 text-navy-900 px-1.5 py-0.5 rounded">ADMIN</span>
    </div>

    <!-- Search & Nav Container -->
    <div class="flex flex-col flex-1 h-[calc(100%-4rem)]" x-data="{ searchQuery: '' }">
        <!-- Search Input -->
        <div class="px-4 py-3 border-b border-navy-400/30">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-navy-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="searchQuery" placeholder="Cari menu..."
                    class="w-full bg-navy-800/50 border border-navy-400/30 text-white text-sm rounded-lg focus:ring-gold-400 focus:border-gold-400 block pl-9 p-2 placeholder-navy-200">
            </div>
        </div>

        <!-- Nav -->
        <nav id="sidebar-menu" class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            @foreach(($dynamicMenus ?? collect()) as $menu)
                @include('layouts.partials.admin.menu-item', ['menu' => $menu])
            @endforeach
        </nav>
    </div>
</aside>

<!-- Mobile overlay -->
<div x-show="mobileMenu" x-transition.opacity class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
    @click="mobileMenu = false"></div>
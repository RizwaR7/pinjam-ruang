<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIPERA') }} — Admin</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin_assets/images/favicon/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Fallback Fade Out Class */
        .page-fade-out {
            opacity: 0;
            transition: opacity 0.25s ease-out;
        }

        /* Prevent initial load animations from firing on HTMX swap */
        body.htmx-swapped .,
        body.htmx-added .,
        .htmx-added .,
        .htmx-added. {
            animation: none !important;
            opacity: 1 !important;
        }

        /* HTMX Page Transition (Main Content) */
        #main-content {
            transition: opacity 250ms ease-out, transform 250ms ease-out;
        }

        #main-content.htmx-swapping {
            opacity: 0;
            transform: translateY(-20px);
        }

        #main-content.htmx-added {
            opacity: 0;
            transform: translateY(20px);
        }

        /* HTMX Table Transition (Component Content) */
        .table-transition {
            transition: opacity 200ms ease-out, transform 200ms ease-out;
        }

        .table-transition.htmx-swapping {
            opacity: 0;
            transform: scale(0.98);
        }

        .table-transition.htmx-added {
            opacity: 0;
            transform: scale(0.98);
        }

        /* Global Scrollbar — Firefox */
        * {
            scrollbar-width: thin;
            scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
        }

        /* Global Scrollbar — Webkit (Chrome, Edge, Safari) */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.4);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background-color: rgba(100, 116, 139, 0.6);
        }
    </style>
</head>

<body class="bg-slate-50 font-sans h-full antialiased" x-data="{ sidebarOpen: true, mobileMenu: false }" hx-boost="true"
    hx-target="#main-content" hx-select="#main-content" hx-swap="innerHTML swap:250ms settle:250ms"
    hx-indicator="#global-htmx-indicator">

    <!-- Subtle HTMX progress bar (active during ajax requests) -->
    <div id="global-htmx-indicator"
        class="htmx-indicator fixed top-0 w-full h-1 bg-gradient-to-r from-purple-500 via-fuchsia-500 to-purple-500 z-[9999]"
        style="transition: opacity 200ms ease-in;"></div>

    <div class="flex h-full">
        <!-- Sidebar -->
        @include('layouts.partials.admin.navbar-vertical-admin')

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-h-screen" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">
            @include('layouts.partials.admin.header')

            <main id="main-content" class="flex-1 p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => feather.replace());

        // Disable initial load animations after first HTMX navigation
        document.body.addEventListener('htmx:beforeRequest', function (evt) {
            if (!document.getElementById('htmx-disable-animations')) {
                var style = document.createElement('style');
                style.id = 'htmx-disable-animations';
                style.innerHTML = '. { animation: none !important; opacity: 1 !important; transform: none !important; }';
                document.head.appendChild(style);
            }
        });

        // Re-initialize feather icons + update sidebar active states after HTMX swap
        document.body.addEventListener('htmx:afterSettle', function (evt) {
            if (typeof feather !== "undefined") {
                feather.replace();
            }
            updateSidebarActiveState();
        });

        // Client-side sidebar active state tracking
        function updateSidebarActiveState() {
            var currentPath = window.location.pathname;
            var nav = document.getElementById('sidebar-menu');
            if (!nav) return;

            // Active classes for child/single items
            var activeClasses = ['text-gold-400', 'bg-navy-800/50'];
            var inactiveClasses = ['text-navy-100', 'hover:text-white', 'hover:bg-navy-400/40'];

            // Active classes for parent items
            var parentActiveClasses = ['bg-navy-700/50', 'text-gold-400'];
            var parentInactiveClasses = ['text-navy-100', 'hover:text-white', 'hover:bg-navy-400/40'];

            // Reset all menu links
            nav.querySelectorAll('a[href]').forEach(function (link) {
                activeClasses.forEach(function (cls) { link.classList.remove(cls); });
                inactiveClasses.forEach(function (cls) { link.classList.add(cls); });

                // feather.replace() converts <i> to <svg class="feather ...">
                var icon = link.querySelector('svg.feather');
                if (icon) {
                    icon.classList.remove('text-gold-400');
                    icon.classList.add('text-navy-200');
                }
                var dot = link.querySelector('span.rounded-full');
                if (dot) {
                    dot.classList.remove('bg-gold-400');
                    dot.classList.add('bg-navy-300');
                }
            });

            // Reset all parent buttons
            nav.querySelectorAll('button').forEach(function (btn) {
                parentActiveClasses.forEach(function (cls) { btn.classList.remove(cls); });
                parentInactiveClasses.forEach(function (cls) { btn.classList.add(cls); });

                var icon = btn.querySelector('svg.feather');
                if (icon) {
                    icon.classList.remove('text-gold-400');
                    icon.classList.add('text-navy-200', 'group-hover:text-gold-400');
                }
                var chevron = btn.querySelector('svg:not(.feather)');
                if (chevron) {
                    chevron.classList.remove('text-gold-400');
                    chevron.classList.add('text-navy-300');
                }
            });

            // Find the BEST matching link (most specific / longest path wins)
            var bestMatch = null;
            var bestMatchLen = 0;
            nav.querySelectorAll('a[href]').forEach(function (link) {
                var linkPath = new URL(link.href, window.location.origin).pathname;
                if (currentPath === linkPath) {
                    if (linkPath.length > bestMatchLen) {
                        bestMatch = link;
                        bestMatchLen = linkPath.length;
                    }
                } else if (linkPath !== '/admin/home' && currentPath.startsWith(linkPath) && linkPath.length > bestMatchLen) {
                    bestMatch = link;
                    bestMatchLen = linkPath.length;
                }
            });

            // Highlight only the best match
            if (bestMatch) {
                var link = bestMatch;
                inactiveClasses.forEach(function (cls) { link.classList.remove(cls); });
                activeClasses.forEach(function (cls) { link.classList.add(cls); });

                var icon = link.querySelector('svg.feather');
                if (icon) {
                    icon.classList.remove('text-navy-200');
                    icon.classList.add('text-gold-400');
                }
                var dot = link.querySelector('span.rounded-full');
                if (dot) {
                    dot.classList.remove('bg-navy-300');
                    dot.classList.add('bg-gold-400');
                }

                // Highlight the parent button if this is a child item
                var parentDiv = link.closest('div[x-data]');
                if (parentDiv) {
                    var parentBtn = parentDiv.querySelector(':scope > button');
                    if (parentBtn) {
                        parentInactiveClasses.forEach(function (cls) { parentBtn.classList.remove(cls); });
                        parentActiveClasses.forEach(function (cls) { parentBtn.classList.add(cls); });

                        var parentIcon = parentBtn.querySelector('svg.feather');
                        if (parentIcon) {
                            parentIcon.classList.remove('text-navy-200', 'group-hover:text-gold-400');
                            parentIcon.classList.add('text-gold-400');
                        }
                        var parentChevron = parentBtn.querySelector('svg:not(.feather)');
                        if (parentChevron) {
                            parentChevron.classList.remove('text-navy-300');
                            parentChevron.classList.add('text-gold-400');
                        }
                    }
                }
            }
        }
    </script>
    @stack('scripts')

    <script>
        (function () {
            var _searchInput = document.getElementById('menu-search');
            var _sidebarMenu = document.getElementById('sidebar-menu');

            if (_searchInput) {
                var debounceTimer;

                _searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);

                    debounceTimer = setTimeout(function () {
                        var query = _searchInput.value.trim();

                        fetch("{{ route('admin.menus.search') }}?q=" + query)
                            .then(function (res) { return res.text(); })
                            .then(function (data) {
                                if (_sidebarMenu) {
                                    _sidebarMenu.innerHTML = data;
                                }
                                if (typeof feather !== "undefined") {
                                    feather.replace();
                                }
                            });
                    }, 300);
                });
            }
        })();
    </script>

</body>

</html>
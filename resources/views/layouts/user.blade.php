<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIPERA') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('admin_assets/images/favicon/favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans h-full antialiased" x-data="{ sidebarOpen: true, mobileMenu: false }">
    <div class="flex h-full">
        <!-- Sidebar -->
        @include('layouts.partials.admin.navbar-vertical-user')

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
            :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">
            @include('layouts.partials.admin.header')

            <main class="flex-1 p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Feather Icons -->
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script>document.addEventListener('DOMContentLoaded', () => feather.replace());</script>
    @stack('scripts')
</body>

</html>
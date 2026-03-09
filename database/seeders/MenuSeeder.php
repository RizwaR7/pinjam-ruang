<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // ──────────────────────────────────────────────
        //  ADMIN MENUS
        // ──────────────────────────────────────────────

        $adminDashboard = Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => null, 'name' => 'Dashboard'],
            ['route_name' => 'admin.home', 'icon' => 'home', 'sort_order' => 0, 'is_active' => true]
        );

        // --- Manage Front End ---
        $front = Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => null, 'name' => 'Manage Front End'],
            ['route_name' => null, 'icon' => 'layers', 'sort_order' => 10, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => $front->id, 'name' => 'Menu'],
            ['route_name' => 'admin.menus.index', 'icon' => null, 'sort_order' => 0, 'is_active' => true]
        );

        // --- Manage Users ---
        $users = Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => null, 'name' => 'Kelola Pengguna'],
            ['route_name' => null, 'icon' => 'users', 'sort_order' => 20, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => $users->id, 'name' => 'Profile'],
            ['route_name' => 'admin.profile.edit', 'icon' => null, 'sort_order' => 0, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => $users->id, 'name' => 'Kelola User'],
            ['route_name' => 'admin.users.index', 'icon' => 'user-plus', 'sort_order' => 1, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => $users->id, 'name' => 'Role Management'],
            ['route_name' => 'admin.roles.index', 'icon' => 'shield', 'sort_order' => 2, 'is_active' => true]
        );

        // --- Master Data ---
        $masterData = Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => null, 'name' => 'Master Data'],
            ['route_name' => null, 'icon' => 'database', 'sort_order' => 30, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => $masterData->id, 'name' => 'Manajemen Ruangan'],
            ['route_name' => 'admin.rooms.index', 'icon' => 'map-pin', 'sort_order' => 0, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => $masterData->id, 'name' => 'Manajemen Fasilitas'],
            ['route_name' => 'admin.equipment.index', 'icon' => 'tool', 'sort_order' => 1, 'is_active' => true]
        );

        // --- Peminjaman ---
        $bookingParent = Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => null, 'name' => 'Peminjaman'],
            ['route_name' => null, 'icon' => 'calendar', 'sort_order' => 40, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => $bookingParent->id, 'name' => 'Approval Peminjaman'],
            ['route_name' => 'admin.bookings.index', 'icon' => 'check-square', 'sort_order' => 0, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'admin', 'parent_id' => $bookingParent->id, 'name' => 'Jadwal Ruangan'],
            ['route_name' => 'admin.calendar.index', 'icon' => 'clock', 'sort_order' => 1, 'is_active' => true]
        );

        // ──────────────────────────────────────────────
        //  USER MENUS
        // ──────────────────────────────────────────────

        $userDashboard = Menu::updateOrCreate(
            ['context' => 'user', 'parent_id' => null, 'name' => 'Dashboard'],
            ['route_name' => 'home', 'icon' => 'home', 'sort_order' => 0, 'is_active' => true]
        );

        // --- Peminjaman Ruangan ---
        $userBooking = Menu::updateOrCreate(
            ['context' => 'user', 'parent_id' => null, 'name' => 'Peminjaman Ruangan'],
            ['route_name' => null, 'icon' => 'calendar', 'sort_order' => 10, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'user', 'parent_id' => $userBooking->id, 'name' => 'Ajukan Peminjaman'],
            ['route_name' => 'bookings.create', 'icon' => 'plus-circle', 'sort_order' => 0, 'is_active' => true]
        );

        Menu::updateOrCreate(
            ['context' => 'user', 'parent_id' => $userBooking->id, 'name' => 'Riwayat Peminjaman'],
            ['route_name' => 'bookings.index', 'icon' => 'list', 'sort_order' => 1, 'is_active' => true]
        );

        // --- Jadwal & Kalender ---
        $userCalendar = Menu::updateOrCreate(
            ['context' => 'user', 'parent_id' => null, 'name' => 'Jadwal & Kalender'],
            ['route_name' => 'calendar.index', 'icon' => 'clock', 'sort_order' => 20, 'is_active' => true]
        );

        // --- Notifikasi ---
        $userNotif = Menu::updateOrCreate(
            ['context' => 'user', 'parent_id' => null, 'name' => 'Notifikasi'],
            ['route_name' => 'notifications.index', 'icon' => 'bell', 'sort_order' => 30, 'is_active' => true]
        );

        // ──────────────────────────────────────────────
        //  ROLE ASSIGNMENTS
        // ──────────────────────────────────────────────

        $adminMenuIds = Menu::where('context', 'admin')->pluck('id')->all();
        $userMenuIds = Menu::where('context', 'user')->pluck('id')->all();

        // 1. Pengelola Sistem (Super Admin): Semua admin menus
        $pengelolaSistem = Role::where('slug', 'pengelola_sistem')->first();
        $pengelolaSistem?->menus()->sync($adminMenuIds);

        // 2. Pengelola Gedung: Dashboard, Profile, Master Data (Ruangan, Fasilitas), Peminjaman (Approval, Jadwal)
        $pengelolaGedungMenus = Menu::where('context', 'admin')
            ->whereIn('name', [
                'Dashboard',
                'Kelola Pengguna',
                'Profile',
                'Master Data',
                'Manajemen Ruangan',
                'Manajemen Fasilitas',
                'Peminjaman',
                'Approval Peminjaman',
                'Jadwal Ruangan',
            ])->pluck('id')->all();
        $pengelolaGedung = Role::where('slug', 'pengelola_gedung')->first();
        $pengelolaGedung?->menus()->sync($pengelolaGedungMenus);

        // 3. Peminjam: All user menus
        $peminjam = Role::where('slug', 'peminjam')->first();
        $peminjam?->menus()->sync($userMenuIds);
    }
}
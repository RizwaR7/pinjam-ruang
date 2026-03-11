<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\MenuController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

// ── Admin Login Route ─────────────────────────────────────
Route::get('login/admin', [\App\Http\Controllers\Auth\LoginController::class, 'showAdminLoginForm'])->name('admin.login');

Auth::routes();

// ──────────────────────────────────────────────────────────
//  PEMINJAM ROUTES  (peminjam — dosen, mahasiswa, ormawa, tamu)
// ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'user-role:peminjam'])->group(function () {
    Route::get('/home', [HomeController::class, 'userHome'])->name('home');

    // ── Peminjaman Ruangan ──────────────────────────────
    Route::get('/bookings', [App\Http\Controllers\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [App\Http\Controllers\BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/request-return', [App\Http\Controllers\BookingController::class, 'requestReturn'])->name('bookings.request-return');
    Route::post('/bookings/{booking}/pay-fine', [App\Http\Controllers\BookingController::class, 'uploadFinePayment'])->name('bookings.pay-fine');

    // ── Kalender (User) ─────────────────────────────────
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

    // ── Notifikasi ──────────────────────────────────────
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// ──────────────────────────────────────────────────────────
//  ADMIN ROUTES  (pengelola_sistem, pengelola_gedung)
// ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'user-role:pengelola_sistem,pengelola_gedung'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/home', [HomeController::class, 'adminHome'])->name('home');

    // ── Role Management ───────────────────────────────
    Route::resource('roles', App\Http\Controllers\RoleController::class);

    // ── Menu Management ───────────────────────────────
    Route::post('menus/reorder', [MenuController::class, 'reorder'])->name('menus.reorder');
    Route::resource('menus', MenuController::class);

    // ── Profile ───────────────────────────────────────
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

    // ── Room Management ───────────────────────────────
    Route::resource('rooms', App\Http\Controllers\Admin\RoomController::class);

    // ── User Management ───────────────────────────────
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::patch('/users/{user}/reset-password', [App\Http\Controllers\Admin\UserController::class, 'resetPassword'])->name('users.reset-password');

    // ── Equipment / Facility Management ───────────────
    Route::resource('equipment', App\Http\Controllers\Admin\EquipmentController::class);

    // ── Booking Approval ──────────────────────────────
    Route::get('/bookings', [App\Http\Controllers\Admin\BookingApprovalController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [App\Http\Controllers\Admin\BookingApprovalController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/approve', [App\Http\Controllers\Admin\BookingApprovalController::class, 'approve'])->name('bookings.approve');
    Route::patch('/bookings/{booking}/reject', [App\Http\Controllers\Admin\BookingApprovalController::class, 'reject'])->name('bookings.reject');
    Route::patch('/bookings/{booking}/confirm-return', [App\Http\Controllers\Admin\BookingApprovalController::class, 'confirmReturn'])->name('bookings.confirm-return');

    // ── Settings ──────────────────────────────────────────
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

    // ── Fine Payments ─────────────────────────────────────
    Route::get('/fine-payments', [App\Http\Controllers\Admin\FinePaymentController::class, 'index'])->name('fine-payments.index');
    Route::patch('/fine-payments/{finePayment}/verify', [App\Http\Controllers\Admin\FinePaymentController::class, 'verify'])->name('fine-payments.verify');
    Route::patch('/fine-payments/{finePayment}/reject', [App\Http\Controllers\Admin\FinePaymentController::class, 'reject'])->name('fine-payments.reject');

    // ── Kalender (Admin) ──────────────────────────────
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

    // ── Notifikasi (Admin) ────────────────────────────
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');

    // ── Menu Search ───────────────────────────────────
    Route::get('/menu-search', [MenuController::class, 'search'])->name('menus.search');
});

// ── API: Calendar Events (shared) ─────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/api/calendar-events', [App\Http\Controllers\CalendarController::class, 'events'])->name('api.calendar-events');
    Route::get('/api/room-availability', [App\Http\Controllers\CalendarController::class, 'checkAvailability'])->name('api.room-availability');
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');
});

# 📋 Dokumentasi Proyek: Web App Peminjaman Ruangan (web-pin-ruang)

> **Repository:** [https://github.com/coatalter/web-pin-ruang](https://github.com/coatalter/web-pin-ruang)
> **Framework:** Laravel 10 · PHP 8.x · Bootstrap 5 / Tailwind CSS · Vite
> **Tanggal Mulai:** 23 Februari 2026

---

## 📖 Daftar Isi

1. [Ringkasan Proyek](#-ringkasan-proyek)
2. [Tech Stack](#-tech-stack)
3. [Struktur Direktori Utama](#-struktur-direktori-utama)
4. [Database Schema (ERD)](#-database-schema-erd)
5. [Alur Aplikasi (App Flow)](#-alur-aplikasi-app-flow)
6. [Fitur yang Sudah Diimplementasi](#-fitur-yang-sudah-diimplementasi)
7. [Daftar File Kunci per Fitur](#-daftar-file-kunci-per-fitur)
8. [Status MVP (Minimum Viable Product)](#-status-mvp-minimum-viable-product)
9. [Riwayat Commit (Git History)](#-riwayat-commit-git-history)
10. [Cara Setup & Menjalankan](#-cara-setup--menjalankan)

---

## 🎯 Ringkasan Proyek

Aplikasi web untuk **peminjaman ruangan** di lingkungan kampus (Universitas & Fakultas). Sistem ini dirancang agar setiap pengguna mendapatkan pengalaman yang disesuaikan dengan wewenangnya melalui:

- **Dynamic Role Management** — Role bisa ditambah/diubah/dihapus langsung dari admin panel.
- **Dynamic Menu System** — Menu sidebar di-render dari database berdasarkan role pengguna, bukan hardcode.
- **Scope Pemisahan Fakultas & Universitas** — Arsitektur mendukung pemisahan hak akses berdasarkan tingkat organisasi.

---

## 🛠 Tech Stack

| Layer | Teknologi |
|---|---|
| **Backend Framework** | Laravel 10 |
| **Frontend** | Blade Templates, Bootstrap 5 (Admin), Tailwind CSS (Role Views) |
| **Build Tool** | Vite |
| **Database** | MySQL |
| **Authentication** | Laravel Auth Scaffolding (`Auth::routes()`) |
| **Authorization** | Custom `UserRoleMiddleware` + Dynamic Menu |
| **Icons** | Feather Icons |
| **API Tokens** | Laravel Sanctum |

---

## 📁 Struktur Direktori Utama

```
web-pin-ruang-1/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── MenuController.php      ← CRUD Dynamic Menu
│   │   │   │   └── ProfileController.php   ← Edit profil & avatar admin
│   │   │   ├── Auth/                       ← Login, Register, Reset Password (Laravel scaffold)
│   │   │   ├── HomeController.php          ← Dashboard admin & user
│   │   │   └── RoleController.php          ← CRUD Role Management
│   │   └── Middleware/
│   │       └── UserRoleMiddleware.php       ← Cek role user pada setiap request
│   ├── Models/
│   │   ├── Menu.php                         ← Model menu dinamis (self-referencing parent/child)
│   │   ├── Role.php                         ← Model role (relasi: users, menus)
│   │   └── User.php                         ← Model user (relasi: role)
│   └── Providers/
│       └── AppServiceProvider.php           ← View Composer: inject dynamicMenus ke sidebar
│
├── database/
│   ├── migrations/
│   │   ├── create_roles_table               ← Tabel roles (name, slug, description)
│   │   ├── create_menus_table               ← Tabel menus + pivot menu_role
│   │   ├── add_role_id_to_users_table       ← Migrasi data role lama ke foreign key
│   │   ├── move_legacy_role_column          ← Backup & hapus kolom role lama
│   │   └── add_avatar_to_users_table        ← Kolom avatar untuk profil
│   └── seeders/
│       ├── RoleSeeder.php                   ← 16 role bawaan (Admin → Rektor)
│       ├── UserSeeder.php                   ← 2 user default (admin & user)
│       ├── MenuSeeder.php                   ← Menu dinamis (Admin Dashboard, Manage Users, dll)
│       └── DatabaseSeeder.php               ← Orkestrasi seeder
│
├── resources/views/
│   ├── admin/
│   │   ├── home.blade.php                   ← Dashboard admin
│   │   ├── roles/ (index, create, edit, show)  ← UI CRUD Role
│   │   ├── menus/ (index, create, edit)     ← UI CRUD Menu
│   │   └── profile/edit.blade.php           ← Halaman edit profil admin
│   ├── auth/ (login, register, verify, passwords/)  ← Halaman autentikasi
│   ├── layouts/
│   │   ├── admin.blade.php                  ← Master layout admin
│   │   ├── user.blade.php                   ← Master layout user
│   │   └── partials/admin/
│   │       ├── navbar-vertical-admin.blade.php  ← Sidebar dinamis (render dari $dynamicMenus)
│   │       ├── menu-item.blade.php          ← Komponen rekursif menu item
│   │       ├── header.blade.php             ← Top navbar
│   │       └── head.blade.php / scripts.blade.php
│   └── user/home.blade.php                 ← Dashboard user biasa
│
└── routes/
    └── web.php                              ← Routing utama (auth, user, admin groups)
```

---

## 🗄 Database Schema (ERD)

```
┌─────────────────┐       ┌──────────────────┐       ┌──────────────────┐
│      users      │       │      roles       │       │      menus       │
├─────────────────┤       ├──────────────────┤       ├──────────────────┤
│ id              │       │ id               │       │ id               │
│ name            │       │ name             │       │ context (admin/  │
│ email           │       │ slug (unique)    │       │          user)   │
│ password        │  FK   │ description      │       │ parent_id (FK→   │
│ role_id ────────┼──────→│ created_at       │       │         menus.id)│
│ avatar          │       │ updated_at       │       │ name             │
│ role_legacy     │       └────────┬─────────┘       │ route_name       │
│ email_verified  │                │                  │ url              │
│ created_at      │                │                  │ icon             │
│ updated_at      │                │ M:N              │ sort_order       │
└─────────────────┘                │                  │ is_active        │
                          ┌────────┴─────────┐       │ created_at       │
                          │    menu_role     │       │ updated_at       │
                          ├──────────────────┤       └────────┬─────────┘
                          │ menu_id (FK) ────┼───────────────→│
                          │ role_id (FK) ────┼───────→ roles  │
                          └──────────────────┘                │
                                                              │
                                            Self-Referencing  │
                                            (parent_id → id) ─┘
```

### Relasi Antar Tabel

| Relasi | Tipe | Deskripsi |
|---|---|---|
| `User → Role` | **BelongsTo** | Setiap user memiliki 1 role |
| `Role → Users` | **HasMany** | Satu role bisa dimiliki banyak user |
| `Role ↔ Menu` | **BelongsToMany** | Pivot table `menu_role`. Role menentukan menu apa yang tampil |
| `Menu → Children` | **HasMany (self)** | Menu parent memiliki submenu children |
| `Menu → Parent` | **BelongsTo (self)** | Submenu mengacu ke parent menu |

---

## 🔄 Alur Aplikasi (App Flow)

```
┌────────────────────────────────────────────────────────────────┐
│  1. AUTHENTICATION                                             │
│  ┌──────────┐    ┌───────────┐    ┌──────────────────┐        │
│  │  Login   │───→│ Verifikasi│───→│ Cek Role (slug)  │        │
│  │  Page    │    │ Kredensial│    │ via role_id FK    │        │
│  └──────────┘    └───────────┘    └────────┬─────────┘        │
│                                            │                   │
│  2. ROLE & MENU RESOLUTION                 ▼                   │
│  ┌─────────────────────────────────────────────────────┐      │
│  │  AppServiceProvider → View Composer                  │      │
│  │  ┌─────────────────────────────────────────────┐    │      │
│  │  │  Fetch Menu dari DB berdasarkan:            │    │      │
│  │  │  • context ('admin' atau 'user')            │    │      │
│  │  │  • role_id user yang login                  │    │      │
│  │  │  • is_active = true                         │    │      │
│  │  │  • whereHas('roles', role_id)               │    │      │
│  │  └──────────────────────┬──────────────────────┘    │      │
│  │                         │                            │      │
│  │         Render sidebar secara dinamis                │      │
│  │  ┌──────────────────────▼──────────────────────┐    │      │
│  │  │  navbar-vertical-admin.blade.php            │    │      │
│  │  │  → Foreach $dynamicMenus                    │    │      │
│  │  │    → @include menu-item (recursive)         │    │      │
│  │  └─────────────────────────────────────────────┘    │      │
│  └─────────────────────────────────────────────────────┘      │
│                                                                │
│  3. MIDDLEWARE PROTECTION                                      │
│  ┌─────────────────────────────────────────────────────┐      │
│  │  UserRoleMiddleware('user-role:admin')               │      │
│  │  • Cek Auth::check()                                │      │
│  │  • Cek Auth::user()->role->slug === 'admin'         │      │
│  │  • Jika cocok → lanjut | Jika tidak → 403 JSON     │      │
│  └─────────────────────────────────────────────────────┘      │
│                                                                │
│  4. DASHBOARD                                                  │
│  ┌───────────────────────┐    ┌────────────────────────┐      │
│  │  Admin Dashboard      │    │  User Dashboard        │      │
│  │  /admin/home          │    │  /home                 │      │
│  │  (full menu sidebar)  │    │  (limited menu)        │      │
│  └───────────────────────┘    └────────────────────────┘      │
│                                                                │
│  5. TRANSAKSI (BELUM DIIMPLEMENTASI)                          │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐     │
│  │  Pilih   │→ │  Form    │→ │ Pending  │→ │ Approve/ │     │
│  │  Ruangan │  │ Booking  │  │ Status   │  │ Reject   │     │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘     │
└────────────────────────────────────────────────────────────────┘
```

---

## ✅ Fitur yang Sudah Diimplementasi

### A. Authentication System
- ✅ Login (`/login`) — Form login email + password
- ✅ Register (`/register`) — Form registrasi user baru
- ✅ Logout
- ✅ Password Reset (`/password/reset`)
- ✅ Role-based redirect setelah login (admin → `/admin/home`, user → `/home`)

### B. Role Management (CRUD)
- ✅ **Create** — Tambah role baru dengan nama, slug (auto-generate), deskripsi
- ✅ **Read** — Daftar semua role dengan jumlah pengguna, pencarian, paginasi
- ✅ **Update** — Edit nama, slug, deskripsi role
- ✅ **Delete** — Hapus role (validasi: tidak bisa hapus role yang masih punya user)
- ✅ **Show** — Detail role beserta daftar user yang memiliki role tersebut
- ✅ **Preset Role Universitas** — Tombol preset cepat (Mahasiswa, Dosen, Kepala Lab, dll.)
- ✅ **Seed Data** — 16 role bawaan (Admin, User, Mahasiswa, Dosen, Kepala Lab, Asisten Lab, Teknisi Lab, Kaprodi, Sekretaris Prodi, Staff Prodi, Dekan, Wakil Dekan, Staff Fakultas, Rektor, Staff Universitas)

### C. Dynamic Menu System (CRUD)
- ✅ **Create** — Tambah menu parent/submenu, tentukan icon, route, urutan, dan role yang bisa akses
- ✅ **Read** — Daftar menu dalam format tree/tabel
- ✅ **Update** — Edit nama, icon, route, urutan, role assignment
- ✅ **Delete** — Hapus menu (validasi: parent dengan children tidak bisa langsung dihapus)
- ✅ **Dynamic Rendering** — Sidebar di-render otomatis dari database via `View Composer` di `AppServiceProvider`
- ✅ **Recursive Menu Component** — `menu-item.blade.php` mendukung nested menu tanpa batas kedalaman
- ✅ **Context Separation** — Menu dipisah berdasarkan context: `admin` dan `user`

### D. User Profile
- ✅ **Edit Profile** — Ubah nama, email
- ✅ **Upload Avatar** — Upload gambar avatar (disimpan di `storage/app/public/avatars/`)
- ✅ **Ganti Password** — Ubah password dengan verifikasi password lama

### E. Middleware & Authorization
- ✅ **UserRoleMiddleware** — Middleware custom untuk proteksi route berdasarkan `role.slug`
- ✅ **Multi-role support** — Middleware mendukung pemeriksaan multiple role (dipisah `|` atau `,`)
- ✅ **View Composer** — Inject dynamic menu ke sidebar berdasarkan role user yang login

### F. Admin Dashboard
- ✅ Halaman dashboard admin (`/admin/home`)
- ✅ Sidebar navigasi dinamis

### G. User Dashboard
- ✅ Halaman dashboard user biasa (`/home`)
- ✅ Sidebar navigasi dinamis (menu terbatas sesuai role)

---

## 📂 Daftar File Kunci per Fitur

### Authentication
| File | Fungsi |
|---|---|
| `routes/web.php` | `Auth::routes()` + route groups |
| `app/Http/Controllers/Auth/*` | Login, Register, Reset, Verify controllers |
| `resources/views/auth/*` | Login, Register, Verify, Password reset views |
| `app/Http/Middleware/UserRoleMiddleware.php` | Proteksi route berdasarkan role |
| `app/Http/Middleware/RedirectIfAuthenticated.php` | Redirect user yang sudah login |

### Role Management
| File | Fungsi |
|---|---|
| `app/Models/Role.php` | Model: `fillable`, relasi `users()`, `menus()` |
| `app/Http/Controllers/RoleController.php` | CRUD: index, create, store, show, edit, update, destroy |
| `resources/views/admin/roles/index.blade.php` | Tabel daftar role + stats + search + delete modal |
| `resources/views/admin/roles/create.blade.php` | Form tambah role + preset universitas |
| `resources/views/admin/roles/edit.blade.php` | Form edit role |
| `resources/views/admin/roles/show.blade.php` | Detail role + daftar user + timestamps + delete |
| `database/migrations/2026_02_23_000001_create_roles_table.php` | Schema: `id, name, slug, description` |
| `database/seeders/RoleSeeder.php` | 16 role bawaan |

### Dynamic Menu
| File | Fungsi |
|---|---|
| `app/Models/Menu.php` | Model: self-referencing `parent()/children()`, `roles()` M:N |
| `app/Http/Controllers/Admin/MenuController.php` | CRUD menu + sync roles |
| `resources/views/admin/menus/index.blade.php` | Daftar menu (tree view) |
| `resources/views/admin/menus/create.blade.php` | Form tambah menu |
| `resources/views/admin/menus/edit.blade.php` | Form edit menu |
| `resources/views/layouts/partials/admin/navbar-vertical-admin.blade.php` | Render sidebar dari `$dynamicMenus` |
| `resources/views/layouts/partials/admin/menu-item.blade.php` | Komponen rekursif per menu item |
| `app/Providers/AppServiceProvider.php` | View Composer: fetch menu sesuai role |
| `database/migrations/2026_02_23_000002_create_menus_table.php` | Schema: `menus` + pivot `menu_role` |
| `database/seeders/MenuSeeder.php` | Menu awal (Dashboard, Manage Front End, Manage Users) |

### User Profile
| File | Fungsi |
|---|---|
| `app/Http/Controllers/Admin/ProfileController.php` | Edit + update profil (nama, email, avatar, password) |
| `resources/views/admin/profile/edit.blade.php` | Form edit profil |
| `database/migrations/2026_02_25_031515_add_avatar_to_users_table.php` | Kolom `avatar` di tabel users |

---

## 📊 Status MVP (Minimum Viable Product)

| # | Fitur MVP | Status | Keterangan |
|---|---|---|---|
| **A** | **CRUD Role Management** | ✅ **SELESAI** | Create, Read, Update, Delete role + validasi + preset |
| **B** | **CRUD Dynamic Menu** | ✅ **SELESAI** | Parent/Submenu + role-assignment + rendering dinamis |
| **C** | **Room Management** | ❌ **BELUM** | Belum ada model `Room`, migrasi, ataupun controller |
| **D** | **Booking & Approval System** | ❌ **BELUM** | Belum ada model `Booking`, kalender, form, approval workflow |

### Detail Status per Sub-Fitur

#### A. CRUD Role Management ✅
- [x] Create role baru
- [x] Read daftar role + search + paginasi
- [x] Update nama/slug/deskripsi role
- [x] Delete role (validasi jika role masih dipakai user aktif)
- [x] Detail role + daftar user terkait
- [x] 16 preset role universitas/fakultas

#### B. CRUD Dynamic Menu ✅
- [x] Create parent menu baru
- [x] Create submenu (pilih parent)
- [x] Assign role ke menu
- [x] Read tree-view menu dengan relasi parent-children
- [x] Update nama, icon, route, urutan, role
- [x] Delete menu (validasi children)
- [x] Dynamic rendering sidebar via View Composer

#### C. Room Management ❌
- [ ] Model `Room` (nama, lokasi, kapasitas, fasilitas)
- [ ] Migration tabel `rooms`
- [ ] Klasifikasi ruangan: Universitas vs Fakultas
- [ ] CRUD ruangan (Create, Read, Update, Delete)
- [ ] Halaman UI manajemen ruangan

#### D. Booking & Approval System ❌
- [ ] Model `Booking` (user_id, room_id, tanggal, waktu, tujuan, status)
- [ ] Migration tabel `bookings`
- [ ] Sistem kalender ketersediaan ruangan
- [ ] Form peminjaman
- [ ] Status workflow: `Pending → Approved/Rejected → Finished`
- [ ] Notifikasi ke admin untuk approval
- [ ] Dashboard approval untuk admin
- [ ] Auto-update jadwal ruangan setelah approved

---

## 📜 Riwayat Commit (Git History)

| Tanggal | Commit | Deskripsi |
|---|---|---|
| 25 Feb 2026 | `28abf3d` | UI update & merge conflicts resolution (Tailwind CSS views) |
| 24 Feb 2026 | `99086fe` | Merge PR #3: Role Management CRUD (final) |
| 24 Feb 2026 | `09c1265` | feat: add role management CRUD |
| 24 Feb 2026 | `6162f2a` | Merge PR #2: Revert role management |
| 24 Feb 2026 | `d5a3105` | Revert "feat: add role management CRUD" |
| 24 Feb 2026 | `40a78ca` | Merge PR #1: Role management (initial) |
| 24 Feb 2026 | `da838bd` | feat: add role management CRUD |
| 23 Feb 2026 | `19b9c0f` | Refactor user roles and permissions system |
| 23 Feb 2026 | `4632201` | **First commit** — Inisialisasi proyek |

---

## 🚀 Cara Setup & Menjalankan

```bash
# 1. Clone repository
git clone https://github.com/coatalter/web-pin-ruang.git
cd web-pin-ruang

# 2. Install dependensi PHP
composer install

# 3. Install dependensi Node.js
npm install

# 4. Copy .env dan generate key
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di file .env
# (ubah DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 6. Jalankan migrasi & seeder
php artisan migrate --seed

# 7. Link storage untuk avatar
php artisan storage:link

# 8. Jalankan development server
php artisan serve
npm run dev

# 9. Akses di browser: http://localhost:8000
```

### Akun Default (dari Seeder)

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@admin.com` | `password` |
| **User** | `user@user.com` | `password` |

# Perpustakaan Digital — Progress Tracker

> Merges blueprint requirements from `docs/blueprint.md` with best-practice phases from `docs/decission-support-sytem.md`.

---

## Phase 1: Foundation — Roles & Authentication ✅

**Goal:** Add `peran` column, create the `Peran` enum, build `CekPeran` middleware, disable Fortify registration, and prepare auth scaffolding.

### 1.1 Role Column Migration ✅
- [x] Create new migration: add `peran` (`string`, default `'user'`) to `users` table
  - Values: `'super_admin'`, `'admin'`, `'user'`
  - **DSS §1.1**: Must come before any other custom migrations

### 1.2 Peran Enum ✅
- [x] Create `app/Enums/Peran.php` (backed string enum)
  - `SuperAdmin = 'super_admin'`, `Admin = 'admin'`, `User = 'user'`
  - **DSS §2.3**: Backed string, always access via `.value`

### 1.3 User Model Update ✅
- [x] Add `'peran'` to `#[Fillable]`
- [x] Add `'peran' => Peran::class` to `casts()`
- [x] Add `isSuperAdmin()`, `isAdmin()`, `isUser()` helper methods
- [x] Add relationships: `hasMany(BukuFavorit::class)`, `hasMany(MarkahBuku::class)`, `hasMany(RiwayatBaca::class)`
- [x] **DSS §2.2**: Use `#[Fillable]` attribute + `casts()` method, never `$fillable`/`$casts` properties

### 1.4 CekPeran Middleware ✅
- [x] Create `app/Http/Middleware/CekPeran.php`
  - Accepts parameter: `peran:super_admin` or `peran:super_admin,admin`
  - Checks `auth()->user()->peran->value` against allowed roles
  - Abort 403 on mismatch

### 1.5 Register Middleware Alias ✅
- [x] In `bootstrap/app.php`, alias `'peran' => CekPeran::class`
  - **DSS §5.1**: Register immediately after creation

### 1.6 Fortify — Disable Registration ✅
- [x] In `config/fortify.php`, comment out `Features::registration()`
  - **DSS §5.2**: Only admins create users; disable public registration

### 1.7 Role-Aware Login Redirect ✅
- [x] Create `app/Http/Responses/LoginResponse.php` implementing Fortify contract
- [x] Register in `FortifyServiceProvider@register()`
- [x] super_admin → `/super-admin/kelola-admin`, admin → `/admin/buku`, user → `/app/dasbor`

### 1.8 User Factory & Seeder ✅
- [x] Update `Database\Factories\UserFactory` to include `peran`
- [x] Create `Database\Seeders\UserSeeder` with super_admin, admin, user
  - **DSS §6.3**: Factory unique ranges must not collide with seeder values

### 1.9 Build Check ✅
- [x] Run `npm run build` — cleaned up register/dashboard imports
  - **DSS §5.2**: Run immediately after disabling registration

---

## Phase 2: Database — Custom Migrations ✅

### 2.1–2.7 All Migrations ✅
- [x] penulis, kategori, buku, buku_kategori, buku_favorit, markah_buku, riwayat_baca
- [x] All `constrained()` use explicit table names — **DSS §2.1**

### 2.8 Run Migrations ✅
- [x] `php artisan migrate:fresh --seed` — all 13 tables created

---

## Phase 3: Eloquent Models ✅

### 3.1–3.6 All Models ✅
- [x] Penulis, Kategori, Buku, BukuFavorit, MarkahBuku, RiwayatBaca
- [x] All use `#[Fillable]` + `HasFactory` + explicit `$table` + return-typed relationships
- [x] RiwayatBaca uses `casts()` for `tanggal => date`

### 3.7 User Model Update ✅
- [x] Relationships to BukuFavorit, MarkahBuku, RiwayatBaca

### 3.8 Factories & Seeders ✅
- [x] PenulisFactory, KategoriFactory, BukuFactory created and seeded

**Goal:** Create all 7 custom tables from the blueprint schema.

- **DSS §1.1**: Dependency order — master tables first, then child tables
- **DSS §1.2**: Explicit table names in `constrained()`
- **DSS §2.1**: Always `->constrained('penulis')` not `->constrained()`

### 2.1 Penulis Migration
- [ ] `php artisan make:migration create_penulis_table`
  - `id`, `nama` (string), `biografi` (text, nullable), `timestamps`

### 2.2 Kategori Migration
- [ ] `php artisan make:migration create_kategori_table`
  - `id`, `nama` (string), `slug` (string, unique), `timestamps`

### 2.3 Buku Migration
- [ ] `php artisan make:migration create_buku_table`
  - `id`, `penulis_id` (FK → penulis), `judul` (string), `sinopsis` (text), `sampul` (string, nullable), `file_pdf` (string), `jumlah_halaman` (integer), `timestamps`
  - FK: `$table->foreignId('penulis_id')->constrained('penulis')->cascadeOnDelete()`

### 2.4 Buku_Kategori Pivot Migration
- [ ] `php artisan make:migration create_buku_kategori_table`
  - `buku_id` (FK → buku), `kategori_id` (FK → kategori)
  - FK: explicit `constrained('buku')` and `constrained('kategori')`

### 2.5 Buku_Favorit Migration
- [ ] `php artisan make:migration create_buku_favorit_table`
  - `id`, `user_id` (FK → users), `buku_id` (FK → buku), `timestamps`
  - Unique constraint on `['user_id', 'buku_id']`

### 2.6 Markah_Buku Migration
- [ ] `php artisan make:migration create_markah_buku_table`
  - `id`, `user_id` (FK → users), `buku_id` (FK → buku), `halaman` (integer), `catatan` (string, nullable), `timestamps`

### 2.7 Riwayat_Baca Migration
- [ ] `php artisan make:migration create_riwayat_baca_table`
  - `id`, `user_id` (FK → users), `buku_id` (FK → buku), `halaman_dibaca` (integer), `tanggal` (date), `timestamps`

### 2.8 Run Migrations
- [ ] `php artisan migrate:fresh --seed`

---

## Phase 3: Eloquent Models

**Goal:** Create all 7 models with relationships, fillable attributes, and casts.

- **DSS §2.2**: `#[Fillable]`, `#[Hidden]`, `casts()` method — never properties
- All custom models must set `protected $table` with the Indonesian table name when Laravel's pluralizer doesn't match

### 3.1 Penulis Model
- [ ] `php artisan make:model Penulis`
  - `#[Fillable(['nama', 'biografi'])]`
  - `hasMany(Buku::class)`

### 3.2 Kategori Model
- [ ] `php artisan make:model Kategori`
  - `#[Fillable(['nama', 'slug'])]`
  - `belongsToMany(Buku::class, 'buku_kategori')`

### 3.3 Buku Model
- [ ] `php artisan make:model Buku`
  - `#[Fillable(['penulis_id', 'judul', 'sinopsis', 'sampul', 'file_pdf', 'jumlah_halaman'])]`
  - `belongsTo(Penulis::class)`
  - `belongsToMany(Kategori::class, 'buku_kategori')`
  - `hasMany(BukuFavorit::class)`, `hasMany(MarkahBuku::class)`, `hasMany(RiwayatBaca::class)`

### 3.4 BukuFavorit Model
- [ ] `php artisan make:model BukuFavorit`
  - `#[Fillable(['user_id', 'buku_id'])]`
  - `belongsTo(User::class)`, `belongsTo(Buku::class)`

### 3.5 MarkahBuku Model
- [ ] `php artisan make:model MarkahBuku`
  - `#[Fillable(['user_id', 'buku_id', 'halaman', 'catatan'])]`
  - `belongsTo(User::class)`, `belongsTo(Buku::class)`

### 3.6 RiwayatBaca Model
- [ ] `php artisan make:model RiwayatBaca`
  - `#[Fillable(['user_id', 'buku_id', 'halaman_dibaca', 'tanggal'])]`
  - `belongsTo(User::class)`, `belongsTo(Buku::class)`

### 3.7 User Model Update
- [ ] See Phase 1.3 — update with relationships to new models

### 3.8 Factories & Seeders
- [ ] `php artisan make:factory PenulisFactory`
- [ ] `php artisan make:factory KategoriFactory`
- [ ] `php artisan make:factory BukuFactory`
- [ ] `php artisan make:seeder DatabaseSeeder` — call UserSeeder, PenulisSeeder, KategoriSeeder, BukuSeeder
- [ ] **DSS §6.3**: Ensure factory unique ranges don't collide with seeded data

---

## Phase 4: Form Requests

**Goal:** One Form Request class per action.

- **DSS §2.5**: Never validate in controllers
- `php artisan make:request` for each

### 4.1 Admin Form Requests
- [ ] `app/Http/Requests/Admin/AdminStoreRequest.php`
- [ ] `app/Http/Requests/Admin/AdminUpdateRequest.php`
- [ ] `app/Http/Requests/Admin/BukuStoreRequest.php`
- [ ] `app/Http/Requests/Admin/BukuUpdateRequest.php`
- [ ] `app/Http/Requests/Admin/PenulisCepatRequest.php` — for inline author creation

### 4.2 App Form Requests
- [ ] `app/Http/Requests/App/ToggleFavoritRequest.php`
- [ ] `app/Http/Requests/App/SimpanMarkahRequest.php`
- [ ] `app/Http/Requests/App/CatatStatistikRequest.php`

---

## Phase 5: Controllers

**Goal:** Create all 6 controllers per the blueprint.

- **DSS §2.4**: Explicit methods (not resource controllers), return type declarations, `Inertia::flash('toast', ...)` for messages
- Return `Inertia\Response` for pages, `RedirectResponse` for actions

### 5.1 SuperAdmin\AdminController
- [ ] `php artisan make:controller SuperAdmin/AdminController`
  - `index()`: list users with `peran = 'admin'`
  - `create()`: show create form
  - `store(AdminStoreRequest)`: create new admin user
  - `edit(User)`: show edit form
  - `update(AdminUpdateRequest, User)`: update admin
  - `destroy(User)`: delete admin (not self)
  - **DSS §2.4**: One file per domain

### 5.2 Admin\BukuController
- [ ] `php artisan make:controller Admin/BukuController`
  - `index()`: list all books with author + categories
  - `create()`: show form with penulis + kategori options
  - `store(BukuStoreRequest)`: upload PDF to `storage/app/public/buku_pdf`, sync categories
  - `edit(Buku)`: show edit form
  - `update(BukuUpdateRequest, Buku)`: update book, re-sync categories
  - `destroy(Buku)`: delete book
  - `tambahPenulisCepat(PenulisCepatRequest)`: create Penulis on-the-fly, return new ID as JSON

### 5.3 Admin\LaporanController ✅
- [x] `php artisan make:controller Admin/LaporanController`
  - `index()`: show reports page with stats
  - `cetakBukuDitambahkan()`, `cetakBukuTerbanyakDibaca()`, `cetakBukuTerfavorit()`, `cetakAktivitasMembaca()` — per-report PDF downloads
  - Month range filtering with `strftime('%Y-%m', col) BETWEEN ? AND ?`
  - **DSS §2.6**: Prefix ambiguous column names in JOIN queries
  - **DSS §7.2 (NEW)**: One PDF per report, not one mega-PDF

### 5.4 Admin\KartuAnggotaController ✅
- [x] `php artisan make:controller Admin/KartuAnggotaController`
  - `index()`: list all users with role `user`, search/filter
  - `cetak($user)`: generate printable member card with standalone layout
  - **DSS §8.1 (NEW)**: Layout resolved via `app.ts` returning `null`, not via `setRootView()`

### 5.5 App\PerpustakaanController
- [ ] `php artisan make:controller App/PerpustakaanController`
  - `index()`: logged-in user stats
  - Books read this month, total pages read, reading streak
  - Query from `RiwayatBaca` where `user_id = auth()->id()`

### 5.6 App\BacaBukuController
- [ ] `php artisan make:controller App/BacaBukuController`
  - `lihat($id)`: show book detail page (synopsis, categories, author)
  - `bacaPdf($id)`: serve PDF file for in-browser viewing
  - `toggleFavorit(ToggleFavoritRequest)`: add/remove favorite
  - `simpanMarkah(SimpanMarkahRequest)`: save/update bookmark
  - `catatStatistik(CatatStatistikRequest)`: log reading session pages

---

## Phase 6: Routes

**Goal:** Create route sub-files and wire them up.

- **DSS §3.1**: Never use `Route::inertia()` for data pages — always `Route::get` + controller
- **DSS §3.2**: One sub-file per domain

### 6.1 Route Files
- [ ] Create `routes/super-admin.php`
  - Prefix `super-admin`, middleware `['auth', 'peran:super_admin']`
  - `Route::resource('kelola-admin', AdminController::class)` except show
  - Name prefix: `super-admin.`

- [ ] Create `routes/admin.php`
  - Prefix `admin`, middleware `['auth', 'peran:super_admin,admin']`
  - `Route::resource('buku', BukuController::class)`
  - `Route::post('penulis/cepat', [BukuController::class, 'tambahPenulisCepat'])`
  - `Route::get('kartu-anggota', [KartuAnggotaController::class, 'index'])`
  - `Route::get('kartu-anggota/cetak/{user_id}', [KartuAnggotaController::class, 'cetak'])`
  - `Route::get('laporan', [LaporanController::class, 'index'])`
  - Name prefix: `admin.`

- [ ] Create `routes/app.php`
  - Prefix `app`, middleware `['auth', 'peran:super_admin,admin,user']`
  - `Route::get('dasbor', [PerpustakaanController::class, 'index'])`
  - `Route::get('buku/{id}', [BacaBukuController::class, 'lihat'])`
  - `Route::get('baca/{id}', [BacaBukuController::class, 'bacaPdf'])`
  - `Route::post('favorit', [BacaBukuController::class, 'toggleFavorit'])`
  - `Route::post('markah', [BacaBukuController::class, 'simpanMarkah'])`
  - `Route::post('catat-halaman', [BacaBukuController::class, 'catatStatistik'])`
  - Name prefix: `app.`

### 6.2 Update web.php
- [ ] Remove existing dashboard route
- [ ] Add `require __DIR__.'/super-admin.php'`, `require __DIR__.'/admin.php'`, `require __DIR__.'/app.php'`
- [ ] Keep settings.php require

### 6.3 Remove Welcome Page
- [ ] Replace `/` route with redirect to login

---

## Phase 7: Frontend — Types & Layout

**Goal:** TypeScript interfaces, shared layouts, and sidebar components.

- **DSS §4.4**: Export all types from `resources/js/types/index.ts`

### 7.1 TypeScript Types
- [ ] `resources/js/types/buku.ts` — Buku, Penulis, Kategori, BukuFavorit, MarkahBuku, RiwayatBaca
- [ ] `resources/js/types/user.ts` — User (with peran), Peran enum type
- [ ] `resources/js/types/laporan.ts` — LaporanStats, MostReadBook, MostFavorited
- [ ] Update `resources/js/types/index.ts` to re-export all

### 7.2 Layout & Sidebar Components
- [ ] Create `resources/js/Components/SidebarSuperAdmin.vue`
  - Links: Kelola Admin
- [ ] Create `resources/js/Components/SidebarAdmin.vue`
  - Links: Manajemen Buku, Laporan, Kartu Anggota
- [ ] Create `resources/js/Components/SidebarUser.vue`
  - Links: Dasbor, Daftar Buku
- [ ] Create `resources/js/Layouts/AppLayout.vue`
  - Conditionally renders correct sidebar based on `auth.user.peran`
- [ ] Update `HandleInertiaRequests` to share `auth.user` with peran
- [ ] **DSS §4.2**: DialogTrigger buttons must not be disabled — gate content instead

---

## Phase 8: Frontend — Vue Pages

**Goal:** All Vue page components from the blueprint.

- **DSS §4.1**: All template-bound state must use `ref()`, never `let`
- **DSS §4.4**: Import types from `@/types`, don't define inline

### 8.1 SuperAdmin Pages
- [ ] `resources/js/pages/SuperAdmin/KelolaAdmin/Index.vue`
  - Table of admins, delete button (not self), create button
- [ ] `resources/js/pages/SuperAdmin/KelolaAdmin/Form.vue`
  - Create/edit form with Inertia `useForm`

### 8.2 Admin Pages
- [ ] `resources/js/pages/Admin/Buku/Index.vue`
  - Book list with pagination, search, delete
- [ ] `resources/js/pages/Admin/Buku/Form.vue`
  - Create/edit form, penulis dropdown with inline creation modal, kategori multi-select
  - Inline author modal: "Penulis tidak ditemukan. Tambah Baru?" → POST to `tambahPenulisCepat`
- [ ] `resources/js/pages/Admin/Laporan/Index.vue`
  - Charts/tables for monthly stats
- [ ] `resources/js/pages/Admin/KartuAnggota/Index.vue`
  - User list with print button per user

### 8.3 App Pages
- [ ] `resources/js/pages/App/Dasbor/Index.vue`
  - User reading stats dashboard
- [ ] `resources/js/pages/App/Baca/Detail.vue`
  - Book synopsis, author, categories, favorite toggle, "Baca" button
- [ ] `resources/js/pages/App/Baca/Viewer.vue`
  - PDF viewer with bookmarking and page tracking
  - Uses `vue-pdf-embed` or `pdfjs-dist`
  - Bookmark button: grabs current page from viewer state, POSTs to `simpanMarkah`
  - Reading stats: `Set` of unique pages, pings `catatStatistik` on `router.on('before')`

---

## Phase 9: PDF Viewer & Interactive Features

**Goal:** Install PDF viewer library and implement bookmarking + reading stats.

### 9.1 PDF Viewer Setup
- [ ] Install `vue-pdf-embed` (Vue 3 PDF component)
- [ ] Configure in `App/Baca/Viewer.vue`
- [ ] Handle PDF loading from backend `bacaPdf` endpoint

### 9.2 Favorite Toggle
- [ ] `Detail.vue`: "Tambah ke Favorit" / "Hapus dari Favorit" button
- [ ] POST to `toggleFavorit`, use Inertia `useForm` with optimistic updates

### 9.3 Bookmark System
- [ ] `Viewer.vue`: "Tandai Halaman Ini" button near pagination
- [ ] On click: POST current page number to `simpanMarkah`

### 9.4 Reading Stats Tracker
- [ ] `Viewer.vue`: `watch` on current page variable
- [ ] Track unique pages visited in a `Set<number>`
- [ ] On `router.on('before')` or `window.beforeunload`: POST `Set.size` to `catatStatistik`

---

## Phase 10: Polish & Auth Flow

**Goal:** Role-aware redirects, flash messages, and authorization gates.

### 10.1 Role-Aware Login Redirect
- [ ] Override `Fortify\LoginResponse` or create custom redirect logic
- [ ] super_admin → `/super-admin/kelola-admin`
- [ ] admin → `/admin/buku`
- [ ] user → `/app/dasbor`

### 10.2 Flash Messages
- [ ] Configure `Inertia::flash('toast', [...])` in all store/update/destroy actions
- [ ] Create toast component in layout

### 10.3 Authorization
- [ ] Create `app/Policies/BukuPolicy.php`
- [ ] Gate admin actions to super_admin + admin
- [ ] Gate super_admin actions to super_admin only

### 10.4 Storage Link
- [ ] `php artisan storage:link` for public access to uploaded PDFs

---

## Phase 11: Testing

**Goal:** Feature tests for all critical paths.

- **DSS §6.1**: Use `PreventRequestForgery::class`
- **DSS §6.2**: Inertia returns JSON — access via `getOriginalContent()->getData()`
- **DSS §6.4**: Use `Model::create()` for precision test data, not nested factories

### 11.1 Auth & Role Tests
- [ ] `tests/Feature/RoleMiddlewareTest.php`
  - Guest cannot access any protected route
  - User cannot access /admin/* or /super-admin/*
  - Admin cannot access /super-admin/*
  - Super admin can access everything

### 11.2 Admin CRUD Tests
- [ ] `tests/Feature/SuperAdmin/AdminManagementTest.php`
  - Super admin can create admin
  - Super admin can list admins
  - Super admin can delete admin (not self)

### 11.3 Book Management Tests
- [ ] `tests/Feature/Admin/BookManagementTest.php`
  - Admin can create book with categories
  - Admin can update book
  - Admin can delete book
  - `tambahPenulisCepat` creates penulis and returns ID

### 11.4 User App Tests
- [ ] `tests/Feature/App/ReadingTest.php`
  - User can view book detail
  - User can toggle favorite
  - User can save bookmark
  - User can log reading stats

### 11.5 Laporan Tests
- [ ] `tests/Feature/Admin/LaporanTest.php`
  - Monthly book count is correct
  - Most read book query is correct
  - Most favorited query is correct

---

## DSS Quick Checklist

Tracked from `docs/decission-support-sytem.md`:

| # | Step | Status |
|---|------|--------|
| 1 | Migration: every `constrained()` uses explicit table name | ☐ |
| 2 | Model: `#[Fillable]` + `casts()` method + relationships | ☐ |
| 3 | Enum: backed string, access via `.value` | ☐ |
| 4 | Routes: `Route::get` + controller, not `Route::inertia` for data pages | ☐ |
| 5 | Controller: return `Inertia\Response` / `RedirectResponse`, flash toast | ☐ |
| 6 | Form Request: one class per action, explicit rules | ☐ |
| 7 | JOIN queries: prefix ambiguous column names with table name | ☐ |
| 8 | Delete single record: use `$model->delete()`, NOT `$model->query()->delete()` | ✅ |
| 8a | Bulk delete: `Model::query()->delete()` is fine for seeders/truncation | ✅ |
| 9 | Vue: all template state uses `ref()`, never `let` | ☐ |
| 10 | DialogTrigger: never disable the trigger button | ☐ |
| 11 | Enum to Inertia: map to `{ value, label }` | ☐ |
| 12 | Roles: middleware + migration + seeder added from the start | ☐ |
| 13 | Fortify: `'home'` config points to the correct dashboard route | ☐ |
| 14 | Route rename: grep the entire project | ☐ |
| 15 | Disable Fortify feature: immediately `npm run build` to find broken imports | ☐ |
| 16 | Pages directory: use lowercase `pages/` consistently in all `@vite()` Blade directives | ✅ |
| 17 | Tests: use `PreventRequestForgery::class`, not `VerifyCsrfToken::class` | ☐ |
| 18 | Factory: unique range doesn't overlap with seeder | ☐ |
| 19 | PDF export: separate report = separate PDF, not one mega-PDF | ✅ |
| 20 | PDF views: inline `<style>` CSS only, DomPDF can't process frameworks | ✅ |
| 21 | Standalone page: return `null` in layout resolver, use `<a target="_blank">` | ✅ |
| 22 | Month range filtering: use `strftime('%Y-%m', col) BETWEEN ? AND ?` for SQLite | ✅ |
| 23 | Inline creation dialogs: don't force navigation for related entity creation | ✅ |
| 24 | Auto-filter: `watch` with 300ms debounce instead of manual refresh button | ✅ |
| 25 | Frontend `computed` import: must be in `<script setup lang="ts">` block | ✅ |
| 26 | Controller `setRootView()` is global state — use layout resolver in `app.ts` instead | ✅ |

---

## File Manifest

### New Files to Create (59 files)

| # | File | Phase |
|---|------|-------|
| 1 | `database/migrations/XXXX_add_peran_to_users_table.php` | 1.1 |
| 2 | `app/Enums/Peran.php` | 1.2 |
| 3 | `app/Http/Middleware/CekPeran.php` | 1.4 |
| 4 | `database/seeders/UserSeeder.php` | 1.8 |
| 5 | `database/migrations/XXXX_create_penulis_table.php` | 2.1 |
| 6 | `database/migrations/XXXX_create_kategori_table.php` | 2.2 |
| 7 | `database/migrations/XXXX_create_buku_table.php` | 2.3 |
| 8 | `database/migrations/XXXX_create_buku_kategori_table.php` | 2.4 |
| 9 | `database/migrations/XXXX_create_buku_favorit_table.php` | 2.5 |
| 10 | `database/migrations/XXXX_create_markah_buku_table.php` | 2.6 |
| 11 | `database/migrations/XXXX_create_riwayat_baca_table.php` | 2.7 |
| 12 | `app/Models/Penulis.php` | 3.1 |
| 13 | `app/Models/Kategori.php` | 3.2 |
| 14 | `app/Models/Buku.php` | 3.3 |
| 15 | `app/Models/BukuFavorit.php` | 3.4 |
| 16 | `app/Models/MarkahBuku.php` | 3.5 |
| 17 | `app/Models/RiwayatBaca.php` | 3.6 |
| 18 | `database/factories/PenulisFactory.php` | 3.8 |
| 19 | `database/factories/KategoriFactory.php` | 3.8 |
| 20 | `database/factories/BukuFactory.php` | 3.8 |
| 21 | `app/Http/Requests/Admin/AdminStoreRequest.php` | 4.1 |
| 22 | `app/Http/Requests/Admin/AdminUpdateRequest.php` | 4.1 |
| 23 | `app/Http/Requests/Admin/BukuStoreRequest.php` | 4.1 |
| 24 | `app/Http/Requests/Admin/BukuUpdateRequest.php` | 4.1 |
| 25 | `app/Http/Requests/Admin/PenulisCepatRequest.php` | 4.1 |
| 26 | `app/Http/Requests/App/ToggleFavoritRequest.php` | 4.2 |
| 27 | `app/Http/Requests/App/SimpanMarkahRequest.php` | 4.2 |
| 28 | `app/Http/Requests/App/CatatStatistikRequest.php` | 4.2 |
| 29 | `app/Http/Controllers/SuperAdmin/AdminController.php` | 5.1 |
| 30 | `app/Http/Controllers/Admin/BukuController.php` | 5.2 |
| 31 | `app/Http/Controllers/Admin/LaporanController.php` | 5.3 |
| 32 | `app/Http/Controllers/Admin/KartuAnggotaController.php` | 5.4 |
| 33 | `app/Http/Controllers/App/PerpustakaanController.php` | 5.5 |
| 34 | `app/Http/Controllers/App/BacaBukuController.php` | 5.6 |
| 35 | `routes/super-admin.php` | 6.1 |
| 36 | `routes/admin.php` | 6.1 |
| 37 | `routes/app.php` | 6.1 |
| 38 | `resources/js/types/buku.ts` | 7.1 |
| 39 | `resources/js/types/user.ts` | 7.1 |
| 40 | `resources/js/types/laporan.ts` | 7.1 |
| 41 | `resources/js/Components/SidebarSuperAdmin.vue` | 7.2 |
| 42 | `resources/js/Components/SidebarAdmin.vue` | 7.2 |
| 43 | `resources/js/Components/SidebarUser.vue` | 7.2 |
| 44 | `resources/js/Layouts/AppLayout.vue` | 7.2 |
| 45 | `resources/js/pages/SuperAdmin/KelolaAdmin/Index.vue` | 8.1 |
| 46 | `resources/js/pages/SuperAdmin/KelolaAdmin/Form.vue` | 8.1 |
| 47 | `resources/js/pages/Admin/Buku/Index.vue` | 8.2 |
| 48 | `resources/js/pages/Admin/Buku/Form.vue` | 8.2 |
| 49 | `resources/js/pages/Admin/Laporan/Index.vue` | 8.2 |
| 50 | `resources/js/pages/Admin/KartuAnggota/Index.vue` | 8.2 |
| 51 | `resources/js/pages/App/Dasbor/Index.vue` | 8.3 |
| 52 | `resources/js/pages/App/Baca/Detail.vue` | 8.3 |
| 53 | `resources/js/pages/App/Baca/Viewer.vue` | 8.3 |
| 54 | `app/Policies/BukuPolicy.php` | 10.3 |
| 55 | `tests/Feature/RoleMiddlewareTest.php` | 11.1 |
| 56 | `tests/Feature/SuperAdmin/AdminManagementTest.php` | 11.2 |
| 57 | `tests/Feature/Admin/BookManagementTest.php` | 11.3 |
| 58 | `tests/Feature/App/ReadingTest.php` | 11.4 |
| 59 | `tests/Feature/Admin/LaporanTest.php` | 11.5 |

### Existing Files to Modify (8 files)

| # | File | Phase | Change |
|---|------|-------|--------|
| 1 | `app/Models/User.php` | 1.3 | Add `peran` to Fillable, casts, helpers, relationships |
| 2 | `bootstrap/app.php` | 1.5 | Register `CekPeran` middleware alias |
| 3 | `config/fortify.php` | 1.6, 1.7 | Disable registration, update home |
| 4 | `database/factories/UserFactory.php` | 1.8 | Add `peran` field |
| 5 | `database/seeders/DatabaseSeeder.php` | 3.8 | Call all seeders |
| 6 | `routes/web.php` | 6.2 | Require sub-files, update root route |
| 7 | `app/Http/Middleware/HandleInertiaRequests.php` | 7.2 | Share `auth.user` with peran |
| 8 | `resources/js/types/index.ts` | 7.1 | Re-export new type modules |

### Single Pages Directory

On macOS (case-insensitive APFS), `resources/js/Pages/` and `resources/js/pages/` are the same directory. There's only one. Use lowercase `pages/` in all `@vite()` Blade directives. See DSS §4.3.

---

## Implementation Order

Start at Phase 1, proceed sequentially. Each phase builds on the previous one. Do not skip phases — DSS §1.1 explicitly requires migration-first development.

```
Phase 1  → Phase 2  → Phase 3  → Phase 4
Phase 4  → Phase 5  → Phase 6  → Phase 7
Phase 7  → Phase 8  → Phase 9  → Phase 10  → Phase 11
```

Phases 1-3 are backend foundation. Phases 4-6 are backend feature. Phases 7-9 are frontend. Phase 10-11 are polish and verification.

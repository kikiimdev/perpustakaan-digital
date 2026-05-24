Here is the complete blueprint for your Digital Library (`Perpustakaan Digital`) application, structured for Laravel 13, Vue 3, and Inertia.js.

I have strictly applied the naming conventions you requested: using English for default tables and Laravel structures, but Bahasa Indonesia for custom tables, models, controllers, and frontend UI text.

---

## 1. Database Schema

Since you are using Laravel's default authentication, we keep the `users` table but add a role column. All other entities are named in Bahasa Indonesia.

* **`users`** (Default)
* `id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `timestamps`
* *Added field:* `peran` (enum/string: `'super_admin'`, `'admin'`, `'user'`) default `'user'`


* **`penulis`** (Authors)
* `id`, `nama`, `biografi` (nullable), `timestamps`


* **`kategori`** (Categories)
* `id`, `nama`, `slug` (unique), `timestamps`


* **`buku`** (Books)
* `id`, `penulis_id` (foreign key), `judul`, `sinopsis` (text), `sampul` (string/nullable), `file_pdf` (string), `jumlah_halaman` (integer), `timestamps`


* **`buku_kategori`** (Pivot Table for Book-Category Many-to-Many)
* `buku_id`, `kategori_id`


* **`buku_favorit`** (User Favorites)
* `id`, `user_id`, `buku_id`, `timestamps`


* **`markah_buku`** (User Bookmarks)
* `id`, `user_id`, `buku_id`, `halaman` (integer), `catatan` (string/nullable), `timestamps`


* **`riwayat_baca`** (Reading Statistics Log)
* `id`, `user_id`, `buku_id`, `halaman_dibaca` (integer - counts how many pages read in a session), `tanggal` (date), `timestamps`
* *(This table is crucial for generating the monthly user stats and admin reports).*



---

## 2. Eloquent Models

Create these models in `app/Models/`. Ensure you define the table names if Laravel's pluralization doesn't match perfectly.

* **`User`**: `hasMany(BukuFavorit::class)`, `hasMany(MarkahBuku::class)`, `hasMany(RiwayatBaca::class)`
* **`Penulis`**: `hasMany(Buku::class)`
* **`Kategori`**: `belongsToMany(Buku::class, 'buku_kategori')`
* **`Buku`**: `belongsTo(Penulis::class)`, `belongsToMany(Kategori::class, 'buku_kategori')`, `hasMany(BukuFavorit::class)`, `hasMany(RiwayatBaca::class)`
* **`BukuFavorit`**: `belongsTo(User::class)`, `belongsTo(Buku::class)`
* **`MarkahBuku`**: `belongsTo(User::class)`, `belongsTo(Buku::class)`
* **`RiwayatBaca`**: `belongsTo(User::class)`, `belongsTo(Buku::class)`

---

## 3. Middleware & Routing Strategy

To handle the hierarchical folder access, you should create a custom middleware (e.g., `CekPeran`).

**Logic for `CekPeran` Middleware:**

* `/super-admin/*` -> Only `'super_admin'` allowed.
* `/admin/*` -> Both `'super_admin'` and `'admin'` allowed.
* `/app/*` -> `'super_admin'`, `'admin'`, and `'user'` allowed.

**`routes/web.php` Structure:**

```php
use App\Http\Controllers\SuperAdmin\AdminController;
use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\KartuAnggotaController;
use App\Http\Controllers\App\PerpustakaanController;
use App\Http\Controllers\App\BacaBukuController;

// Super Admin Routes
Route::middleware(['auth', 'peran:super_admin'])->prefix('super-admin')->group(function () {
    Route::resource('kelola-admin', AdminController::class);
});

// Admin Routes (Super Admin can also access)
Route::middleware(['auth', 'peran:super_admin,admin'])->prefix('admin')->group(function () {
    Route::resource('buku', BukuController::class);
    Route::post('penulis/cepat', [BukuController::class, 'tambahPenulisCepat']); // Inline author creation
    Route::get('kartu-anggota', [KartuAnggotaController::class, 'index']);
    Route::get('kartu-anggota/cetak/{user_id}', [KartuAnggotaController::class, 'cetak']);
    Route::get('laporan', [LaporanController::class, 'index']);
});

// User App Routes (Everyone can access)
Route::middleware(['auth', 'peran:super_admin,admin,user'])->prefix('app')->group(function () {
    Route::get('dasbor', [PerpustakaanController::class, 'index']); // Stats live here
    Route::get('buku/{id}', [BacaBukuController::class, 'lihat']);
    Route::get('baca/{id}', [BacaBukuController::class, 'bacaPdf']);
    Route::post('favorit', [BacaBukuController::class, 'toggleFavorit']);
    Route::post('markah', [BacaBukuController::class, 'simpanMarkah']);
    Route::post('catat-halaman', [BacaBukuController::class, 'catatStatistik']); // Pinged by PDF viewer
});

```

---

## 4. Controller Blueprint

Here is what the core logic inside your Bahasa Indonesia named controllers should handle:

* **`SuperAdmin\AdminController`**: Standard CRUD to create users where `peran = 'admin'`.
* **`Admin\BukuController`**:
* Handles PDF uploads (store in `storage/app/public/buku_pdf`).
* Attaches `Kategori` using `$buku->kategori()->sync($request->kategori_ids)`.
* `tambahPenulisCepat(Request $request)`: An API endpoint that creates a `Penulis` on the fly and returns the new ID to the Vue frontend so the admin doesn't have to leave the "Add Book" page.


* **`Admin\LaporanController`**:
* *Books added in 1 month:* `Buku::whereMonth('created_at', $bulan)->count()`
* *Most read book:* Join `buku` with `riwayat_baca`, group by `buku_id`, sum `halaman_dibaca`, order by descending.
* *Most favorited:* Count relations on `BukuFavorit`.


* **`Admin\KartuAnggotaController`**: Generates a printable HTML/CSS view (or PDF using a package like `barryvdh/laravel-dompdf`) for User ID cards.
* **`App\PerpustakaanController`**: Queries `RiwayatBaca` for the logged-in user to show their dashboard stats (total books read this month, total pages read).
* **`App\BacaBukuController`**: Serves the PDF file to the frontend viewer. Handles the toggle API for favorites and saves page bookmarks.

---

## 5. Vue 3 + Inertia.js Frontend Architecture

Your `resources/js/pages` directory should perfectly mirror the routing constraints.

```text
resources/js/pages/
├── SuperAdmin/
│   └── KelolaAdmin/
│       ├── Index.vue      // "Daftar Admin"
│       └── Form.vue       // "Tambah/Edit Admin"
├── Admin/
│   ├── Buku/
│   │   ├── Index.vue      // "Manajemen Buku"
│   │   └── Form.vue       // "Tambah Buku" (Includes the inline Author modal)
│   ├── Laporan/
│   │   └── Index.vue      // "Laporan Perpustakaan" (Charts/Tables)
│   └── KartuAnggota/
│       └── Index.vue      // "Cetak Kartu"
├── App/
│   ├── Dasbor/
│   │   └── Index.vue      // "Statistik Membaca Saya"
│   └── Baca/
│       ├── Detail.vue     // Book synopsis, "Tambah ke Favorit" button
│       └── Viewer.vue     // The actual PDF reader
└── Components/
    ├── SidebarSuperAdmin.vue
    ├── SidebarAdmin.vue
    └── SidebarUser.vue

```

### Feature Implementation Tips for Vue:

**1. Inline Author Creation (Admin):**
In `Admin/Buku/Form.vue`, use a component like `vue-multiselect`. If the user types an author's name that doesn't exist, show a small button: `"Penulis tidak ditemukan. Tambah Baru?"`. Clicking it opens a modal, submits a quick Axios/Inertia form to `Admin\BukuController@tambahPenulisCepat`, and auto-selects the newly returned author in the dropdown.

**2. The PDF Viewer & Page Tracking (User):**
Use `pdfjs-dist` or a wrapper like `vue-pdf-embed` in `App/Baca/Viewer.vue`.

* **Bookmarking:** Add a button `"Tandai Halaman Ini"` next to the PDF pagination controls. When clicked, it grabs the current page number from the PDF viewer state and posts to `App/BacaBukuController@simpanMarkah`.
* **Reading Stats:** Track page turns. You can use Vue's `watch` on the current page variable. Keep a local `Set` of unique pages visited during the session. When the user closes the viewer or navigates away (using Inertia's `router.on('before')` event), ping `catat-halaman` with the total number of unique pages read during that session to update the user's statistics.

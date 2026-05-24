# Guide: Building a DSS (Decision Support System) Project in Laravel

Based on lessons from building a Scholarship Assessment System (SAW method) with Laravel 13 + Vue 3 + Inertia.js + SQLite. Follow this order to avoid common pitfalls.

---

## Phase 1: Planning

### 1.1 Design the Database Schema First, Then Code

Don't start from controllers or Vue. Start from **migrations**. Get every table, column, foreign key, and unique constraint right before touching any other code.

```
Correct order:
1. Enum (if any)
2. Migrations (dependency order: master tables first, then child tables)
3. Models + relationships
4. Seeders
5. Form Requests
6. Controllers
7. Routes
8. Vue Pages
```

### 1.2 Table Names: Plural, snake_case

Non-English table names (e.g. Indonesian) are fine — just **always pass the table name explicitly** to `constrained()`.

### 1.3 Never Use `truncate()` With Foreign Keys

Use `Model::query()->delete()`. Truncate fails on tables with FK constraints — it will always throw a 500 error.

### 1.4 Delete a Single Record: Use `$model->delete()`, Not `$model->query()->delete()`

`$model->query()` returns a **fresh, unconstrained** query builder — it has no `WHERE id = ?` clause:

```php
// ❌ Deletes EVERY row in the table — $user->query() === User::query()
$user->query()->delete();

// ✅ Deletes only this one record
$user->delete();
```

`Model::query()->delete()` is correct for bulk operations (e.g. seeders calling `User::query()->delete()`). But when you have a specific model instance from route model binding or `findOrFail()`, use `$model->delete()`.

---

## Phase 2: Backend (Laravel)

### 2.1 Migrations: Always Explicit in `constrained()`

```php
// ✅ ALWAYS do this — no matter what language the table name is
$table->foreignId('kriteria_id')->constrained('kriteria')->cascadeOnDelete();
$table->foreignId('pendaftar_id')->constrained('pendaftar')->cascadeOnDelete();

// ❌ NEVER do this — SQLite cannot guess non-English table names
$table->foreignId('kriteria_id')->constrained()->cascadeOnDelete();
```

### 2.2 Models: Use PHP 8 Attributes and `casts()` Method

Follow Laravel 13 conventions — `#[Fillable]`, `#[Hidden]`, `casts(): array` method:

```php
#[Fillable(['kode', 'nama', 'jenis', 'bobot'])]
class Kriteria extends Model
{
    protected function casts(): array
    {
        return [
            'jenis' => JenisKriteria::class,
            'bobot' => 'decimal:2',
        ];
    }
}
```

Never use `$fillable`, `$hidden`, or `$casts` as properties.

### 2.3 Enums: Backed String Enum + Always Use `.value`

```php
enum JenisKriteria: string
{
    case Benefit = 'benefit';
    case Cost = 'cost';
}

// ✅ In controllers/queries: always access via ->value
if ($kriteria->jenis->value === 'benefit') { ... }
```

When passing enums to Inertia, **never send `Enum::cases()` directly** — JSON serialization turns them into plain strings:

```php
// ❌ In Vue it becomes ['benefit', 'cost'] — not objects
'roles' => UserRole::cases()

// ✅ Send as array of { value, label }
'roles' => collect(UserRole::cases())->map(fn ($r) => [
    'value' => $r->value,
    'label' => $r->value === 'super_admin' ? 'Super Admin' : 'Admin',
])->values()
```

### 2.4 Controllers: One Per Feature, Explicit Methods

Namespace controllers under `App\Http\Controllers\Admin\`. One file per domain (KriteriaController, PendaftarController, etc.). Never cram everything into a single controller.

Don't use resource controllers — write methods explicitly: `index()`, `create()`, `store()`, `edit()`, `update()`, `destroy()`.

Always declare return types: `Inertia\Response` for pages, `RedirectResponse` for actions. Use `Inertia::flash('toast', [...])` for flash messages.

### 2.5 Form Requests: One Class Per Action

```
app/Http/Requests/Admin/
├── KriteriaStoreRequest.php
├── KriteriaUpdateRequest.php
├── SubKriteriaStoreRequest.php
└── ...
```

Never validate in the controller. Use separate Form Request classes.

### 2.6 JOIN Queries: Prefix Ambiguous Column Names

When joining two tables that share a column name, always prefix with the table name:

```php
// ❌ Error: ambiguous column name: kriteria_id
Penilaian::where('kriteria_id', ...)
    ->join('sub_kriteria', ...)

// ✅ Correct
Penilaian::where('penilaian.kriteria_id', ...)
    ->join('sub_kriteria', ...)
```

---

## Phase 3: Routes

### 3.1 Never Use `Route::inertia()` for Pages That Need Data

```php
// ❌ No props passed — blank page
Route::inertia('kriteria/create', 'Admin/Kriteria/Form');

// ✅ Controller supplies the props
Route::get('kriteria/create', [KriteriaController::class, 'create']);
```

`Route::inertia()` is only for static pages with zero server-side data.

### 3.2 Route Structure: One Sub-file Per Domain

```
routes/
├── web.php          ← require settings.php + admin.php
├── settings.php     ← profile, security, appearance
└── admin.php        ← all /admin/* routes
```

Use `Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')`.

### 3.3 When Renaming a Named Route, Grep the Entire Project

Not just route files. Fortify config, test files, Inertia pages, and sidebar components may all reference the old route name. Don't rename in just one file.

---

## Phase 4: Frontend (Vue + Inertia)

### 4.1 Use `ref()` Not `let` for Template-Bound State

```ts
// ❌ Dialog will never open
let addDialogOpen = false;
let editingItem: SubKriteria | null = null;

// ✅ Vue can track changes
const addDialogOpen = ref(false);
const editingItem = ref<SubKriteria | null>(null);
```

Rule: every variable bound to `v-model`, `v-model:open`, or used in `<template>` MUST use `ref()` or `reactive()`.

### 4.2 Never Disable a DialogTrigger Button (shadcn-vue)

`DialogTrigger as-child` cannot fire if the child is disabled:

```vue
<!-- ❌ Click does nothing -->
<DialogTrigger as-child>
    <Button :disabled="kriteria.sub_kriteria.length >= 5">Add</Button>
</DialogTrigger>

<!-- ✅ Always keep the button enabled, gate the dialog content -->
<DialogTrigger as-child>
    <Button>Add</Button>
</DialogTrigger>
<DialogContent>
    <p v-if="kriteria.sub_kriteria.length >= 5">Maximum 5.</p>
    <form v-else>...</form>
</DialogContent>
```

### 4.3 Pages Directory: One Directory, One Casing

On macOS (case-insensitive APFS), `resources/js/Pages/` and `resources/js/pages/` are the **same physical directory**. There is only one. But the Blade root views reference the path explicitly:

```php
// resources/views/app.blade.php
@vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])

// resources/views/cetak.blade.php  ← must match casing
@vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
```

Pick one casing — **lowercase `pages/`** — and use it consistently in every Blade file that has a `@vite()` directive. If one file says `Pages/` and another says `pages/`, it causes confusion (and breaks on case-sensitive Linux deployments). The `inertia()` Vite plugin resolves components from the path passed by the server, so the Blade file's casing is what matters.

### 4.4 Export Types in `resources/js/types/`

Keep all TypeScript interfaces in the `types/` folder — don't define them inline in components. Export from `types/index.ts`:

```
resources/js/types/
├── index.ts    ← export * from all files
├── saw.ts      ← Kriteria, SubKriteria, Pendaftar, Penilaian, HasilPenilaian
└── ...
```

---

## Phase 5: Roles & Middleware

### 5.1 Add Role Column, Enum, and Middleware Early

Don't delay role implementation. The sooner it's in place, the less refactoring later:

1. Migration: `$table->string('role')->default('admin')` on users table
2. Enum: `UserRole: string` with `SuperAdmin` and `Admin` cases
3. Model: add `'role'` to `#[Fillable]`, cast to enum, add `isSuperAdmin()` helper
4. Middleware: `EnsureUserIsAdmin` (any admin) and `EnsureUserIsSuperAdmin` (super admin only)
5. Register middleware aliases in `bootstrap/app.php`
6. Apply to route groups

### 5.2 Disable Fortify Registration When Only Admins Create Users

```php
// config/fortify.php
'features' => [
    // Features::registration(), ← comment out
    Features::resetPasswords(),
    ...
],
```

After disabling, **immediately run `npm run build`** to find all files still importing register routes. Don't delay — the longer you wait, the harder build errors are to trace.

### 5.3 Fortify Config Must Be Updated When Routes Change

`config/fortify.php` has a `'home'` setting that determines the post-login redirect. If the dashboard route moves, this must be updated too.

---

## Phase 6: Testing

### 6.1 Use `PreventRequestForgery` (Laravel 13)

The CSRF middleware name changed in Laravel 13. Apply it in `beforeEach()`:

```php
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->withoutMiddleware(PreventRequestForgery::class);
});
```

### 6.2 Inertia Returns JSON — Never Use `assertSee()`

```php
// ❌ Will never work
$response->assertSee($pendaftar->nisn);

// ✅ Access the Inertia JSON payload
$data = $response->getOriginalContent()->getData();
expect($data['page']['props']['hasil'])->toBeArray();
```

### 6.3 Factory `unique()` Must Not Collide With Seeders

If your seeder creates codes C1-C7, the factory must never generate overlapping strings:

```php
// ❌ Could generate C4, colliding with the seeder
'kode' => fake()->unique()->regexify('C[1-9][0-9]?')

// ✅ Safe range
'kode' => 'C'.fake()->unique()->numberBetween(1, 99)
```

### 6.4 Use `Model::create()` Not Nested Factories in Precision Tests

Factory nesting (factory inside factory) creates uncontrolled records. For SAW calculation tests or any scenario needing precise data, use `Model::create()` with explicit values.

---

## Quick Checklist for New Projects

| # | Step | Check |
|---|------|-------|
| 1 | Migration: every `constrained()` uses explicit table name | ☐ |
| 2 | Model: `#[Fillable]` + `casts()` method + relationships | ☐ |
| 3 | Enum: backed string, access via `.value` | ☐ |
| 4 | Routes: `Route::get` + controller, not `Route::inertia` for data pages | ☐ |
| 5 | Controller: return `Inertia\Response` / `RedirectResponse`, flash toast | ☐ |
| 6 | Form Request: one class per action, explicit rules | ☐ |
| 7 | JOIN queries: prefix ambiguous column names with table name | ☐ |
| 8 | Delete single record: use `$model->delete()`, NOT `$model->query()->delete()` | ☐ |
| 8a | Bulk delete: `Model::query()->delete()` is fine for seeders/truncation | ☐ |
| 9 | Vue: all template state uses `ref()`, never `let` | ☐ |
| 10 | DialogTrigger: never disable the trigger button | ☐ |
| 11 | Enum to Inertia: map to `{ value, label }` | ☐ |
| 12 | Roles: middleware + migration + seeder added from the start | ☐ |
| 13 | Fortify: `'home'` config points to the correct dashboard route | ☐ |
| 14 | Route rename: grep the entire project | ☐ |
| 15 | Disable Fortify feature: immediately `npm run build` to find broken imports | ☐ |
| 16 | Pages directory: use lowercase `pages/` consistently in all `@vite()` Blade directives | ☐ |
| 17 | Tests: use `PreventRequestForgery::class`, not `VerifyCsrfToken::class` | ☐ |
| 18 | Factory: unique range doesn't overlap with seeder | ☐ |
| 19 | PDF export: separate report = separate PDF, not one mega-PDF | ☐ |
| 20 | PDF views: inline `<style>` CSS only, DomPDF can't process frameworks | ☐ |
| 21 | Standalone page: return `null` in layout resolver, use `<a target="_blank">` | ☐ |
| 22 | Month range filtering: use `strftime('%Y-%m', col) BETWEEN ? AND ?` for SQLite | ☐ |
| 23 | Inline creation dialogs: don't force navigation for related entity creation | ☐ |
| 24 | Auto-filter: `watch` with 300ms debounce instead of manual refresh button | ☐ |
| 25 | Frontend `computed` import: must be in `<script setup lang="ts">` block, not separate `<script>` | ☐ |
| 26 | Controller `setRootView()` is global state — use layout resolver in `app.ts` instead | ☐ |
| 27 | Seeders must generate actual files (PDFs/covers), never write fake paths to DB | ☐ |
| 28 | JSON API endpoints: use `fetch()` with CSRF token, not Inertia `useForm().post()` | ☐ |
| 29 | PDF viewer: use `vue-pdf-embed` + `pdfjs-dist`, not `<iframe>` — browser ignores fragment changes | ☐ |
| 30 | `vue-pdf-embed` `:scale` only affects DPI — wrap with CSS `transform: scale()` for visual zoom | ☐ |
| 31 | Fortify `'home'` config must match an actual route, or registration redirects to 404 | ☐ |
| 32 | Registration `CreateNewUser` must set `peran` (role) explicitly, not rely on DB default | ☐ |
| 33 | Bookmark toggle: use create/delete, not `updateOrCreate` — enables unbookmark | ☐ |
| 34 | Dashboard card props: check whether backend returns model vs nested relation (e.g. `rec.judul` not `rec.buku.judul`) | ☐ |

---

## Phase 11: Seeding (Real Files on Disk)

### 11.1 Never Write Fake Paths to the Database

If your seeder writes `file_pdf = 'buku_pdf/sample-3.pdf'` but no file actually exists at that path, every iframe/image tag will show broken content (403/404).

**Always generate actual files during seeding:**

```php
Storage::disk('public')->put($filename, Pdf::loadHTML($html)->output());
```

### 11.2 Generate Placeholder Covers with GD

When real cover images aren't available, generate colored placeholder PNGs:

```php
$img = imagecreatetruecolor(400, 600);
$bg = imagecolorallocate($img, $r, $g, $b);
imagefilledrectangle($img, 0, 0, 400, 600, $bg);
imagestring($img, 5, $x, $y, $judul, $white);
imagepng($img, $path);
imagedestroy($img);
```

Use an array of 6-8 distinct background colors, cycling through `$index % count($colors)`. This gives each book a unique visual identity even without real covers.

### 11.3 Clean Up Before Reseeding

Always call `Storage::disk('public')->deleteDirectory('sampul')` before regenerating. Otherwise dead files accumulate.

---

## Phase 12: JSON API Calls (Not Inertia)

### 12.1 Use `fetch()` for Actions That Return JSON

Inertia's `useForm().post()` expects an Inertia page response (`Inertia\Response` or `RedirectResponse`). If the backend returns `JsonResponse`, the browser shows:

> All Inertia requests must receive a valid Inertia response, however a plain JSON response was received.

**Fix: use plain `fetch()` with CSRF:**

```ts
const csrfToken = () => {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el?.getAttribute('content') ?? '';
};

const handleToggle = async () => {
    const res = await fetch('/app/favorit', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({ buku_id: buku.id }),
    });
    const data = await res.json();
    isFavorit.value = data.status === 'added';
};
```

### 12.2 Toggle Pattern: Create OR Delete, Not Upsert

For bookmark/favorite toggles, `updateOrCreate` can never delete — the button can only add, never remove. Use explicit create/delete:

```php
$existing = MarkahBuku::where(...)->first();
if ($existing) {
    $existing->delete();
    return response()->json(['status' => 'removed']);
}
MarkahBuku::create([...]);
return response()->json(['status' => 'added']);
```

---

## Phase 13: PDF Viewer (vue-pdf-embed)

### 13.1 Install and Configure

```bash
npm install vue-pdf-embed pdfjs-dist
cp node_modules/pdfjs-dist/build/pdf.worker.min.mjs public/
```

```ts
// resources/js/app.ts
import { GlobalWorkerOptions } from 'pdfjs-dist';
GlobalWorkerOptions.workerSrc = '/pdf.worker.min.mjs';
```

### 13.2 Why Not `<iframe>`?

Browsers only read `#page=` from the iframe's initial URL. Navigating `currentPage` and updating the iframe `:src` triggers a full reload of the entire PDF — no smooth page flip.

`vue-pdf-embed` renders individual `<canvas>` elements per page via PDF.js. Changing the `:page` prop swaps the rendered page instantly.

### 13.3 The `:scale` Prop Only Affects Render DPI

`vue-pdf-embed`'s `:scale` controls canvas pixel density, not visual size. The canvas dimensions (CSS `width`/`height`) are set programmatically by the component based on page aspect ratio.

For actual visual zoom, **wrap in a `transform: scale()` container:**

```vue
<div :style="{ transform: `scale(${scale})`, transformOrigin: 'top center' }">
    <VuePdfEmbed :source="pdfSource" :page="currentPage" :scale="scale" />
</div>
```

Pass high `:scale` (e.g. 2.0) for crisp rendering when zoomed in.

### 13.4 Sync Page with URL Query

```ts
watch(currentPage, (page) => {
    const url = new URL(window.location.href);
    url.searchParams.set('halaman', String(page));
    window.history.replaceState({}, '', url.toString());
});
```

Read `?halaman=` on mount to restore the last-visited page (useful for bookmark links).

---

## Phase 14: Landing Page & Registration

### 14.1 Registration: Three Changes Needed

**A) Enable the feature in `config/fortify.php`:**
```php
Features::registration(),  // uncomment
```

**B) Default new users to the correct role:**
```php
// app/Actions/Fortify/CreateNewUser.php
return User::create([
    'name' => $input['name'],
    'email' => $input['email'],
    'password' => $input['password'],
    'peran' => Peran::User,  // explicit, not DB default
]);
```

**C) Fix the `'home'` redirect — Fortify uses it for post-registration:**
```php
// config/fortify.php
'home' => '/app/dasbor',  // not '/dashboard' which doesn't exist
```

### 14.2 Add a Register Link to the Login Page

```vue
<TextLink :href="register()">Daftar</TextLink>
```

Run `php artisan wayfinder:generate` to create the route function, then import it.

### 14.3 Landing Page

Replace the default Laravel `/` page with a proper hero + features section. Show "Daftar Gratis" / "Masuk" for guests, "Dashboard" / "Jelajahi Buku" for authenticated users.

---

## Phase 15: Dashboard Card Props

### 15.1 Check the Shape of Backend Data

The backend may return a `Buku` model directly, but the frontend might expect a `BukuFavorit` wrapper:

```ts
// ❌ rec is Buku, not BukuFavorit — rec.buku is undefined
<p>{{ rec.buku?.judul }}</p>

// ✅ Access fields directly on the model
<p>{{ rec.judul }}</p>
```

Always trace from controller through to Inertia props — the model shape at each level determines the template path.

---

## Phase 7: PDF Export

### 7.1 Use barryvdh/laravel-dompdf

```bash
composer require barryvdh/laravel-dompdf --no-interaction
```

DomPDF renders Blade views to PDF. Create a Blade template in `resources/views/pdf/` with inline CSS (no Tailwind — DomPDF doesn't support it). Use `Pdf::loadView()` and `$pdf->download()`.

### 7.2 One PDF Per Report, Not One Mega-PDF

```php
// ✅ Separate report = separate PDF download
class LaporanController extends Controller
{
    public function cetakBukuDitambahkan(Request $request) { ... }
    public function cetakBukuTerbanyakDibaca(Request $request) { ... }
    public function cetakBukuTerfavorit(Request $request) { ... }
    public function cetakAktivitasMembaca(Request $request) { ... }
}

// ❌ One giant PDF with everything
public function cetakPdf(Request $request) { ... }
```

Each report type gets its own route, controller method, Blade view, and filename. The frontend has per-card export buttons — not one global button.

### 7.3 PDF Blade Views: Inline CSS Only

DomPDF cannot process CSS frameworks. Use plain `<style>` tags with base elements:

```html
<style>
    body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; padding: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
    th { background: #f5f5f5; font-weight: 600; }
    .footer { margin-top: 24px; text-align: right; font-size: 10px; color: #999; }
</style>
```

---

## Phase 8: Standalone Pages (Print, Cetak, etc.)

### 8.1 Never Rely on `setRootView()` Alone

`Inertia::setRootView('cetak')` in a controller is **global state** — it corrupts subsequent page navigations. And Inertia client-side navigation reuses the root view from the initial page load, so setting it in the controller doesn't help when navigating via `Link` or `router.get()`.

The correct approach is two-fold:

**A) Server-side: return `null` in the layout resolver**

```ts
// resources/js/app.ts
createInertiaApp({
    resolve: async (name) => {
        const page = (await resolvePageComponent(name)).default;

        // Standalone page → no layout wrapper
        if (name === 'Admin/KartuAnggota/Cetak') {
            return page;
        }

        // Default: wrap in AppLayout with sidebar/header
        page.layout = page.layout || AppLayout;
        return page;
    },
});
```

**B) Client-side: use `<a target="_blank">` not Inertia `Link`**

```vue
<!-- ❌ Inertia intercepts, reuses current root view -->
<Link :href="route('admin.kartu-anggota.cetak', anggota.id)">Cetak</Link>

<!-- ✅ Fresh page load → server sends the correct root view -->
<a :href="route('admin.kartu-anggota.cetak', anggota.id)" target="_blank">Cetak</a>
```

Same rule applies for PDF export buttons — use `window.open(url, '_blank')` not `router.get()`.

### 8.2 Inline Creation Dialogs for Related Entities

Don't force navigation to separate CRUD pages for related entities. Use inline creation dialogs:

```vue
<!-- ✅ Inline dialog in the form itself -->
<Dialog v-model:open="penulisDialogOpen">
    <DialogTrigger as-child>
        <Button type="button" size="sm" variant="outline">+ Penulis Baru</Button>
    </DialogTrigger>
    <DialogContent>
        <form @submit.prevent="simpanPenulis">
            <Input v-model="penulisBaru.nama" placeholder="Nama penulis" />
            <Button type="submit">Simpan</Button>
        </form>
    </DialogContent>
</Dialog>
```

Backend: create a lightweight "quick add" route:

```php
Route::post('penulis/cepat', [BukuController::class, 'tambahPenulisCepat'])->name('penulis.cepat');
```

### 8.3 Watch + Debounce for Auto-Filtering

When page data depends on date range inputs, avoid manual "Refresh" buttons:

```ts
const bulanAwal = ref(props.bulanAwal);
const bulanAkhir = ref(props.bulanAkhir);

let timeout: ReturnType<typeof setTimeout>;
watch([bulanAwal, bulanAkhir], () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/admin/laporan', {
            bulan_awal: bulanAwal.value,
            bulan_akhir: bulanAkhir.value,
        }, { preserveState: true, replace: true });
    }, 300); // 300ms debounce
});
```

---

## Phase 9: SQLite Date Queries

### 9.1 Use `strftime()` for Month Range Filters, Not `whereMonth()`

`whereMonth()` can't do range queries across months. Use `strftime()` with `BETWEEN`:

```php
// ❌ Only works for a single month
Buku::whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->count();

// ✅ Works for any date range
Buku::whereRaw("strftime('%Y-%m', created_at) BETWEEN ? AND ?", [$bulanAwal, $bulanAkhir])->count();
```

Parameter format: `'2025-01'`, `'2025-06'` (input type `month` gives this format natively).

### 9.2 Always Use Parameterized Queries with `whereRaw()`

```php
// ✅ Never concatenate user input
->whereRaw("strftime('%Y-%m', created_at) BETWEEN ? AND ?", [$awal, $akhir])

// ❌ SQL injection risk
->whereRaw("strftime('%Y-%m', created_at) BETWEEN '$awal' AND '$akhir'")
```

---

## Phase 10: Vue Multi-Block Scripts

### 10.1 Keep All Imports in `<script setup lang="ts">`

Vue SFCs can have multiple `<script>` blocks, but **imports** must be in the `<script setup>` block — a second `<script lang="ts">` block can't access them:

```vue
<!-- ✅ Everything in one block -->
<script setup lang="ts">
import { computed, ref, watch } from 'vue';
// ... component logic
</script>

<!-- ❌ computed() import in one block, usage in another → runtime error -->
<script setup lang="ts">
import { ref, watch } from 'vue';
</script>
<script lang="ts">
import { computed } from 'vue'; // Will NOT work
</script>
```

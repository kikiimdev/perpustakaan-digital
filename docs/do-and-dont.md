# Laravel 13 + Inertia + Vue 3: Do's and Don'ts

Quick-reference checklist derived from building a digital library app. Each item is a mistake we made and the fix.

---

## Migrations & Database

| # | ❌ Don't | ✅ Do |
|---|----------|------|
| D1 | `$table->foreignId('kriteria_id')->constrained()` — SQLite can't guess non-English table names | Always pass table name: `->constrained('kriteria')` |
| D2 | `Model::truncate()` on tables with foreign keys — always 500 error | `Model::query()->delete()` for bulk, `$model->delete()` for single |
| D3 | `$user->query()->delete()` on a route-model-bound instance — deletes all rows | `$user->delete()` — `query()` returns fresh, unconstrained builder |
| D4 | Write fake file paths in seeders (e.g. `'buku_pdf/sample-3.pdf'`) — broken images everywhere | Generate actual files: `Storage::disk('public')->put($path, $content)` |
| D5 | Let seeders append on top of old data — duplicates and orphans accumulate | `migrate:fresh --seed` when schema changes; `Storage::deleteDirectory()` before regenerating |

## Controllers & Backend

| # | ❌ Don't | ✅ Do |
|---|----------|------|
| C1 | `Inertia::setRootView('cetak')` — global state corrupts subsequent navigations | Return `null` in `app.ts` layout resolver for standalone pages |
| C2 | `Route::inertia('page', 'Component')` for pages that need server data | `Route::get(...)->uses([Controller::class, 'method'])` |
| C3 | Validate in controller methods | Separate Form Request class per action |
| C4 | `whereMonth()` + `whereYear()` for date range filters — single-month only | SQLite: `whereRaw("strftime('%Y-%m', col) BETWEEN ? AND ?", [$from, $to])` |
| C5 | Concatenate user input in `whereRaw()` | Always use `?` placeholders for parameterized queries |
| C6 | `updateOrCreate` for toggle actions (bookmark/favorite) — can't delete | Explicit `first()` → `delete()` or `create()` pattern |
| C7 | Omit `peran` (role) in `CreateNewUser` — relies on DB default | Explicitly set `'peran' => Peran::User` |
| C8 | `Fortify 'home' => '/dashboard'` when no `/dashboard` route exists | Point to an actual route: `'/app/dasbor'` |

## Frontend: Vue + Inertia

| # | ❌ Don't | ✅ Do |
|---|----------|------|
| F1 | `let` for template-bound state — Vue can't track it | `ref()` or `reactive()` for anything in `<template>` |
| F2 | Disable a `DialogTrigger` button — click does nothing | Keep button enabled; gate content inside dialog with `v-if` |
| F3 | `useForm().post()` for endpoints that return `JsonResponse` — "plain JSON" error | `fetch()` + `X-CSRF-TOKEN` header for JSON endpoints |
| F4 | `<iframe>` for PDF viewing — browser ignores `#page=` fragment changes | `vue-pdf-embed` (`:page` prop swaps rendered page instantly) |
| F5 | Rely on `vue-pdf-embed` `:scale` prop alone for zoom — only affects DPI | Wrap in `<div :style="{ transform: 'scale(...)' }">` for visual zoom |
| F6 | Use `Pages/` casing in one Blade file and `pages/` in another | Pick lowercase `pages/` — use consistently in every `@vite()` directive |
| F7 | Import `computed`/`watch` in a separate `<script>` block | All imports in `<script setup lang="ts">` — separate blocks can't share |

## Frontend: Props & Types

| # | ❌ Don't | ✅ Do |
|---|----------|------|
| P1 | Assume backend returns nested relation when it returns raw model | Trace controller → Inertia → template: `rec.judul` vs `rec.buku?.judul` |
| P2 | Send `Enum::cases()` directly to Inertia — becomes plain string array | Map to `[{ value, label }]` objects with `collect()->map()` |
| P3 | Define types inline in components | Export from `resources/js/types/`, re-export via `index.ts` |

## Routes & Config

| # | ❌ Don't | ✅ Do |
|---|----------|------|
| R1 | Rename a route in one file | Grep entire project: config, tests, sidebar, Vue pages, middleware |
| R2 | Disable a Fortify feature and forget to rebuild | Immediately run `npm run build` to surface broken imports |
| R3 | Navigation via Inertia `<Link>` to standalone/print pages | Use `<a target="_blank">` for fresh page load with correct root view |
| R4 | Forget to add register link after enabling `Features::registration()` | Add link on login page + run `wayfinder:generate` |

## PDF Generation

| # | ❌ Don't | ✅ Do |
|---|----------|------|
| G1 | One mega-PDF with all reports combined | Per-report PDF: separate controller method, Blade view, route, filename |
| G2 | Tailwind or CSS frameworks in DomPDF views | Plain `<style>` tags: `font-family: sans-serif`, explicit `px`, `border: 1px` |
| G3 | Use `page-break-before: always` on empty divs | Inline `style="page-break-before: always"` on page container div |

## Testing

| # | ❌ Don't | ✅ Do |
|---|----------|------|
| T1 | `VerifyCsrfToken::class` in `withoutMiddleware()` (Laravel 13 renamed it) | `PreventRequestForgery::class` |
| T2 | `$response->assertSee()` for Inertia pages — returns JSON, not HTML | `$response->getOriginalContent()->getData()` to access props |
| T3 | Factory `unique()` range overlapping with seeder fixed values | Use distinct numeric ranges: seeder C1-C7, factory C10-C99 |

## General Architecture

| # | ❌ Don't | ✅ Do |
|---|----------|------|
| A1 | Start coding from controllers or Vue | Migrations → Models → Seeders → Form Requests → Controllers → Routes → Vue |
| A2 | Inline creation of related entities by navigating away | Inline dialog + lightweight `/cepat` POST route |
| A3 | Manual "Refresh" button for date filters | `watch()` + 300ms debounce auto-reload |
| A4 | `$fillable`, `$hidden`, `$casts` as properties (Laravel 13) | `#[Fillable]`, `#[Hidden]`, `casts(): array` method |
| A5 | Use resource controllers | Write methods explicitly: `index()`, `store()`, `update()`, `destroy()` |

---

**Last updated from:** Digital Library project (Laravel 13 + Vue 3 + Inertia + SQLite + shadcn-vue + Tailwind 4 + Fortify + DomPDF + vue-pdf-embed)

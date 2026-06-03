# Code Style
- For admin book form: use inline creation dialogs for related entities (Penulis, Kategori) rather than requiring navigation to separate CRUD pages. Confidence: 0.70

# Inertia
- When an Inertia page unexpectedly shows the wrong layout (sidebar/header still visible), check `resources/js/app.ts` layout resolver — the `default: return AppLayout` case wraps all unmatched components. Adding a specific case returning `null` for standalone pages overrides this. Don't rely solely on server-side `setRootView()`. Confidence: 0.65
- For public Inertia pages (no auth required), explicitly return `null` from the layout resolver in `app.ts` for guest routes, or ensure layout components handle null user gracefully — the default AppLayout includes components like UserInfo.vue that assume authenticated user exists and will throw errors for guests. Confidence: 0.75

# Laporan
- For PDF exports: create separate individual PDF exports per report (e.g., Buku Ditambahkan, Buku Terbanyak Dibaca, Buku Terfavorit) rather than combining all reports into one PDF. Each report should have its own downloadable PDF. Confidence: 0.70

# Database
- Avoid database-specific SQL functions in `whereRaw()` — this app uses SQLite for testing and PostgreSQL for production. Functions like `strftime()` (SQLite-only) break on PostgreSQL; use `to_char()`/`EXTRACT()`/`DATE_TRUNC()` for Postgres or Eloquent's database-agnostic methods. Confidence: 0.75

# File Storage
- Resolve storage file URLs (e.g., `sampul`, `file_pdf`) in exactly one layer — either via model accessors using `Storage::url()` or in templates by prepending `/storage/`, never both. Double application causes broken URLs like `/storage//storage/sampul/...`. Prefer model accessors for consistency across all pages. Confidence: 0.75

# Pdfjs
- Use local worker file (`/pdf.worker.min.mjs`) for pdfjs-dist instead of CDN. Confidence: 0.75

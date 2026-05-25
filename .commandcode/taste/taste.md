# Code Style
- For admin book form: use inline creation dialogs for related entities (Penulis, Kategori) rather than requiring navigation to separate CRUD pages. Confidence: 0.70

# Inertia
- When an Inertia page unexpectedly shows the wrong layout (sidebar/header still visible), check `resources/js/app.ts` layout resolver — the `default: return AppLayout` case wraps all unmatched components. Adding a specific case returning `null` for standalone pages overrides this. Don't rely solely on server-side `setRootView()`. Confidence: 0.65

# Laporan
- For PDF exports: create separate individual PDF exports per report (e.g., Buku Ditambahkan, Buku Terbanyak Dibaca, Buku Terfavorit) rather than combining all reports into one PDF. Each report should have its own downloadable PDF. Confidence: 0.70

# Database
- Avoid database-specific SQL functions in `whereRaw()` — this app uses SQLite for testing and PostgreSQL for production. Functions like `strftime()` (SQLite-only) break on PostgreSQL; use `to_char()`/`EXTRACT()`/`DATE_TRUNC()` for Postgres or Eloquent's database-agnostic methods. Confidence: 0.75

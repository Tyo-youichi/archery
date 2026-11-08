# 7 · Laporan

Rekap lintas-atlet (semua atlet dalam satu tabel), meniru layout sheet Excel ATHTRIX.
Ramah cetak / Simpan PDF (landscape).

Controller: `app/Http/Controllers/ReportController.php`
View: `resources/views/reports/index.blade.php` + partial per jenis
Model: `app/Models/{Athlete,MonthlyScore}.php` (+ relasi)
Helper: `app/Support/Metrics.php`

| Route | Name | Method | Akses RBAC |
|-------|------|--------|------------|
| `GET /laporan/{type?}` | `reports.index` | `index` | admin, coach |

- **`{type}`** (default `fisik`): `fisik`, `teknik`, `skor`, `peralatan`, `beban`.
- Query opsional: `all=1` (ikutkan atlet nonaktif), `year` (khusus tab skor).

## `index()` → `match($type)`

| type | Data | Partial | Sumber |
|------|------|---------|--------|
| `fisik` | denyut nadi + antropometri terbaru + metrik fisik terbaru | `reports/physical.blade.php` | `anthropometries`, `physicalTests`, `Metrics::metrics('physical')` |
| `teknik` | metrik teknik terbaru | `reports/technique.blade.php` | `techniqueTests`, `Metrics::metrics('technique')` |
| `skor` | pivot 12 bulan 1 tahun | `reports/score.blade.php` | `monthlyScores` (filter tahun) |
| `peralatan` | 1 baris per atlet | `reports/equipment.blade.php` | `equipment`, `config('archery.equipment_fields')` |
| `beban` | 1RM terbaru per latihan | `reports/load.blade.php` | `loadTests`, `config('archery.load_exercises')` |

- View induk `reports/index.blade.php`: tab pills + tombol **Cetak/PDF** + toggle atlet aktif/semua; memilih partial lewat peta slug→nama (`fisik→physical`, dst), lalu `@include`.
- Kolom fisik/teknik **dinamis** mengikuti tabel `metrics` (lewat `Metrics`).

> Catatan deploy: route & menu Laporan di-seed ke tabel RBAC — perlu
> `php artisan db:seed --class=RbacSeeder` setelah fitur ditambahkan (lihat [11-rbac.md](11-rbac.md)).

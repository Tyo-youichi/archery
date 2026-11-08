# 2 · Dashboard

Controller: `app/Http/Controllers/DashboardController.php`
View: `resources/views/dashboard.blade.php`

| Aspek | Detail |
|-------|--------|
| Route | `GET /` → redirect `dashboard.index` · `GET /dashboard` → `dashboard.index` → `DashboardController@index` |
| Akses RBAC | `dashboard.*` → `*` (semua user login) |
| Middleware | `auth`, `rbac`, `payment` |

## Alur `index()`

1. Ambil `auth()->user()`.
2. **Jika bukan staf** (atlet): kalau punya data atlet → redirect `athletes.show` (profilnya sendiri); kalau tidak → tampilkan `dashboard` versi kosong (`staff => false`).
3. **Jika staf** (admin/coach):
   - Ambil semua `Athlete` aktif (`is_active = true`), urут nama.
   - Untuk tiap atlet panggil **`App\Support\ProfileService::forAthlete()`** → ambil `physical.overall` & `technique.overall` (persentase profil).
   - Hitung rata-rata fisik & teknik seluruh atlet.
   - Rekap **shoot 100 arrow minggu ini** dari `ShootLog` (Senin–Sabtu, `shot_100 = true`), dikelompokkan per atlet.
   - Kirim ke view: `rows`, `total`, `avgPhysical`, `avgTechnique`, `weekLogs`, `weekStart`.

## Keterkaitan

| Jenis | File |
|-------|------|
| Model | `App\Models\Athlete`, `App\Models\ShootLog` |
| Helper | `App\Support\ProfileService` (hitung % profil dari benchmark) |
| View | `resources/views/dashboard.blade.php` (+ partial `partials/pct.blade.php` untuk bar persen) |

> `ProfileService` membaca definisi metrik & benchmark dari `App\Support\Metrics`
> (tabel `metrics`, fallback `config/archery.php`), dan nilai tes dari kolom JSON
> `results` pada `physical_tests` / `technique_tests`. Detail di [10-metrik-bm.md](10-metrik-bm.md).

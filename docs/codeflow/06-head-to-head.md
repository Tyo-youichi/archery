# 6 · Head to Head

Bandingkan dua atlet berdampingan (antropometri, performa fisik, teknik, rekap skor).

Controller: `app/Http/Controllers/HeadToHeadController.php`
Helper: `app/Support/ProfileService.php`
Model: `app/Models/Athlete.php`, `app/Models/MonthlyScore.php`
View: `resources/views/head2head.blade.php`

| Route | Name | Method | Akses RBAC |
|-------|------|--------|------------|
| `GET /head-to-head` | `h2h.index` | `index` | admin, coach |

## `index()`
1. `abort_unless(auth()->user()->isStaff(), 403)` — guard ekstra selain RBAC.
2. Ambil semua atlet (untuk dropdown pilih A & B).
3. Jika query `a` & `b` terisi → untuk tiap atlet:
   - `ProfileService::forAthlete()` → profil fisik & teknik.
   - Rekap `MonthlyScore`: `best`, `low`, `avg`, `count`.
4. View `head2head.blade.php` (cetak/PDF didukung; foto kiri–kanan).

| Model | `Athlete` (+ `anthropometries`), `MonthlyScore` |
|-------|------|
| Helper | `ProfileService` → `Metrics` |

> Tombol **Cetak / Simpan PDF** memakai `@media print` khusus agar foto atlet tetap
> di kiri & kanan (grid 3 kolom) saat dicetak.

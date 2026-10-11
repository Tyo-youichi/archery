# Archer Monitor — Monitoring Atlet Panahan

Laravel 10 · PHP 8.1 · MariaDB. Berdasarkan scope di `LEADS_PERPANI_Kota_Bekasi_Analysis.xlsx`
dan struktur data di `ATHTRIX PROFILE ... .XLSM`, plus Buku Saku "Tabel Monitor Shoot 100 Arrow".

## Fitur

| Modul | Keterangan |
|---|---|
| Login / Registrasi | Atlet mendaftar sendiri (`/register`): reguler, prestasi, terapi ABK. Role: `admin`, `coach`, `athlete`. |
| RBAC | Role & izin per-route di **database** (tabel `roles`, `route_permissions`, `menus`), di-seed dari `config/rbac.php`, bisa di-CRUD admin. CSRF aktif (monolith). |
| Database atlet | CRUD biodata (staf). Atlet hanya bisa melihat profilnya sendiri. |
| Upload foto atlet | Kompresi di frontend → disimpan `.webp`; ikon anonim bila kosong. |
| Profil atlet (tab) | Biodata, antropometri (IMT otomatis), tes fisik, performa teknik, beban (1RM otomatis), skor bulanan, peralatan — tiap kategori jadi **tab** dengan data visualnya: donut capaian, radar benchmark-vs-hasil, bar hasil-vs-benchmark. |
| Monitoring denyut nadi | Gauge nadi istirahat / latihan / maksimum (% terhadap nadi maks) di tab Antropometri. |
| Metrik & benchmark dinamis | Tabel `metrics` + halaman CRUD (admin): tambah/ubah/nonaktifkan metrik, set BM per gender. Hasil tes disimpan sebagai JSON `results`; **metrik baru tanpa migrasi**. BM di-snapshot per record. |
| Profil % vs benchmark | `% = min(1, hasil / BM)`; grup = rata-rata; profil = rata-rata grup (sama dengan Excel). BM diambil dari tabel `metrics` (per gender), dapat diubah lewat UI. |
| Buku Saku | Tabel mingguan Senin–Sabtu: teknik diperbaiki (a–f), shoot 100 arrow Ya/Tdk, catatan. Atlet mengisi sendiri. |
| Head to Head | Bandingkan 2 atlet: radar, antropometri, fisik, teknik, rekap skor. Cetak/ekspor PDF. |
| Dashboard staf | Rata-rata profil tim + kepatuhan 100 arrow minggu ini. |

Belum dibuat (ada di leads): pembayaran bulanan + pembatasan akses, tabel Task/Result task, notifikasi WhatsApp.

## Instalasi (tanpa Docker)

```bash
composer install
cp .env.example .env            # sesuaikan DB_* (MariaDB)
php artisan key:generate
php artisan migrate
php artisan db:seed             # RBAC, referensi Yo-Yo IR1, definisi metrik
php artisan archery:make-user admin@contoh.com "Admin" admin
php artisan serve
```

Buat database dulu: `CREATE DATABASE archer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
Bootstrap & Chart.js di-host **lokal** di `public/js` (tanpa CDN, tanpa npm).

Data contoh fiktif (opsional, hanya lokal): `php artisan db:seed --class=DemoSeeder`.
Akun demo: `admin@archer.test` / `Admin#2026`, `coach@archer.test` / `Coach#2026`, `athlete@archer.test` / `Athlete#2026`.

## Menjalankan dengan Docker

Compose berisi **satu service `app`** (Apache + PHP 8.1), diekspos di **port 7777**. Database di luar
compose (MariaDB host/eksternal); `storage` & `public/uploads` dipersistensikan via volume. Detail di `DOCKER.md`.

```bash
docker compose up -d --build                                   # app di http://localhost:7777
docker compose exec app php artisan db:seed --class=DemoSeeder  # data contoh
```

## Impor dari Excel ATHTRIX

```bash
php artisan archery:import-excel /path/ATHTRIX_PROFILE.XLSM --year=2026 --test-date=2026-01-15
```

Mengimpor atlet, antropometri, tes fisik, performa teknik, skor bulanan, peralatan, dan beban.
Aman dijalankan ulang (upsert). Catatan:
- Pencocokan antar-sheet berdasarkan **nama**, karena ID atlet di Excel tidak konsisten antar-sheet (mis. ATL3 di sheet teknik ≠ ATL3 di DATA ATLET; juga ada typo `ALT2`, `ALT6`, dst.).
- Excel tidak menyimpan tanggal tes dan tahun skor bulanan, jadi diisi lewat opsi `--test-date` dan `--year`.
- Skor `307+304` disimpan sebagai catatan dan dijumlahkan (611).

## Struktur singkat

- `config/archery.php` — grup profil, kode teknik, definisi form, serta **nilai awal (seed)** metrik & benchmark. Runtime metrik dibaca dari tabel `metrics`.
- `app/Support/Metrics.php` — definisi metrik dari DB (fallback config) + cache.
- `app/Support/ProfileService.php` — rumus profil (baca metrik + JSON `results`).
- `app/Http/Controllers/MetricController.php` — CRUD metrik & benchmark (admin).
- `app/Http/Controllers/RecordController.php` — CRUD generik data berseri (field metrik dinamis → JSON `results`).
- `database/migrations/2026_10_11_000001_create_athletes_table.php` — skema inti.
- `database/migrations/2026_10_11_000003_create_metrics_table.php` — tabel metrik/benchmark.
- `tests/Unit/ProfileServiceTest.php` — uji rumus terhadap angka Excel.

Tes: `php artisan test`

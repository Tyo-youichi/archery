# Archer Monitor — Monitoring Atlet Panahan

Laravel 10 · PHP 8.1 · MariaDB. Berdasarkan scope di `LEADS_PERPANI_Kota_Bekasi_Analysis.xlsx`
dan struktur data di `ATHTRIX PROFILE ... .XLSM`, plus Buku Saku "Tabel Monitor Shoot 100 Arrow".

## Fitur

| Modul | Keterangan |
|---|---|
| Login / Registrasi | Atlet mendaftar sendiri (`/register`): reguler, prestasi, terapi ABK. Role: `admin`, `coach`, `athlete`. |
| Database atlet | CRUD biodata (staf). Atlet hanya bisa melihat profilnya sendiri. |
| Dashboard Profile | Biodata, antropometri (IMT otomatis), tes fisik, performa teknik, beban (1RM otomatis), skor bulanan, peralatan, grafik radar/skor/berat badan. |
| Profil % vs benchmark | `% = min(1, hasil / BM)`; grup = rata-rata; profil = rata-rata grup (sama dengan Excel). BM default fisik per gender, bisa diubah per tes. |
| Buku Saku | Tabel mingguan Sn–Sb: teknik diperbaiki (a–f), shoot 100 arrow Ya/Tdk, catatan. Atlet mengisi sendiri. |
| Head to Head | Bandingkan 2 atlet: radar, antropometri, fisik, teknik, rekap skor. |
| Dashboard staf | Rata-rata profil tim + kepatuhan 100 arrow minggu ini. |

Belum dibuat (ada di leads): pembayaran bulanan + pembatasan akses, tabel Task/Result task, notifikasi WhatsApp, upload foto.

## Instalasi

```bash
composer install
cp .env.example .env            # sesuaikan DB_* (MariaDB)
php artisan key:generate
php artisan migrate
php artisan db:seed             # tabel referensi Yo-Yo IR1
php artisan archery:make-user admin@contoh.com "Admin" admin
php artisan serve
```

Buat database dulu: `CREATE DATABASE archer CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
Frontend memakai Bootstrap & Chart.js dari CDN, jadi **tidak perlu npm**.

Data contoh fiktif (opsional, hanya lokal): `php artisan db:seed --class=DemoSeeder` (akun `coach@archer.test` / `password`).

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

- `config/archery.php` — metrik, benchmark, grup profil, kode teknik, definisi form. Ubah di sini untuk menambah metrik tes.
- `app/Support/ProfileService.php` — rumus profil.
- `app/Http/Controllers/RecordController.php` — CRUD generik data berseri (form dibuat dari config).
- `database/migrations/2026_10_11_000001_create_athletes_table.php` — seluruh skema.
- `tests/Unit/ProfileServiceTest.php` — uji rumus terhadap angka Excel.

Tes: `php artisan test`

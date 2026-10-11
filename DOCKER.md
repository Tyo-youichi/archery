# Menjalankan Archer Monitor dengan Docker

Butuh **Docker** + **Docker Compose** (Docker Desktop sudah termasuk).

## Jalankan

```bash
# build + start (app PHP-FPM, nginx, MariaDB, phpMyAdmin)
docker compose up -d --build

# lihat log aplikasi (composer install, migrate berjalan di sini)
docker compose logs -f app
```

Saat pertama kali, container `app` otomatis: menyiapkan `.env`, `composer install`
(atau `composer update` bila lock tidak sinkron), `key:generate`, membuat folder
upload, menunggu DB, lalu `php artisan migrate`.

Akses:
- Aplikasi: http://localhost:8080
- phpMyAdmin: http://localhost:8081  (server: `db`, user `archer` / `secret`)
- MariaDB dari host: `127.0.0.1:33060`

## Seed data (akun + atlet dummy + RBAC)

```bash
# sekali saja, isi data contoh
docker compose exec app php artisan db:seed --class=DemoSeeder
# atau set SEED=true di .env lalu `docker compose up -d` untuk seed otomatis saat start
```

Akun demo: `admin@archer.test` / `Admin#2026`, `coach@archer.test` / `Coach#2026`,
`athlete@archer.test` / `Athlete#2026`.

## Perintah artisan lain

```bash
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan test
docker compose exec app php artisan archery:import-excel storage/app/ATHTRIX.XLSM --year=2026 --test-date=2026-01-15
```

## Konfigurasi (opsional, lewat .env di host sebelum `up`)

| Variabel | Default | Keterangan |
|---|---|---|
| `APP_PORT` | 8080 | Port aplikasi |
| `PMA_PORT` | 8081 | Port phpMyAdmin |
| `DB_PORT_HOST` | 33060 | Port MariaDB yang diekspos ke host |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | archer / archer / secret | Kredensial DB |
| `DB_ROOT_PASSWORD` | root | Password root MariaDB |
| `SEED` | false | `true` = jalankan `db:seed` saat start pertama |

## Stop / reset

```bash
docker compose down           # stop (data DB tetap tersimpan di volume)
docker compose down -v        # stop + hapus data DB
```

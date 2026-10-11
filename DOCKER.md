# Menjalankan Archer Monitor dengan Docker

Butuh **Docker** + **Docker Compose** (Docker Desktop sudah termasuk).

Compose ini **hanya berisi satu service: `app`** (Apache + PHP 8.1 dalam satu
container). **Database berada di luar compose** — siapkan MariaDB sendiri (di
host atau server lain), lalu arahkan app ke sana lewat variabel `DB_*`.

## Siapkan database (sekali)

Pakai MariaDB/MySQL yang sudah ada, atau jalankan satu saja via Docker:

```bash
docker run -d --name archer-db -p 3306:3306 \
  -e MARIADB_DATABASE=archer -e MARIADB_USER=archer \
  -e MARIADB_PASSWORD=secret -e MARIADB_ROOT_PASSWORD=root \
  -v archer_db:/var/lib/mysql mariadb:10.11
```

Secara default app mencari DB di `host.docker.internal:3306` (MariaDB di host).
Kalau DB di tempat lain, set `DB_HOST`/`DB_PORT` (lihat tabel di bawah).

## Jalankan app

```bash
docker compose up -d --build

# lihat log (key:generate, tunggu DB, migrate berjalan di sini)
docker compose logs -f app
```

Saat start pertama, container otomatis: menyiapkan `.env`, `key:generate`,
menyiapkan folder upload, menunggu DB, lalu `php artisan migrate`.

Akses aplikasi: **http://localhost:7777**

## Seed data (akun + atlet dummy + RBAC)

```bash
docker compose exec app php artisan db:seed --class=DemoSeeder
# atau set SEED=true lalu `docker compose up -d` untuk seed otomatis saat start pertama
```

Akun demo: `admin@archer.test` / `Admin#2026`, `coach@archer.test` / `Coach#2026`,
`athlete@archer.test` / `Athlete#2026`.

## Data persisten (volume di luar container)

| Volume | Mount | Isi |
|---|---|---|
| `uploads_data` | `/var/www/public/uploads` | Foto atlet (`public/uploads/athletes`) |
| `storage_data` | `/var/www/storage` | Log, cache, session Laravel |

Foto yang di-upload tetap ada walau container di-rebuild.

## Konfigurasi (lewat .env di host sebelum `up`, atau environment)

| Variabel | Default | Keterangan |
|---|---|---|
| `APP_PORT` | 7777 | Port aplikasi di host |
| `APP_URL` | http://localhost:7777 | URL aplikasi |
| `DB_HOST` | host.docker.internal | Alamat MariaDB (ganti bila DB di server lain) |
| `DB_PORT` | 3306 | Port MariaDB |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | archer / archer / secret | Kredensial DB |
| `SEED` | false | `true` = jalankan `DemoSeeder` saat start pertama |

## Perintah artisan lain

```bash
docker compose exec app php artisan migrate:fresh --seed --seeder=DemoSeeder
docker compose exec app php artisan optimize:clear
```

## Catatan

- **Kode di-bake ke image.** Setelah mengubah kode, jalankan ulang
  `docker compose up -d --build`. Untuk pengembangan (edit langsung tanpa
  rebuild), aktifkan baris bind-mount `- ./:/var/www` di `docker-compose.yml`.
- Service nginx/phpMyAdmin dari versi sebelumnya sudah tidak dipakai; file
  `docker/nginx/` boleh dihapus.

## Stop / reset

```bash
docker compose down            # stop (volume upload & storage tetap tersimpan)
docker compose down -v         # stop + hapus volume (upload & storage ikut terhapus)
```

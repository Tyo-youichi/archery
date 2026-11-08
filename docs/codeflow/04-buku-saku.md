# 4 · Buku Saku (Shoot Log)

Tabel Monitor Shoot 100 Arrow — catatan mingguan (Senin–Sabtu) per atlet.

Controller: `app/Http/Controllers/ShootLogController.php`
Model: `app/Models/ShootLog.php`
View: `resources/views/shoot-log/week.blade.php`

| Route | Name | Method | Akses RBAC |
|-------|------|--------|------------|
| `GET /athletes/{athlete}/shoot-log` | `shoot-log.edit` | `edit` | `*` (atlet: miliknya; dicek controller) |
| `PUT /athletes/{athlete}/shoot-log` | `shoot-log.update` | `update` | `*` |

- **Kepemilikan**: `authorizeView($athlete)` (trait `AuthorizesAthlete`) — staf semua, atlet hanya miliknya.
- **Minggu**: param query/input `week`; `weekStart()` → Senin (`startOfWeek`).

## `edit()`
1. `authorizeView`.
2. Ambil `shootLogs` dalam rentang Senin–Sabtu minggu itu, di-key per tanggal.
3. Bangun 6 hari dari `config('archery.shoot_days')` (Senin…Sabtu) + log tiap hari.
4. **Rekap 8 minggu terakhir**: jumlah hari `shot_100 = true` per minggu.
5. View `shoot-log/week.blade.php` + navigasi `prev`/`next` minggu.

## `update()`
1. `authorizeView`.
2. Validasi: `techniques` (kode a–f dari `config('archery.technique_codes')`), `shot_100` (`ya`/`tdk`), `notes`.
3. Loop 6 hari: baris kosong → hapus `ShootLog` hari itu; ada isi → `updateOrCreate` (`shot_100` = `ya`).
4. Redirect balik ke `shoot-log.edit` minggu yang sama.

| Model | `App\Models\ShootLog` (cast `techniques` = array, `log_date` = date) |
|-------|------|
| Config | `config/archery.php` → `shoot_days`, `technique_codes` |

> Dipakai juga oleh Dashboard (rekap shoot 100 arrow minggu ini) — lihat [02-dashboard.md](02-dashboard.md).

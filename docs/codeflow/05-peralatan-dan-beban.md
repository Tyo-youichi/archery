# 5 · Peralatan & Printout Beban

Dua fitur kecil di halaman profil atlet.

## 5.1 Peralatan — `EquipmentController`

Controller: `app/Http/Controllers/EquipmentController.php`
Model: `app/Models/Equipment.php` (tabel `equipments`, relasi `hasOne` dari `Athlete`)
View: `resources/views/equipment/form.blade.php`

| Route | Name | Method | Akses RBAC |
|-------|------|--------|------------|
| `GET /athletes/{athlete}/equipment` | `equipment.edit` | `edit` | admin, coach, athlete |
| `PUT /athletes/{athlete}/equipment` | `equipment.update` | `update` | admin, coach, athlete |

- **Kepemilikan**: `authorizeView($athlete)` (trait) — atlet boleh isi peralatannya sendiri.
- **Field**: dinamis dari `config('archery.equipment_fields')` (Riser, Limbs, Sight, … Stand).
- `update()`: validasi tiap field `nullable|string|max:150` lalu `equipment()->updateOrCreate([], ...)`.

## 5.2 Printout Beban (1RM) — `BebanController`

Controller: `app/Http/Controllers/BebanController.php`
Model: `app/Models/LoadTest.php`
View: `resources/views/beban/print.blade.php`

| Route | Name | Method | Akses RBAC |
|-------|------|--------|------------|
| `GET /athletes/{athlete}/beban/cetak` | `beban.print` | `print` | `*` (atlet: miliknya; dicek controller) |

- **Kepemilikan**: `authorizeView($athlete)` (trait).
- `print()`: ambil **1RM terbaru per latihan** (`LoadTest`, unik per `exercise`, urut nama) → view ramah cetak (`window.print()` / Simpan PDF).

> Input beban-nya sendiri lewat `RecordController` type `load` ([03-atlet-dan-data.md](03-atlet-dan-data.md)).
> Rekap beban lintas-atlet ada di menu **Laporan** ([07-laporan.md](07-laporan.md)).

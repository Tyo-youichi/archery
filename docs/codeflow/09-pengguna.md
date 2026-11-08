# 9 · Pengguna

Kelola akun. Non-atlet (admin/coach) dikelola penuh di sini; akun atlet ikut tampil agar
bisa dinonaktifkan saat keluar klub (biodatanya tetap di menu Atlet).

Controller: `app/Http/Controllers/UserController.php`
Model: `app/Models/User.php`
View: `resources/views/users/{index,form}.blade.php`
Helper: `app/Support/Rbac.php` (daftar role)

| Route | Name | Method | Akses RBAC |
|-------|------|--------|------------|
| `GET /users` | `users.index` | `index` | admin |
| `GET /users/create` | `users.create` | `create` | admin |
| `POST /users` | `users.store` | `store` | admin |
| `GET /users/{user}/edit` | `users.edit` | `edit` | admin |
| `PUT /users/{user}` | `users.update` | `update` | admin |
| `PUT /users/{user}/aktif` | `users.toggle` | `toggleActive` | admin |
| `DELETE /users/{user}` | `users.destroy` | `destroy` | admin |

> Semua tercakup pola RBAC `users.*` → admin. `users.toggle` didaftarkan sebelum
> `Route::resource('users')` di `web.php`.

## Method
- **`index()`**: semua user (staf lebih dulu, lalu atlet), filter `q` & `status` (aktif/nonaktif). Kirim `athleteRole` agar view bisa membedakan baris atlet.
- **`create/store/edit/update`**: form akun non-atlet. `roleOptions()` = semua role **kecuali** `athlete` (akun atlet tak diedit di sini — `edit` menolak & arahkan ke menu Atlet). Password opsional saat edit.
- **`toggleActive()`**: balik `is_active`. Pengaman: tak bisa menonaktifkan diri sendiri atau **admin aktif terakhir**. Akun nonaktif tak bisa login (dicek di `AuthController@login` & middleware `rbac`).
- **`destroy()`**: pengaman admin terakhir & akun tertaut atlet (disarankan nonaktifkan saja).

| Model | `App\Models\User` (`is_active` boolean, `role`, relasi `athlete`) |
|-------|------|
| Migrasi | `database/migrations/2026_10_23_000002_add_is_active_to_users_table.php` |

## Keterkaitan status aktif
- `AuthController@login`: tolak login bila `is_active = false`.
- `EnforceRoutePermission` (middleware `rbac`): keluarkan sesi berjalan bila akun dinonaktifkan.

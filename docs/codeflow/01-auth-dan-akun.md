# 1 · Autentikasi & Akun

Controller: `app/Http/Controllers/AuthController.php` & `app/Http/Controllers/AccountController.php`
Model: `app/Models/User.php`, `app/Models/Athlete.php`
View: `resources/views/auth/{login,register}.blade.php`

## Login

| Aspek | Detail |
|-------|--------|
| Route | `GET /login` → `login` → `AuthController@showLogin` · `POST /login` (throttle:10,1) → `AuthController@login` |
| Middleware | grup `guest` (hanya untuk yang belum login) |
| Proses | `Auth::attempt()`; bila sukses **cek `is_active`** — akun nonaktif ditolak. Lalu `session()->regenerate()` → redirect `dashboard.index`. |
| Model | `User` |
| View | `resources/views/auth/login.blade.php` |

## Registrasi mandiri atlet

| Aspek | Detail |
|-------|--------|
| Route | `GET /register` → `register` → `AuthController@showRegister` · `POST /register` → `AuthController@register` |
| Middleware | grup `guest` |
| Proses | Validasi; `DB::transaction` membuat **`User`** (role = `config('rbac.default_role')` = `athlete`) **+ `Athlete`** (kode via `Athlete::nextCode()`). Lalu auto-login → `dashboard.index`. |
| Model | `User`, `Athlete` |
| Config | `config/archery.php` → `divisions`, `registration_types` |
| View | `resources/views/auth/register.blade.php` |

## Logout

| Aspek | Detail |
|-------|--------|
| Route | `POST /logout` → `logout` → `AuthController@logout` — **`->withoutMiddleware('rbac')`** |
| Proses | `Auth::logout()` + invalidasi sesi → redirect `login` |
| Kenapa tanpa rbac | Supaya user tidak pernah terkunci dari tombol keluar, apa pun isi tabel izin. |

## Edit akun sendiri (email/telepon/kata sandi)

| Aspek | Detail |
|-------|--------|
| Route | `PUT /akun` → `account.update` → `AccountController@update` |
| Akses RBAC | `account.*` → `*` (semua user login) |
| Proses | Validasi email unik (abaikan diri sendiri), telepon, password opsional (kosong = tidak diubah; model auto-hash). Update user yang sedang login → `back()`. |
| Model | `User` |
| View | **Tidak punya view sendiri** — panelnya menyatu di form edit biodata atlet: `resources/views/athletes/form.blade.php` (muncul hanya untuk pemilik akun). Lihat [03-atlet-dan-data.md](03-atlet-dan-data.md). |

> Catatan: akun non-atlet (admin/coach) dibuat & dikelola admin di menu **Pengguna** ([09-pengguna.md](09-pengguna.md)).

# 11 · Akses (RBAC): Role, Izin Route, Menu

Inti kontrol akses. Keputusan izin dibaca dari **tabel DB** berdasarkan **nama route**,
di-seed dari `config/rbac.php` dan di-cache. Deny-by-default.

Controllers: `RoleController`, `RoutePermissionController`, `MenuController`
Helper: `app/Support/Rbac.php`
Middleware: `app/Http/Middleware/EnforceRoutePermission.php` (alias `rbac`)
Models: `app/Models/{Role,RoutePermission,Menu}.php`
Views: `resources/views/rbac/{roles,permissions,menus}/{index,form}.blade.php` + `rbac/_tabs.blade.php`

## Routes (grup prefix `rbac`, name `rbac.*` → admin)

| Route | Name | Controller |
|-------|------|-----------|
| `GET /rbac` | `rbac.index` | redirect → `rbac.roles.index` |
| resource `roles` (kecuali show) | `rbac.roles.*` | `RoleController` |
| resource `permissions` (param `permission`) | `rbac.permissions.*` | `RoutePermissionController` |
| resource `menus` (param `menu`) | `rbac.menus.*` | `MenuController` |

Semua CRUD memanggil **`Rbac::flush()`** setelah berubah (buang cache snapshot RBAC).

## Tiga tabel

| Tabel / Model | Isi | Dipakai untuk |
|---------------|-----|---------------|
| `roles` / `Role` | kode, nama, `is_staff`, `sort` | daftar role + penanda staf |
| `route_permissions` / `RoutePermission` | `pattern` (pola nama route, wildcard), `roles` (JSON) | keputusan izin per route |
| `menus` / `Menu` | `label`, `route`, `param`, `roles` (JSON), `is_active`, `sort` | menu sidebar sesuai role |

### RoleController
`index` (role + jumlah user per role), CRUD. `destroy` menolak bila role masih dipakai user
atau dipakai di izin route/menu. Kode role tak bisa diubah (dipakai di `users.role`).

### RoutePermissionController
`index` juga mengirim **daftar nama route aplikasi** (dari `Route::getRoutes()`) sebagai referensi
saat admin membuat pola. `pattern` unik; `roles` minimal 1.

### MenuController
CRUD item menu. `param = self_athlete` → menu yang menunjuk data atlet milik user sendiri.

## `App\Support\Rbac` (otak)
| Method | Fungsi |
|--------|--------|
| `routeAllowed($name, $role)` | cocokkan nama route dengan pola (`Str::is`); `'*'` = semua login; **deny-by-default** |
| `menu($user)` | daftar menu yang boleh dilihat user (resolve URL, skip `self_athlete` tanpa data atlet) |
| `isStaff($role)` / `label($role)` / `roles()` | util role |
| `flush()` | buang cache snapshot (`rememberForever`) |

Data dibaca dari tabel (via model), fallback ke `config/rbac.php` bila tabel belum ada/kosong.

## Middleware `rbac` — `EnforceRoutePermission`
Pada tiap request bergrup auth:
1. Jika akun `is_active = false` → logout + redirect `login`.
2. Ambil nama route → `Rbac::routeAllowed($name, $user->role)`; bila tidak → `abort(403)`.

`logout` memakai `->withoutMiddleware('rbac')` agar selalu bisa keluar.

## Seed & cache
- Sumber awal: `config/rbac.php` (`roles`, `routes`, `menus`). Di-seed oleh `database/seeders/RbacSeeder.php`.
- Setelah menambah route/menu baru atau mengubah `config/rbac.php`:
  ```bash
  php artisan db:seed --class=RbacSeeder
  php artisan optimize:clear
  ```
- Menu sidebar dirender di `resources/views/layouts/app.blade.php` lewat `Rbac::menu(auth()->user())`.

> ⚠️ Pastikan baris `rbac.*` selalu ada di tabel izin; jika terhapus, admin bisa terkunci
> dari halaman RBAC — pulihkan via `php artisan db:seed --class=RbacSeeder`.

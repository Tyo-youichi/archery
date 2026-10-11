<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoutePermission;
use App\Support\Rbac;
use Illuminate\Database\Seeder;

/**
 * Mengisi tabel RBAC (roles, route_permissions, menus) DARI config/rbac.php.
 * Config = sumber definisi; DB = sumber runtime. Idempotent (updateOrCreate).
 *
 *   php artisan db:seed --class=RbacSeeder
 */
class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $sort = 0;
        foreach (config('rbac.roles', []) as $code => $name) {
            Role::updateOrCreate(['code' => $code], [
                'name' => $name,
                'is_staff' => in_array($code, config('rbac.staff', []), true),
                'sort' => $sort++,
            ]);
        }

        // Izin per-route (urutan config dipertahankan lewat 'sort': match pertama menang)
        $sort = 0;
        foreach (config('rbac.routes', []) as $pattern => $roles) {
            RoutePermission::updateOrCreate(['pattern' => $pattern], [
                'roles' => array_values($roles),
                'sort' => $sort++,
            ]);
        }

        // Menu
        $sort = 0;
        foreach (config('rbac.menus', []) as $m) {
            Menu::updateOrCreate(['label' => $m['label'], 'route' => $m['route']], [
                'param' => $m['param'] ?? null,
                'roles' => array_values($m['roles']),
                'sort' => $sort++,
                'is_active' => true,
            ]);
        }

        Rbac::flush();
    }
}

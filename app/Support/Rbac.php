<?php

namespace App\Support;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoutePermission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Pusat logika RBAC. Sumber data = TABEL DB (roles, route_permissions, menus),
 * di-seed dari config/rbac.php. Bila tabel belum ada / kosong, otomatis
 * fallback ke config/rbac.php sehingga aplikasi tetap jalan sebelum di-seed.
 * Hasil baca di-cache; panggil Rbac::flush() setelah mengubah data RBAC.
 */
class Rbac
{
    private const CACHE_KEY = 'rbac.snapshot';

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Snapshot seluruh data RBAC (dari DB atau fallback config), ter-cache.
     * @return array{roles:array,staff:array,routes:array,menus:array,default_role:string}
     */
    private static function data(): array
    {
        if (! config('rbac.cache', true)) {
            return self::load();
        }

        return Cache::rememberForever(self::CACHE_KEY, fn () => self::load());
    }

    private static function load(): array
    {
        $cfg = [
            'roles'        => config('rbac.roles', []),
            'staff'        => config('rbac.staff', []),
            'routes'       => config('rbac.routes', []),
            'menus'        => config('rbac.menus', []),
            'default_role' => config('rbac.default_role', 'athlete'),
        ];

        // Fallback ke config bila DB belum siap (migrasi/seed belum jalan).
        try {
            if (! Schema::hasTable('roles')) {
                return $cfg;
            }
        } catch (\Throwable) {
            return $cfg;
        }

        $roles = Role::orderBy('sort')->orderBy('code')->get();
        if ($roles->isEmpty()) {
            return $cfg;   // tabel ada tapi belum di-seed
        }

        $routes = [];
        foreach (RoutePermission::orderBy('sort')->orderBy('id')->get() as $p) {
            $routes[$p->pattern] = $p->roles;
        }

        $menus = Menu::where('is_active', true)->orderBy('sort')->orderBy('id')->get()
            ->map(fn ($m) => array_filter([
                'label' => $m->label, 'route' => $m->route, 'roles' => $m->roles, 'param' => $m->param,
            ], fn ($v) => $v !== null))->all();

        return [
            'roles'        => $roles->pluck('name', 'code')->all(),
            'staff'        => $roles->where('is_staff', true)->pluck('code')->all(),
            'routes'       => $routes,
            'menus'        => $menus,
            'default_role' => config('rbac.default_role', 'athlete'),
        ];
    }

    /** @return array<string,string> kode role => label */
    public static function roles(): array
    {
        return self::data()['roles'];
    }

    public static function defaultRole(): string
    {
        return self::data()['default_role'];
    }

    public static function label(?string $role): string
    {
        return self::data()['roles'][$role] ?? ($role ?? '-');
    }

    public static function isStaff(?string $role): bool
    {
        return in_array($role, self::data()['staff'], true);
    }

    /**
     * Apakah $role boleh mengakses route $name (izin per-route).
     * Kebijakan DENY-BY-DEFAULT: route yang TIDAK terdaftar di tabel izin => DITOLAK.
     * Tanda '*' pada daftar role = boleh untuk semua user terautentikasi.
     * (Route tanpa nama tidak melewati pengecekan ini — lihat EnforceRoutePermission.)
     */
    public static function routeAllowed(string $name, ?string $role): bool
    {
        foreach (self::data()['routes'] as $pattern => $allowed) {
            if (Str::is($pattern, $name)) {
                return in_array('*', $allowed, true) || in_array($role, $allowed, true);
            }
        }

        return false;   // deny-by-default
    }

    /**
     * Menu yang boleh dilihat user, sudah di-resolve URL-nya.
     * @return array<int,array{label:string,url:string,route:string}>
     */
    public static function menu(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $items = [];
        foreach (self::data()['menus'] as $m) {
            if (! in_array($user->role, $m['roles'], true)) {
                continue;
            }
            if (! Route::has($m['route'])) {
                continue;
            }

            $params = [];
            if (($m['param'] ?? null) === 'self_athlete') {
                $athlete = $user->athlete;
                if (! $athlete) {
                    continue;
                }
                $params = [$athlete];
            }

            $items[] = [
                'label' => $m['label'],
                'url'   => route($m['route'], $params),
                'route' => $m['route'],
            ];
        }

        return $items;
    }
}

<?php

/*
|--------------------------------------------------------------------------
| RBAC — Role Based Access Control (berbasis ROUTE, bukan per-endpoint)
|--------------------------------------------------------------------------
| Satu sumber kebenaran untuk: daftar role, role staf, izin per-route, dan menu.
| Ubah akses cukup di sini — tidak perlu menyentuh controller atau route.
*/

return [

    // Daftar role + label tampilan. 'admin' adalah peran tertinggi (superadmin).
    'roles' => [
        'admin'   => 'Administrator',
        'coach'   => 'Pelatih',
        'athlete' => 'Atlet',
    ],

    // Role default saat registrasi mandiri.
    'default_role' => 'athlete',

    // Cache snapshot RBAC dari DB. Setelah mengubah data RBAC, panggil Rbac::flush()
    // (RbacSeeder sudah melakukannya). Set false saat debugging.
    'cache' => true,

    // Role yang dianggap "staf" (akses manajemen data). Dipakai isStaff().
    'staff' => ['admin', 'coach'],

    /*
    | Izin per-ROUTE NAME. Key boleh memakai wildcard "*" (Str::is) pada NAMA route.
    | Yang lebih spesifik diletakkan DI ATAS yang berpola, karena match pertama menang.
    | KEBIJAKAN: DENY-BY-DEFAULT — route yang TIDAK tercantum di sini akan DITOLAK.
    | Tanda '*' pada daftar role = boleh untuk semua user login. Jadi SETIAP halaman
    | baru wajib didaftarkan di sini (atau lewat UI RBAC) agar bisa diakses.
    | Kepemilikan data (mis. atlet lihat profil sendiri) tetap dicek di controller.
    */
    'routes' => [
        'rbac.*'           => ['admin'],              // pengelolaan RBAC: admin saja
        'metrics.*'        => ['admin'],              // pengelolaan metrik & benchmark: admin saja
        'users.*'          => ['admin'],              // pengelolaan pengguna non-atlet: admin saja
        'athletes.destroy' => ['admin'],              // hanya admin boleh hapus atlet
        'athletes.index'   => ['admin', 'coach'],
        'athletes.create'  => ['admin', 'coach'],
        'athletes.store'   => ['admin', 'coach'],
        // Atlet boleh edit BIODATA miliknya sendiri (kepemilikan + batasan field di controller).
        'athletes.edit'    => ['admin', 'coach', 'athlete'],
        'athletes.update'  => ['admin', 'coach', 'athlete'],
        // Atlet boleh isi PERALATAN miliknya sendiri (kepemilikan dicek di controller).
        'equipment.*'      => ['admin', 'coach', 'athlete'],
        'records.*'        => ['admin', 'coach'],
        'h2h.*'            => ['admin', 'coach'],

        // Boleh semua user login (tanda '*'); kepemilikan data dicek di controller.
        'dashboard.*'      => ['*'],
        'athletes.show'    => ['*'],
        'shoot-log.*'      => ['*'],
        'beban.*'          => ['*'],   // printout beban: staf semua, atlet miliknya (cek di controller)
        // (logout tidak di sini — route-nya pakai withoutMiddleware('rbac'), selalu boleh)
    ],

    /*
    | Menu sidebar. Tampil hanya bila role user ada di 'roles' item.
    | 'param' => 'self_athlete' => pakai atlet milik user (link profil/Buku Saku sendiri).
    | Item dilewati bila route-nya tidak ada atau param tidak bisa di-resolve.
    */
    'menus' => [
        ['label' => 'Dashboard',    'route' => 'dashboard.index', 'roles' => ['admin', 'coach', 'athlete']],
        ['label' => 'Atlet',        'route' => 'athletes.index', 'roles' => ['admin', 'coach']],
        ['label' => 'Head to Head', 'route' => 'h2h.index',      'roles' => ['admin', 'coach']],
        ['label' => 'Metrik & BM', 'route' => 'metrics.index',    'roles' => ['admin']],
        ['label' => 'Pengguna',    'route' => 'users.index',      'roles' => ['admin']],
        ['label' => 'Akses (RBAC)', 'route' => 'rbac.index',      'roles' => ['admin']],
        ['label' => 'Profil Saya',  'route' => 'athletes.show',  'roles' => ['athlete'], 'param' => 'self_athlete'],
        ['label' => 'Buku Saku',    'route' => 'shoot-log.edit', 'roles' => ['athlete'], 'param' => 'self_athlete'],
        ['label' => 'Peralatan Saya', 'route' => 'equipment.edit', 'roles' => ['athlete'], 'param' => 'self_athlete'],
    ],
];

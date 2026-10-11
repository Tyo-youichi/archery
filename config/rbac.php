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
    | Izin per-ROUTE NAME. Key boleh memakai wildcard "*" (Str::is).
    | Yang lebih spesifik diletakkan DI ATAS yang berpola, karena match pertama menang.
    | Route yang TIDAK tercantum: boleh untuk semua user terautentikasi
    | (kepemilikan data, mis. atlet lihat profil sendiri, dicek di controller).
    */
    'routes' => [
        'athletes.destroy' => ['admin'],              // hanya admin boleh hapus atlet
        'athletes.index'   => ['admin', 'coach'],
        'athletes.create'  => ['admin', 'coach'],
        'athletes.store'   => ['admin', 'coach'],
        'athletes.edit'    => ['admin', 'coach'],
        'athletes.update'  => ['admin', 'coach'],
        'equipment.*'      => ['admin', 'coach'],
        'records.*'        => ['admin', 'coach'],
        'h2h'              => ['admin', 'coach'],

        // Tidak diatur (boleh semua role login, cek kepemilikan di controller):
        //   dashboard, athletes.show, shoot-log.edit, shoot-log.update, logout
    ],

    /*
    | Menu sidebar. Tampil hanya bila role user ada di 'roles' item.
    | 'param' => 'self_athlete' => pakai atlet milik user (link profil/Buku Saku sendiri).
    | Item dilewati bila route-nya tidak ada atau param tidak bisa di-resolve.
    */
    'menus' => [
        ['label' => 'Dashboard',    'route' => 'dashboard',      'roles' => ['admin', 'coach', 'athlete']],
        ['label' => 'Atlet',        'route' => 'athletes.index', 'roles' => ['admin', 'coach']],
        ['label' => 'Head to Head', 'route' => 'h2h',            'roles' => ['admin', 'coach']],
        ['label' => 'Profil Saya',  'route' => 'athletes.show',  'roles' => ['athlete'], 'param' => 'self_athlete'],
        ['label' => 'Buku Saku',    'route' => 'shoot-log.edit', 'roles' => ['athlete'], 'param' => 'self_athlete'],
    ],
];

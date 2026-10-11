<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Akun untuk ketiga role. HANYA untuk pengembangan/percobaan — ganti password di produksi.
 *   php artisan db:seed --class=RoleUserSeeder
 *
 *   admin@archer.test    / Admin#2026     (role admin / superadmin tertinggi)
 *   coach@archer.test    / Coach#2026     (role pelatih)
 *   athlete@archer.test  / Athlete#2026   (role atlet; ditautkan ke atlet DUM1 oleh DemoSeeder)
 */
class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Super Admin',  'email' => 'admin@archer.test',   'role' => 'admin',   'hash' => '$2y$12$volq2Od1VCz0i9nb4.7/kOXg0VRtFoWAkm3D6wQ.ZJB.ttjFikFre'],
            ['name' => 'Pelatih Demo', 'email' => 'coach@archer.test',   'role' => 'coach',   'hash' => '$2y$12$G6l7B/y6xnczInOcIdDPW.8aqFPtNxZxcrJG3g6362mHEAdHfXfD6'],
            ['name' => 'Atlet Demo',   'email' => 'athlete@archer.test', 'role' => 'athlete', 'hash' => '$2y$12$RKga0ycxcq.XGD2Bwq0IN.bz.RG6Ogx4yNbWAR0DCS4D8LW84Qyzi'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'role' => $u['role'], 'password' => $u['hash'], 'phone' => null]
            );
        }
    }
}

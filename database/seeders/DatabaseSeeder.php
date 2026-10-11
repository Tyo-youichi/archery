<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // RBAC (roles, route_permissions, menus) + akun 3 role + referensi Yo-Yo.
        $this->call([
            RbacSeeder::class,
            RoleUserSeeder::class,
            YoyoReferenceSeeder::class,
            MetricSeeder::class,
        ]);

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

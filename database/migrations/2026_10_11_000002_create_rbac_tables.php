<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel RBAC. Didefinisikan di config/rbac.php (sumber seed) dan disimpan di DB
 * agar bisa diubah tanpa deploy. Aplikasi membaca dari tabel ini (dengan cache).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Daftar role (admin, coach, athlete, atau tambahan baru).
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();        // dipakai di users.role
            $table->string('name', 100);                 // label tampilan
            $table->boolean('is_staff')->default(false);  // akses manajemen data
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        // Izin per-ROUTE: pola nama route -> daftar kode role yang boleh.
        Schema::create('route_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('pattern', 150)->unique();     // mis. athletes.index, records.*
            $table->json('roles');                        // ["admin","coach"]
            $table->unsignedSmallInteger('sort')->default(0);  // match pertama (sort kecil) menang
            $table->string('description', 200)->nullable();
            $table->timestamps();
        });

        // Menu sidebar: tampil sesuai role.
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100);
            $table->string('route', 150);
            $table->string('param', 50)->nullable();      // mis. self_athlete
            $table->json('roles');                        // role yang boleh melihat
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
        Schema::dropIfExists('route_permissions');
        Schema::dropIfExists('roles');
    }
};

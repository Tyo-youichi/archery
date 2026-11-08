<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Aman untuk MariaDB/MySQL lama di shared hosting (indeks utf8mb4).
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);

        // Semua pagination memakai view kustom (Bootstrap 5) yang SELALU tampil,
        // termasuk saat hanya satu halaman, agar tampilan tabel konsisten.
        Paginator::defaultView('vendor.pagination.app');
    }
}

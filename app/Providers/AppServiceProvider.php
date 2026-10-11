<?php

namespace App\Providers;

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
        //
    }
}

<?php

use App\Http\Controllers\AthleteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\HeadToHeadController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ShootLogController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Atlet boleh melihat profilnya sendiri & mengisi Buku Saku (dicek di controller).
    Route::get('/athletes/{athlete}', [AthleteController::class, 'show'])->name('athletes.show')->whereNumber('athlete');
    Route::get('/athletes/{athlete}/shoot-log', [ShootLogController::class, 'edit'])->name('shoot-log.edit');
    Route::put('/athletes/{athlete}/shoot-log', [ShootLogController::class, 'update'])->name('shoot-log.update');

    // Khusus admin & pelatih.
    Route::middleware('role:admin,coach')->group(function () {
        Route::get('/athletes', [AthleteController::class, 'index'])->name('athletes.index');
        Route::get('/athletes/create', [AthleteController::class, 'create'])->name('athletes.create');
        Route::post('/athletes', [AthleteController::class, 'store'])->name('athletes.store');
        Route::get('/athletes/{athlete}/edit', [AthleteController::class, 'edit'])->name('athletes.edit');
        Route::put('/athletes/{athlete}', [AthleteController::class, 'update'])->name('athletes.update');
        Route::delete('/athletes/{athlete}', [AthleteController::class, 'destroy'])->name('athletes.destroy');

        Route::get('/athletes/{athlete}/equipment', [EquipmentController::class, 'edit'])->name('equipment.edit');
        Route::put('/athletes/{athlete}/equipment', [EquipmentController::class, 'update'])->name('equipment.update');

        Route::prefix('/athletes/{athlete}/records/{type}')->name('records.')->group(function () {
            Route::get('/create', [RecordController::class, 'create'])->name('create');
            Route::post('/', [RecordController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [RecordController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::put('/{id}', [RecordController::class, 'update'])->name('update')->whereNumber('id');
            Route::delete('/{id}', [RecordController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });

        Route::get('/head-to-head', [HeadToHeadController::class, 'index'])->name('h2h');
    });
});

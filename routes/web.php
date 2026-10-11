<?php

use App\Http\Controllers\AthleteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\HeadToHeadController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MetricController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoutePermissionController;
use App\Http\Controllers\ShootLogController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

/*
 * Semua route di bawah ini terautentikasi DAN dilindungi RBAC berbasis route.
 * Middleware 'rbac' mengambil keputusan izin dari config/rbac.php berdasarkan
 * nama route — tidak perlu menandai role per route di sini.
 * Route yang tidak diatur di config boleh diakses semua user login; kepemilikan
 * data (atlet hanya datanya sendiri) tetap dicek di controller.
 */
Route::middleware(['auth', 'rbac'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil & Buku Saku: atlet untuk dirinya sendiri, staf untuk semua (dicek di controller).
    Route::get('/athletes/{athlete}', [AthleteController::class, 'show'])->name('athletes.show')->whereNumber('athlete');
    Route::get('/athletes/{athlete}/shoot-log', [ShootLogController::class, 'edit'])->name('shoot-log.edit');
    Route::put('/athletes/{athlete}/shoot-log', [ShootLogController::class, 'update'])->name('shoot-log.update');

    // Manajemen atlet (izin diatur di config/rbac.php: staf; hapus = admin saja).
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

    // Pengelolaan metrik & benchmark (CRUD). Khusus admin (hard guard + izin RBAC 'metrics.*').
    Route::middleware('role:admin')->group(function () {
        Route::resource('metrics', MetricController::class)->except(['show']);
    });

    /*
     * Pengelolaan RBAC (CRUD roles, izin route, menu). Khusus admin.
     * Hard guard 'role:admin' dipasang eksplisit agar aman walau data RBAC di DB
     * belum di-seed (tidak bergantung pada config/DB route_permissions).
     */
    Route::middleware('role:admin')->prefix('rbac')->name('rbac.')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('permissions', RoutePermissionController::class)->except(['show'])
            ->parameters(['permissions' => 'permission']);
        Route::resource('menus', MenuController::class)->except(['show'])
            ->parameters(['menus' => 'menu']);
    });
});

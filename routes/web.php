<?php

use App\Http\Controllers\AthleteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BebanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\HeadToHeadController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MetricController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoutePermissionController;
use App\Http\Controllers\ShootLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

/*
 * Semua route di bawah ini terautentikasi DAN dilindungi RBAC berbasis route.
 * Middleware 'rbac' mengambil keputusan izin dari TABEL RBAC (di-seed dari
 * config/rbac.php) berdasarkan nama route. Kebijakan DENY-BY-DEFAULT: route yang
 * tidak terdaftar di tabel izin akan DITOLAK — tidak ada hard-guard di sini,
 * seluruh akses diatur dari tabel/UI RBAC. Kepemilikan data (atlet hanya datanya
 * sendiri) tetap dicek di controller.
 */
Route::middleware(['auth', 'rbac', 'payment'])->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard.index'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    // Logout TIDAK melalui pengecekan RBAC: user yang sudah login harus selalu bisa
    // keluar, apa pun isi tabel izin (mencegah terperangkap tanpa bisa logout).
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->withoutMiddleware('rbac');

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

    // Printout data beban (1RM) per atlet — ramah cetak / Simpan PDF.
    Route::get('/athletes/{athlete}/beban/cetak', [BebanController::class, 'print'])->name('beban.print')->whereNumber('athlete');

    // Pembayaran iuran. Atlet: unggah bukti periode berjalan. Admin: verifikasi/tolak.
    Route::get('/pembayaran/saya', [PaymentController::class, 'my'])->name('payment.my');
    Route::post('/pembayaran/saya', [PaymentController::class, 'upload'])->name('payment.upload');
    Route::get('/pembayaran/{payment}/bukti', [PaymentController::class, 'proof'])->name('payment.proof')->whereNumber('payment');
    Route::get('/pembayaran', [PaymentController::class, 'index'])->name('payment.index');
    Route::put('/pembayaran/{payment}/verifikasi', [PaymentController::class, 'verify'])->name('payment.verify')->whereNumber('payment');
    Route::put('/pembayaran/{payment}/tolak', [PaymentController::class, 'reject'])->name('payment.reject')->whereNumber('payment');

    Route::prefix('/athletes/{athlete}/records/{type}')->name('records.')->group(function () {
        Route::get('/create', [RecordController::class, 'create'])->name('create');
        Route::post('/', [RecordController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [RecordController::class, 'edit'])->name('edit')->whereNumber('id');
        Route::put('/{id}', [RecordController::class, 'update'])->name('update')->whereNumber('id');
        Route::delete('/{id}', [RecordController::class, 'destroy'])->name('destroy')->whereNumber('id');
    });

    Route::get('/head-to-head', [HeadToHeadController::class, 'index'])->name('h2h.index');

    // Pengelolaan metrik/benchmark & pengguna non-atlet. Akses murni dari tabel RBAC
    // ('metrics.*' / 'users.*' => admin) — tanpa hard-guard.
    Route::resource('metrics', MetricController::class)->except(['show']);
    Route::resource('users', UserController::class)->except(['show']);

    /*
     * Pengelolaan RBAC (CRUD roles, izin route, menu). Akses dari tabel RBAC
     * ('rbac.*' => admin) — tanpa hard-guard. PENTING: pastikan baris 'rbac.*' selalu
     * ada di tabel izin; jika terhapus, admin bisa terkunci dari halaman ini — pulihkan
     * via CLI: php artisan db:seed --class=RbacSeeder.
     */
    Route::prefix('rbac')->name('rbac.')->group(function () {
        Route::get('/', fn () => redirect()->route('rbac.roles.index'))->name('index');   // rbac.index → landing RBAC
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('permissions', RoutePermissionController::class)->except(['show'])
            ->parameters(['permissions' => 'permission']);
        Route::resource('menus', MenuController::class)->except(['show'])
            ->parameters(['menus' => 'menu']);
    });
});

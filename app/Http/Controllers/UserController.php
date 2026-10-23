<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Rbac;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Manajemen akun pengguna. Akun non-atlet (admin, pelatih) dibuat & diedit penuh
 * di sini. Akun ATLET (dibuat lewat registrasi mandiri) ikut ditampilkan agar bisa
 * dinonaktifkan saat atlet keluar dari klub — tetapi biodatanya dikelola di menu
 * Atlet. Khusus admin (izin RBAC 'users.*').
 */
class UserController extends Controller
{
    /** Role non-atlet yang boleh dibuat/diberikan di sini (semua role kecuali role default atlet). */
    private function roleOptions(): array
    {
        $athlete = config('rbac.default_role', 'athlete');

        return collect(Rbac::roles())->except($athlete)->all();   // ['admin' => 'Administrator', 'coach' => 'Pelatih', ...]
    }

    public function index(Request $request)
    {
        $athleteRole = config('rbac.default_role', 'athlete');

        $users = User::query()
            ->when($request->filled('q'), fn ($w) => $w->where(fn ($q) => $q
                ->where('name', 'like', '%' . $request->q . '%')
                ->orWhere('email', 'like', '%' . $request->q . '%')))
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            // Staf (non-atlet) lebih dulu, lalu atlet; masing-masing urut nama.
            ->orderByRaw("CASE WHEN role = ? THEN 1 ELSE 0 END", [$athleteRole])
            ->orderBy('name')
            ->get();

        return view('users.index', [
            'users'       => $users,
            'roles'       => Rbac::roles(),
            'athleteRole' => $athleteRole,
        ]);
    }

    public function create()
    {
        return view('users.form', ['user' => new User(['role' => 'coach']), 'roles' => $this->roleOptions()]);
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return redirect()->route('users.index')->with('ok', 'Akun pengguna dibuat.');
    }

    public function edit(User $user)
    {
        // Akun atlet tidak diedit di form ini (role-nya tidak ada di pilihan staf);
        // biodatanya dikelola di menu Atlet. Hanya status aktif yang bisa diubah di sini.
        if ($user->hasRole(config('rbac.default_role', 'athlete'))) {
            return redirect()->route('users.index')
                ->withErrors(['user' => 'Akun atlet dikelola lewat menu Atlet. Dari sini hanya status aktif yang bisa diubah.']);
        }

        return view('users.form', ['user' => $user, 'roles' => $this->roleOptions()]);
    }

    public function update(Request $request, User $user)
    {
        $user->update($this->validated($request, $user));

        return redirect()->route('users.index')->with('ok', 'Akun pengguna diperbarui.');
    }

    /** Aktifkan / nonaktifkan akun. Akun nonaktif tidak bisa login. */
    public function toggleActive(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Tidak bisa menonaktifkan akun sendiri.']);
        }
        if ($user->is_active && $user->hasRole('admin')
            && User::where('role', 'admin')->where('is_active', true)->count() <= 1) {
            return back()->withErrors(['user' => 'Tidak bisa menonaktifkan admin aktif terakhir.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('ok', $user->is_active
            ? "Akun {$user->name} diaktifkan."
            : "Akun {$user->name} dinonaktifkan.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus akun sendiri.']);
        }
        if ($user->hasRole('admin') && User::where('role', 'admin')->count() <= 1) {
            return back()->withErrors(['user' => 'Tidak bisa menghapus admin terakhir.']);
        }
        if ($user->athlete) {
            return back()->withErrors(['user' => 'Akun ini tertaut ke data atlet; nonaktifkan saja, atau hapus lewat menu Atlet.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('ok', 'Akun pengguna dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'phone'    => 'nullable|string|max:30',
            'role'     => ['required', Rule::in(array_keys($this->roleOptions()))],
            // Password wajib saat membuat; opsional saat edit (kosong = tidak diubah).
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);   // model meng-hash otomatis (cast 'hashed')
        }

        return $data;
    }
}

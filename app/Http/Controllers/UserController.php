<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Rbac;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Manajemen akun pengguna NON-ATLET (admin, pelatih, dsb). Atlet dibuat lewat
 * registrasi mandiri + data atlet, jadi tidak dikelola di sini. Khusus admin
 * (route di grup role:admin + izin RBAC 'users.*').
 */
class UserController extends Controller
{
    /** Role non-atlet yang boleh dibuat/diberikan di sini (semua role kecuali role default atlet). */
    private function roleOptions(): array
    {
        $athlete = config('rbac.default_role', 'athlete');

        return collect(Rbac::roles())->except($athlete)->all();   // ['admin' => 'Administrator', 'coach' => 'Pelatih', ...]
    }

    public function index()
    {
        $athlete = config('rbac.default_role', 'athlete');
        $users = User::where('role', '!=', $athlete)->orderBy('name')->get();

        return view('users.index', ['users' => $users, 'roles' => Rbac::roles()]);
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
        return view('users.form', ['user' => $user, 'roles' => $this->roleOptions()]);
    }

    public function update(Request $request, User $user)
    {
        $user->update($this->validated($request, $user));

        return redirect()->route('users.index')->with('ok', 'Akun pengguna diperbarui.');
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
            return back()->withErrors(['user' => 'Akun ini tertaut ke data atlet; kelola lewat menu Atlet.']);
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

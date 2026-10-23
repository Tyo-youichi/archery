<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Self-service akun login untuk user yang sedang masuk (terutama atlet):
 * ubah email, telepon, dan kata sandi sendiri. Tidak menyentuh role/status —
 * itu wewenang admin lewat menu Pengguna.
 */
class AccountController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);   // model meng-hash otomatis (cast 'hashed')
        }

        $user->update($data);

        return back()->with('ok', 'Akun login diperbarui.');
    }
}

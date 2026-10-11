<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use App\Models\RoutePermission;
use App\Models\User;
use App\Support\Rbac;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('sort')->orderBy('code')->get();
        $userCounts = User::selectRaw('role, count(*) c')->groupBy('role')->pluck('c', 'role');

        return view('rbac.roles.index', compact('roles', 'userCounts'));
    }

    public function create()
    {
        return view('rbac.roles.form', ['role' => new Role(['is_staff' => false, 'sort' => Role::max('sort') + 1])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Role::create($data);
        Rbac::flush();

        return redirect()->route('rbac.roles.index')->with('ok', 'Role ditambahkan.');
    }

    public function edit(Role $role)
    {
        return view('rbac.roles.form', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        // Kode tidak boleh diubah (dipakai di users.role & referensi RBAC).
        $role->update($this->validated($request, $role, codeEditable: false));
        Rbac::flush();

        return redirect()->route('rbac.roles.index')->with('ok', 'Role diperbarui.');
    }

    public function destroy(Role $role)
    {
        if (User::where('role', $role->code)->exists()) {
            return back()->withErrors(['role' => "Role '{$role->code}' masih dipakai user, tidak bisa dihapus."]);
        }
        $used = RoutePermission::whereJsonContains('roles', $role->code)->exists()
             || Menu::whereJsonContains('roles', $role->code)->exists();
        if ($used) {
            return back()->withErrors(['role' => "Role '{$role->code}' masih dipakai di izin route/menu. Lepaskan dulu."]);
        }

        $role->delete();
        Rbac::flush();

        return redirect()->route('rbac.roles.index')->with('ok', 'Role dihapus.');
    }

    private function validated(Request $request, ?Role $role = null, bool $codeEditable = true): array
    {
        $rules = [
            'name' => 'required|string|max:100',
            'sort' => 'nullable|integer|min:0|max:65535',
        ];
        if ($codeEditable) {
            $rules['code'] = ['required', 'string', 'max:30', 'regex:/^[a-z0-9_]+$/', Rule::unique('roles', 'code')];
        }
        $data = $request->validate($rules, [], ['code' => 'kode role']);
        $data['is_staff'] = $request->boolean('is_staff');
        $data['sort'] = $data['sort'] ?? 0;

        return $data;
    }
}

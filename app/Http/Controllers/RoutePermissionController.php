<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoutePermission;
use App\Support\Rbac;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class RoutePermissionController extends Controller
{
    public function index()
    {
        $permissions = RoutePermission::orderBy('sort')->orderBy('id')->get();
        // Daftar nama route aplikasi (untuk referensi admin saat membuat pola).
        $routeNames = collect(Route::getRoutes())->map->getName()->filter()->unique()->sort()->values();

        return view('rbac.permissions.index', compact('permissions', 'routeNames'));
    }

    public function create()
    {
        return view('rbac.permissions.form', [
            'permission' => new RoutePermission(['roles' => [], 'sort' => RoutePermission::max('sort') + 1]),
            'roleOptions' => $this->roleOptions(),
        ]);
    }

    public function store(Request $request)
    {
        RoutePermission::create($this->validated($request));
        Rbac::flush();

        return redirect()->route('rbac.permissions.index')->with('ok', 'Izin route ditambahkan.');
    }

    public function edit(RoutePermission $permission)
    {
        return view('rbac.permissions.form', ['permission' => $permission, 'roleOptions' => $this->roleOptions()]);
    }

    public function update(Request $request, RoutePermission $permission)
    {
        $permission->update($this->validated($request, $permission));
        Rbac::flush();

        return redirect()->route('rbac.permissions.index')->with('ok', 'Izin route diperbarui.');
    }

    public function destroy(RoutePermission $permission)
    {
        $permission->delete();
        Rbac::flush();

        return redirect()->route('rbac.permissions.index')->with('ok', 'Izin route dihapus.');
    }

    private function roleOptions(): array
    {
        return Role::orderBy('sort')->pluck('name', 'code')->all();
    }

    private function validated(Request $request, ?RoutePermission $permission = null): array
    {
        $codes = array_keys($this->roleOptions());
        $data = $request->validate([
            'pattern' => ['required', 'string', 'max:150', Rule::unique('route_permissions', 'pattern')->ignore($permission)],
            'roles' => 'required|array|min:1',
            'roles.*' => ['string', Rule::in($codes)],
            'sort' => 'nullable|integer|min:0|max:65535',
            'description' => 'nullable|string|max:200',
        ], [], ['pattern' => 'pola route', 'roles' => 'role']);
        $data['roles'] = array_values($data['roles']);
        $data['sort'] = $data['sort'] ?? 0;

        return $data;
    }
}

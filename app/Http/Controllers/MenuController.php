<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use App\Support\Rbac;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('sort')->orderBy('id')->get();

        return view('rbac.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('rbac.menus.form', [
            'menu' => new Menu(['roles' => [], 'sort' => Menu::max('sort') + 1, 'is_active' => true]),
            'roleOptions' => $this->roleOptions(),
        ]);
    }

    public function store(Request $request)
    {
        Menu::create($this->validated($request));
        Rbac::flush();

        return redirect()->route('rbac.menus.index')->with('ok', 'Menu ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        return view('rbac.menus.form', ['menu' => $menu, 'roleOptions' => $this->roleOptions()]);
    }

    public function update(Request $request, Menu $menu)
    {
        $menu->update($this->validated($request));
        Rbac::flush();

        return redirect()->route('rbac.menus.index')->with('ok', 'Menu diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        Rbac::flush();

        return redirect()->route('rbac.menus.index')->with('ok', 'Menu dihapus.');
    }

    private function roleOptions(): array
    {
        return Role::orderBy('sort')->pluck('name', 'code')->all();
    }

    private function validated(Request $request): array
    {
        $codes = array_keys($this->roleOptions());
        $data = $request->validate([
            'label' => 'required|string|max:100',
            'route' => 'required|string|max:150',
            'param' => 'nullable|in:self_athlete',
            'roles' => 'required|array|min:1',
            'roles.*' => ['string', Rule::in($codes)],
            'sort' => 'nullable|integer|min:0|max:65535',
        ], [], ['roles' => 'role']);
        $data['roles'] = array_values($data['roles']);
        $data['param'] = $data['param'] ?? null;
        $data['sort'] = $data['sort'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}

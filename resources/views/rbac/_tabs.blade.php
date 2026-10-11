<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Pengaturan Akses (RBAC)</h1>
</div>
<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('rbac.roles.*')]) href="{{ route('rbac.roles.index') }}">Roles</a></li>
    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('rbac.permissions.*')]) href="{{ route('rbac.permissions.index') }}">Izin Route</a></li>
    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('rbac.menus.*')]) href="{{ route('rbac.menus.index') }}">Menu</a></li>
</ul>

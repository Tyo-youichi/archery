<?php

namespace App\Http\Middleware;

use App\Support\Rbac;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware RBAC berbasis ROUTE. Dipasang sekali pada grup auth; keputusan
 * akses diambil dari config/rbac.php berdasarkan NAMA route yang sedang diakses.
 */
class EnforceRoutePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $name = $request->route()?->getName();

        if ($user && $name && ! Rbac::routeAllowed($name, $user->role)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}

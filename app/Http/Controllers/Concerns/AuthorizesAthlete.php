<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Athlete;

trait AuthorizesAthlete
{
    /** Staf (admin/pelatih) boleh semua; atlet hanya datanya sendiri. */
    protected function authorizeView(Athlete $athlete): void
    {
        $u = auth()->user();
        abort_unless($u->isStaff() || $athlete->user_id === $u->id, 403);
    }
}

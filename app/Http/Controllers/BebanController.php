<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesAthlete;
use App\Models\Athlete;
use App\Models\LoadTest;

/**
 * Printout data beban (1RM) per atlet — tampilan ramah cetak / Simpan PDF.
 * Staf boleh semua atlet; atlet boleh miliknya sendiri (dicek di controller).
 */
class BebanController extends Controller
{
    use AuthorizesAthlete;

    public function print(Athlete $athlete)
    {
        $this->authorizeView($athlete);

        // 1RM terbaru per latihan (urut nama latihan).
        $latest = LoadTest::where('athlete_id', $athlete->id)
            ->orderBy('exercise')->orderByDesc('tested_at')->orderByDesc('id')
            ->get()->unique('exercise')->sortBy('exercise')->values();

        return view('beban.print', compact('athlete', 'latest'));
    }
}

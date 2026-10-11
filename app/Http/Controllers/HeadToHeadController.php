<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\MonthlyScore;
use App\Support\ProfileService;
use Illuminate\Http\Request;

class HeadToHeadController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isStaff(), 403);

        $athletes = Athlete::orderBy('name')->get(['id', 'code', 'name']);
        $a = $request->filled('a') ? Athlete::find($request->a) : null;
        $b = $request->filled('b') ? Athlete::find($request->b) : null;

        $cmp = null;
        if ($a && $b) {
            $cmp = [];
            foreach ([$a, $b] as $k => $ath) {
                $p = ProfileService::forAthlete($ath);
                $s = MonthlyScore::where('athlete_id', $ath->id)->whereNotNull('score');
                $cmp[$k] = [
                    'athlete' => $ath,
                    'profile' => $p,
                    'best' => (clone $s)->max('score'),
                    'avg' => ($avg = (clone $s)->avg('score')) ? round($avg, 1) : null,
                    'count' => (clone $s)->count(),
                ];
            }
        }

        return view('head2head', compact('athletes', 'a', 'b', 'cmp'));
    }
}

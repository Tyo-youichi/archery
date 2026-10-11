<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\ShootLog;
use App\Support\ProfileService;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (! $user->isStaff()) {
            $athlete = $user->athlete;

            return $athlete
                ? redirect()->route('athletes.show', $athlete)
                : view('dashboard', ['staff' => false]);
        }

        $athletes = Athlete::where('is_active', true)->orderBy('name')->get();
        $rows = $athletes->map(function (Athlete $a) {
            $p = ProfileService::forAthlete($a);

            return [
                'athlete' => $a,
                'physical' => $p['physical']['overall'],
                'technique' => $p['technique']['overall'],
            ];
        });

        $avg = fn ($col) => ($v = $rows->pluck($col)->filter(fn ($x) => $x !== null))->count() ? $v->avg() : null;

        $weekStart = Carbon::now()->startOfWeek();
        $weekLogs = ShootLog::whereBetween('log_date', [$weekStart->toDateString(), $weekStart->copy()->addDays(5)->toDateString()])
            ->where('shot_100', true)->selectRaw('athlete_id, count(*) as c')->groupBy('athlete_id')->pluck('c', 'athlete_id');

        return view('dashboard', [
            'staff' => true,
            'rows' => $rows,
            'total' => $athletes->count(),
            'avgPhysical' => $avg('physical'),
            'avgTechnique' => $avg('technique'),
            'weekLogs' => $weekLogs,
            'weekStart' => $weekStart,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesAthlete;
use App\Models\Athlete;
use App\Models\ShootLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/** Buku Saku Atlet - Tabel Monitor Shoot 100 Arrow (mingguan, Senin-Sabtu). */
class ShootLogController extends Controller
{
    use AuthorizesAthlete;

    private function weekStart(?string $date): Carbon
    {
        try {
            return ($date ? Carbon::parse($date) : Carbon::now())->startOfWeek();
        } catch (\Throwable) {
            return Carbon::now()->startOfWeek();
        }
    }

    public function edit(Request $request, Athlete $athlete)
    {
        $this->authorizeView($athlete);
        $start = $this->weekStart($request->query('week'));
        $logs = $athlete->shootLogs()->whereBetween('log_date', [$start->toDateString(), $start->copy()->addDays(5)->toDateString()])
            ->get()->keyBy(fn ($l) => $l->log_date->toDateString());

        $days = [];
        $i = 0;
        foreach (config('archery.shoot_days') as $name) {
            $d = $start->copy()->addDays($i++);
            $days[] = ['name' => $name, 'date' => $d, 'log' => $logs->get($d->toDateString())];
        }

        // Rekap 8 minggu terakhir: jumlah hari shoot 100 arrow
        $recap = $athlete->shootLogs()->where('log_date', '>=', $start->copy()->subWeeks(7)->toDateString())
            ->where('shot_100', true)->get()->groupBy(fn ($l) => $l->log_date->copy()->startOfWeek()->toDateString())
            ->map->count();

        return view('shoot-log.week', [
            'athlete' => $athlete, 'start' => $start, 'days' => $days, 'recap' => $recap,
            'prev' => $start->copy()->subWeek()->toDateString(), 'next' => $start->copy()->addWeek()->toDateString(),
        ]);
    }

    public function update(Request $request, Athlete $athlete)
    {
        $this->authorizeView($athlete);
        $start = $this->weekStart($request->input('week'));
        $codes = array_keys(config('archery.technique_codes'));

        $request->validate([
            'logs.*.techniques' => 'nullable|array',
            'logs.*.techniques.*' => 'in:' . implode(',', $codes),
            'logs.*.shot_100' => 'nullable|in:ya,tdk',
            'logs.*.notes' => 'nullable|string|max:2000',
        ]);

        for ($i = 0; $i < 6; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $in = $request->input("logs.$date", []);
            $techniques = array_values($in['techniques'] ?? []);
            $shot = $in['shot_100'] ?? null;
            $notes = $in['notes'] ?? null;

            if (! $techniques && ! $shot && ! $notes) {
                ShootLog::where('athlete_id', $athlete->id)->where('log_date', $date)->delete();
                continue;
            }
            ShootLog::updateOrCreate(
                ['athlete_id' => $athlete->id, 'log_date' => $date],
                ['techniques' => $techniques, 'shot_100' => $shot === 'ya', 'notes' => $notes]
            );
        }

        return redirect()->route('shoot-log.edit', [$athlete, 'week' => $start->toDateString()])->with('ok', 'Catatan minggu ini tersimpan.');
    }
}

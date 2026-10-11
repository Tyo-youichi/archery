<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesAthlete;
use App\Models\Athlete;
use App\Models\LoadTest;
use App\Support\ProfileService;
use Illuminate\Http\Request;

class AthleteController extends Controller
{
    use AuthorizesAthlete;

    public function index(Request $request)
    {
        $athletes = Athlete::query()
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%' . $request->q . '%')->orWhere('code', 'like', '%' . $request->q . '%')))
            ->when($request->filled('division'), fn ($q) => $q->where('division', $request->division))
            ->orderBy('name')->paginate(25)->withQueryString();

        return view('athletes.index', compact('athletes'));
    }

    public function create()
    {
        return view('athletes.form', ['athlete' => new Athlete(['code' => Athlete::nextCode(), 'is_active' => true])]);
    }

    public function store(Request $request)
    {
        $athlete = Athlete::create($this->validated($request));

        return redirect()->route('athletes.show', $athlete)->with('ok', 'Atlet ditambahkan.');
    }

    public function show(Athlete $athlete)
    {
        $this->authorizeView($athlete);

        $profile = ProfileService::forAthlete($athlete);
        $scores = $athlete->monthlyScores;
        $anthros = $athlete->anthropometries()->reorder('measured_at')->get();

        // 1RM terbaru per latihan
        $loads = LoadTest::where('athlete_id', $athlete->id)->orderByDesc('tested_at')->orderByDesc('id')
            ->get()->unique('exercise')->values();

        $charts = [
            'radar' => [
                'labels' => array_merge(
                    array_column($profile['physical']['groups'], 'label'),
                    array_column($profile['technique']['groups'], 'label')
                ),
                'data' => array_map(
                    fn ($v) => $v === null ? 0 : round($v * 100, 1),
                    array_merge(
                        array_column($profile['physical']['groups'], 'pct'),
                        array_column($profile['technique']['groups'], 'pct')
                    )
                ),
            ],
            'scores' => [
                'labels' => $scores->map(fn ($s) => substr($s->month_name, 0, 3) . ' ' . $s->year)->all(),
                'data' => $scores->pluck('score')->all(),
            ],
            'weight' => [
                'labels' => $anthros->map(fn ($a) => $a->measured_at->format('d M y'))->all(),
                'data' => $anthros->pluck('weight_kg')->map(fn ($v) => $v === null ? null : (float) $v)->all(),
            ],
        ];

        return view('athletes.show', [
            'athlete' => $athlete->load('equipment'),
            'profile' => $profile,
            'scores' => $scores,
            'anthros' => $anthros->reverse()->values(),
            'loads' => $loads,
            'charts' => $charts,
            'physicalHistory' => $athlete->physicalTests()->limit(5)->get(),
            'techniqueHistory' => $athlete->techniqueTests()->limit(5)->get(),
        ]);
    }

    public function edit(Athlete $athlete) { return view('athletes.form', compact('athlete')); }

    public function update(Request $request, Athlete $athlete)
    {
        $athlete->update($this->validated($request, $athlete));

        return redirect()->route('athletes.show', $athlete)->with('ok', 'Biodata diperbarui.');
    }

    public function destroy(Athlete $athlete)
    {
        $athlete->delete();

        return redirect()->route('athletes.index')->with('ok', 'Atlet dihapus.');
    }

    private function validated(Request $request, ?Athlete $athlete = null): array
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:athletes,code' . ($athlete ? ',' . $athlete->id : ''),
            'name' => 'required|string|max:255',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:L,P',
            'origin' => 'nullable|string|max:150',
            'division' => 'nullable|string|max:30',
            'registration_type' => 'required|in:' . implode(',', array_keys(config('archery.registration_types'))),
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesAthlete;
use App\Models\Athlete;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    use AuthorizesAthlete;

    public function edit(Athlete $athlete)
    {
        $this->authorizeView($athlete);   // staf semua; atlet hanya miliknya

        return view('equipment.form', ['athlete' => $athlete, 'equipment' => $athlete->equipment]);
    }

    public function update(Request $request, Athlete $athlete)
    {
        $this->authorizeView($athlete);

        $rules = collect(config('archery.equipment_fields'))->map(fn () => 'nullable|string|max:150')->all();
        $athlete->equipment()->updateOrCreate([], $request->validate($rules));

        return redirect()->route('athletes.show', $athlete)->with('ok', 'Peralatan tersimpan.');
    }
}

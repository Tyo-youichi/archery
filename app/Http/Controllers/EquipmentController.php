<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function edit(Athlete $athlete)
    {
        return view('equipment.form', ['athlete' => $athlete, 'equipment' => $athlete->equipment]);
    }

    public function update(Request $request, Athlete $athlete)
    {
        $rules = collect(config('archery.equipment_fields'))->map(fn () => 'nullable|string|max:150')->all();
        $athlete->equipment()->updateOrCreate([], $request->validate($rules));

        return redirect()->route('athletes.show', $athlete)->with('ok', 'Peralatan tersimpan.');
    }
}

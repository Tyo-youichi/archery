<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anthropometry extends Model
{
    protected $table = 'anthropometries';
    protected $guarded = ['id'];
    protected $casts = ['measured_at' => 'date'];

    public function athlete() { return $this->belongsTo(Athlete::class); }

    /** Indeks Massa Tubuh = BB (kg) / TB (m)^2 */
    public function getBmiAttribute(): ?float
    {
        if (! $this->height_cm || ! $this->weight_kg) {
            return null;
        }
        $m = $this->height_cm / 100;

        return round($this->weight_kg / ($m * $m), 2);
    }

    public function getBmiCategoryAttribute(): ?string
    {
        $bmi = $this->bmi;
        if ($bmi === null) {
            return null;
        }

        return $bmi < 18.5 ? 'KURUS' : ($bmi <= 25 ? 'NORMAL' : 'GEMUK');
    }
}

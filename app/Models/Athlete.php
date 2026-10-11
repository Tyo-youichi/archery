<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $fillable = [
        'user_id', 'code', 'name', 'birth_place', 'birth_date', 'gender', 'origin',
        'division', 'registration_type', 'photo', 'is_active',
    ];

    protected $casts = ['birth_date' => 'date', 'is_active' => 'boolean'];

    /** Kode atlet berikutnya: ATL{n+1}. */
    public static function nextCode(): string
    {
        $max = static::pluck('code')->map(fn ($c) => (int) preg_replace('/\D/', '', $c))->max() ?? 0;

        return 'ATL' . ($max + 1);
    }

    public function user() { return $this->belongsTo(User::class); }
    public function anthropometries() { return $this->hasMany(Anthropometry::class)->orderByDesc('measured_at'); }
    public function physicalTests() { return $this->hasMany(PhysicalTest::class)->orderByDesc('tested_at'); }
    public function techniqueTests() { return $this->hasMany(TechniqueTest::class)->orderByDesc('tested_at'); }
    public function loadTests() { return $this->hasMany(LoadTest::class)->orderByDesc('tested_at'); }
    public function monthlyScores() { return $this->hasMany(MonthlyScore::class)->orderBy('year')->orderBy('month'); }
    public function equipment() { return $this->hasOne(Equipment::class); }
    public function shootLogs() { return $this->hasMany(ShootLog::class)->orderBy('log_date'); }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date?->age;
    }

    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) { 'L' => 'Laki-laki', 'P' => 'Perempuan', default => '-' };
    }
}

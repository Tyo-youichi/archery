<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Payment extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'amount'      => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeForPeriod($q, int $year, int $month)
    {
        return $q->where('year', $year)->where('month', $month);
    }

    /** Label periode, mis. "Oktober 2026". */
    public function getPeriodLabelAttribute(): string
    {
        return Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }
}

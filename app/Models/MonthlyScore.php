<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyScore extends Model
{
    protected $guarded = ['id'];

    public function athlete() { return $this->belongsTo(Athlete::class); }

    public function getMonthNameAttribute(): string
    {
        return ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus',
                'September', 'Oktober', 'November', 'Desember'][$this->month] ?? '-';
    }
}

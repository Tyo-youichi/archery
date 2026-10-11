<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShootLog extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['log_date' => 'date', 'techniques' => 'array', 'shot_100' => 'boolean'];

    public function athlete() { return $this->belongsTo(Athlete::class); }
}

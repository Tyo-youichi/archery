<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechniqueTest extends Model
{
    protected $table = 'technique_tests';
    protected $guarded = ['id'];
    protected $casts = ['tested_at' => 'date', 'results' => 'array'];

    public function athlete() { return $this->belongsTo(Athlete::class); }
}

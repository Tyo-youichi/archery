<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'equipments';
    protected $guarded = ['id'];

    public function athlete() { return $this->belongsTo(Athlete::class); }
}

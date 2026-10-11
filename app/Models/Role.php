<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['is_staff' => 'boolean'];

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'code');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $fillable = ['user_id', 'name', 'desasiswa_id','club_id', 'registration_code'];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

}

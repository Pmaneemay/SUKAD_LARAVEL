<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $fillable = ['user_id', 'name', 'desasiswa_id', 'club_id', 'registration_code'];

    /**
     * Relationship with Desasiswa model
     */
    public function desasiswa()
    {
        return $this->belongsTo(Desasiswa::class, 'desasiswa_id', 'desasiswa_id');
    }
}
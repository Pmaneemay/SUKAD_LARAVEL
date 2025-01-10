<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matchup extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'group_name',
        'match_id',
        'team1_id',
        'team2_id',
    ];

    public function sport()
    {
        return $this->belongsTo(Sport::class, 'sport_id');
    }

    public function team1()
    {
        return $this->belongsTo(Desasiswa::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Desasiswa::class, 'team2_id');
    }
}
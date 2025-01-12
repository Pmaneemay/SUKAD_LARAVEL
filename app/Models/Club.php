<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    protected $fillable = ['club_id', 'club_name', 'desasiswa_id', 'sport_id'];
    protected $primaryKey = 'club_id';
    public $incrementing = false;


}

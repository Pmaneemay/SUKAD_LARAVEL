<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Selection extends Model
{
    protected $fillable = ['selection_id', 'selection_date','time_start','time_end','registration_deadline','is_ended','club_id','note'];
    protected $primaryKey = 'selection_id';
    public  $timestamps = false;

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id', 'club_id');
    }

    public function participants(){
        return $this->hasMany(Participant::class, 'selection_id', 'selection_id');
    }

}

<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = ['club_id', 'club_name', 'desasiswa_id', 'sport_id'];
    protected $primaryKey = 'club_id';
    public $incrementing = false;

    public function players(){
        return $this->hasMany(Student::class, 'club_id', 'club_id');
    }


}

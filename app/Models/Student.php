<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'name', 'desasiswa_id', 'club_id', 'matrics_no'];
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    public function credentials(){
        return $this->hasOne(User::class, 'user_id', 'user_id');
    }
    

}

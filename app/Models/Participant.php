<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = ['selection_id', 'student_id', 'selection_status',' priority'];
    public  $timestamps = false;
}

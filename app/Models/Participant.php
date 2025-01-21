<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = ['selection_id', 'student_id', 'selection_status',' priority'];
    public  $timestamps = false;

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'user_id');
    }

    public function status()
    {
        return $this->belongsTo(Selection_status::class, 'selection_status', 'id');
    }


}

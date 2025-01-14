<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Selection_status extends Model
{
   protected $table = 'selection_status';
   protected $fillable = ['id', 'type', 'description'];

}

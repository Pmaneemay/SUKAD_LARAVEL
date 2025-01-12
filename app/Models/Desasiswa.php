<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desasiswa extends Model
{
    use HasFactory;

    protected $fillable = ['desasiswa_id', 'desasiswa_name', 'logo_path'];

    protected $primaryKey = 'desasiswa_id';

    public $incrementing = false;

    protected $keyType = 'string';
}
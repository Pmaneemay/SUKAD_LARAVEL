<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    protected $fillable = ['email', 'password', 'role'];
    protected $hidden = ['password'];
    protected $primaryKey = 'user_id';  
    public $incrementing = false;  
    protected $keyType = 'string'; 
    public $timestamp = false;

    public function profile()
    {
        switch ($this->role) {
            case 'DSAD':
                return $this->hasOne(Admin::class, 'user_id', 'user_id');
            case 'STUD':
                return $this->hasOne(Student::class, 'user_id', 'user_id');
            case 'TMNG':
                return $this->hasOne(Manager::class, 'user_id', 'user_id');
            case 'EORG':
                return $this->hasOne(Organiser::class, 'user_id', 'user_id');
            default:
                return null; 
        }
    }

}

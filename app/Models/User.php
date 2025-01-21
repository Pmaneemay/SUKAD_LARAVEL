<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['user_id','email', 'password', 'role'];
    protected $hidden = ['password'];
    protected $primaryKey = 'user_id';  
    public $incrementing = false;  
    protected $keyType = 'string'; 
    public $timestamps = false; // Corrected spelling (should be $timestamps)

    /**
     * Dynamically link user to their specific profile (e.g., Admin, Student, Manager, Organiser).
     */
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

    /**
     * Define a relationship to the FacilityBooking model.
     */
    public function bookings()
    {
        return $this->hasMany(FacilityBooking::class, 'user_id', 'user_id');
    }

    /**
     * Define a relationship to the Manager model.
     */
    public function manager()
    {
        return $this->hasOne(Manager::class, 'user_id', 'user_id');
    }
}
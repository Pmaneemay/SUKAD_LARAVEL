<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportsFacility extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'location'];

    /**
     * Get the bookings for the sports facility.
     */
    public function bookings()
    {
        return $this->hasMany(FacilityBooking::class, 'facility_id');
    }
}

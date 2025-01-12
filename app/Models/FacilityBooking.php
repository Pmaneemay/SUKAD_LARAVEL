<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityBooking extends Model
{
    use HasFactory;

    protected $fillable = ['facility_id', 'user_id', 'booking_date', 'status'];

    /**
     * Get the facility associated with the booking.
     */
    public function facility()
    {
        return $this->belongsTo(SportsFacility::class, 'facility_id');
    }

    /**
     * Get the user who made the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the time slot associated with the booking.
     */
    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot');
    }

    /**
     * Get the manager associated with the booking through the user.
     */
    public function manager()
    {
        return $this->hasOneThrough(
            Manager::class,
            User::class,
            'user_id', // Foreign key on the users table
            'user_id', // Foreign key on the managers table
            'user_id', // Local key on the facility_bookings table
            'user_id'  // Local key on the users table
        );
    }
}
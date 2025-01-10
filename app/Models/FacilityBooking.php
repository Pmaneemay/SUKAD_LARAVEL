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
        return $this->belongsTo(User::class, 'user_id');
    }
}

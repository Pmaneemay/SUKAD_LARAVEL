<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'timeslots';

    protected $fillable = [
        'facility_id',
        'start_time',
        'end_time',
    ];

    /**
     * Relationship with the SportsFacility model
     */
    public function facility()
    {
        return $this->belongsTo(SportsFacility::class, 'facility_id');
    }
}
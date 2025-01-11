<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacilityBooking;
use App\Models\SportsFacility;
use App\Models\TimeSlot;

class FacilityBookingController extends Controller
{
    /**
     * Show the list of sports facilities for booking.
     */
    public function index()
    {
        $facilities = SportsFacility::all(); // Fetch all facilities
        return view('facility_bookings.index', compact('facilities'));
    }

    /**
     * Fetch availability of time slots for a specific facility and date.
     */
    public function getAvailability(Request $request, $facilityId)
    {
        $date = $request->input('date');
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        // Fetch all time slots for the given facility
        $timeSlots = TimeSlot::where('facility_id', $facilityId)->get();

        // Fetch booked time slots for the given date
        $bookedSlots = FacilityBooking::where('facility_id', $facilityId)
            ->where('booking_date', $date)
            ->pluck('time_slot');

        // Create response data
        $response = $timeSlots->map(function ($slot) use ($bookedSlots) {
            return [
                'id' => $slot->id,
                'time' => $slot->start_time . ' - ' . $slot->end_time,
                'available' => !$bookedSlots->contains($slot->id), // Mark unavailable if booked
            ];
        });

        return response()->json(['timeSlots' => $response]);
    }

    /**
     * Store a new booking in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:sports_facilities,id',
            'booking_date' => 'required|date',
            'time_slot' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    $timeSlot = \App\Models\TimeSlot::find($value);
                    if (!$timeSlot || $timeSlot->facility_id != $request->facility_id) {
                        $fail('The selected time slot is invalid for this facility.');
                    }
                },
            ],
        ]);

        FacilityBooking::create([
            'facility_id' => $request->facility_id,
            'booking_date' => $request->booking_date,
            'time_slot' => $request->time_slot,
            'user_id' => auth()->id(),
            'status' => 'Pending',
        ]);

        return response()->json(['success' => 'Booking confirmed!'], 201);
    }

    /**
     * Fetch past bookings for the logged-in user.
     */
    public function pastBookings()
{
    $user = auth()->user();
    $bookings = FacilityBooking::with(['facility', 'timeSlot'])
        ->orderBy('booking_date', 'desc')
        ->get();

    \Log::info('Fetched bookings', ['role' => $user->role, 'bookings' => $bookings]); // Debug log

    return view('facility_bookings.past', compact('bookings'));
}

    /**
     * Update the status of a booking.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected',
        ]);
//find booking by ID
        $booking = FacilityBooking::findOrFail($id);

        // Only allow EORG to update statuses
        if (auth()->user()->role !== 'EORG') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        //update status
        $booking->status = $request->status;
        $booking->save();

        return response()->json(['success' => 'Booking status updated successfully!']);
    }
}
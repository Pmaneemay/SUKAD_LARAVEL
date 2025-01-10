<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FacilityBooking;
use App\Models\SportsFacility;

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

        // Fetch booked time slots for the given facility and date
        $bookedSlots = FacilityBooking::where('facility_id', $facilityId)
            ->where('booking_date', $date)
            ->pluck('time_slot');

        // Define standard time slots
        $timeSlots = [
            "10:00 AM",
            "11:00 AM",
            "12:00 PM",
            "1:00 PM",
            "2:00 PM",
            "3:00 PM",
        ];

        // Create response data
        $response = array_map(function ($slot) use ($bookedSlots) {
            return [
                'time' => $slot,
                'available' => !$bookedSlots->contains($slot), // Mark unavailable if booked
            ];
        }, $timeSlots);

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
            'time_slot' => 'required|string', // Ensure time_slot is validated
        ]);

        // Check if the time slot is already booked
        $isBooked = FacilityBooking::where('facility_id', $request->facility_id)
            ->where('booking_date', $request->booking_date)
            ->where('time_slot', $request->time_slot)
            ->exists();

        if ($isBooked) {
            return response()->json(['error' => 'This time slot has already been booked. Please choose another.'], 400);
        }

        FacilityBooking::create([
            'facility_id' => $request->facility_id,
            'booking_date' => $request->booking_date,
            'time_slot' => $request->time_slot, // Save time_slot
            'user_id' => auth()->id(), // Ensure the user ID is correctly retrieved
            'status' => 'Pending',
        ]);

        return response()->json(['success' => 'Booking confirmed!'], 201);
    }

    /**
     * Fetch past bookings for the logged-in user.
     */
    public function pastBookings()
    {
        $bookings = FacilityBooking::with('facility')
            ->where('user_id', auth()->id()) // Assuming the user is authenticated
            ->orderBy('booking_date', 'desc')
            ->get();

        return view('facility_bookings.past', compact('bookings'));
    }
}
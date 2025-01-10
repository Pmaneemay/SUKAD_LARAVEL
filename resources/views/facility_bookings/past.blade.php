@extends('layouts.app')

@section('title', 'Past Bookings')

@section('content')
<div class="container">
    <h1 class="text-center my-4">Past Bookings</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Facility</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $booking)
                <tr>
                    <td>{{ $booking->facility->name }}</td>
                    <td>{{ $booking->booking_date }}</td>
                    <td>{{ $booking->time_slot }}</td> <!-- Display timeslot -->
                    <td>
                        @if ($booking->status == 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @elseif ($booking->status == 'Approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
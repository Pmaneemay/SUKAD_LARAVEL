@extends('layouts.app')

@section('title', 'Past Bookings')

@section('content')
<div class="container">
    <!-- Alert Container -->
    <div id="alert-container"></div>

    <h1 class="text-center my-4">Past Bookings</h1>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Facility</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Status</th>
                <th>Booked By</th>
                <th>Desasiswa</th> <!-- New column -->
                @if (auth()->user()->role === 'EORG')
                    <th>Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $booking)
                <tr>
                    <td>{{ $booking->facility->name ?? 'Unknown Facility' }}</td>
                    <td>{{ $booking->booking_date }}</td>
                    <td>
                        @if ($booking->timeSlot)
                            {{ $booking->timeSlot->start_time }} - {{ $booking->timeSlot->end_time }}
                        @else
                            Not Available
                        @endif
                    </td>
                    <td id="status-{{ $booking->id }}">
                        <span class="badge bg-{{ $booking->status == 'Pending' ? 'warning text-dark' : ($booking->status == 'Approved' ? 'success' : 'danger') }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td>{{ $booking->manager?->name ?? 'Unknown Manager' }}</td>
                    <td>{{ $booking->manager?->desasiswa?->desasiswa_name ?? 'Unknown Desasiswa' }}</td> <!-- Display Desasiswa Name -->
                    @if (auth()->user()->role === 'EORG')
                        <td>
                            <button class="btn btn-success btn-sm update-status" data-id="{{ $booking->id }}" data-status="Approved">Approve</button>
                            <button class="btn btn-danger btn-sm update-status" data-id="{{ $booking->id }}" data-status="Rejected">Reject</button>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.update-status', function (e) {
        e.preventDefault(); // Prevent default behavior

        const bookingId = $(this).data('id'); // Get booking ID
        const status = $(this).data('status'); // Get status to update

        // Perform AJAX request
        $.ajax({
            url: `/bookings/${bookingId}/update-status`, // Backend route
            type: 'POST', // HTTP method
            data: {
                _token: "{{ csrf_token() }}", // CSRF token
                status: status, // Status to update
            },
            success: function (response) {
                // Clear the alert container before adding a new message
                $("#alert-container").empty();

                // Update the status badge dynamically
                $(`#status-${bookingId}`).html(`
                    <span class="badge bg-${status === 'Approved' ? 'success' : 'danger'}">
                        ${status}
                    </span>
                `);

                // Display a success message
                const successMessage = `
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        ${response.success}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $("#alert-container").append(successMessage); // Insert into the alert container
            },
            error: function (xhr) {
                // Clear the alert container before adding a new message
                $("#alert-container").empty();

                // Handle errors
                const errorMessage = xhr.responseJSON?.error || 'An error occurred. Please try again.';
                const errorAlert = `
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        ${errorMessage}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $("#alert-container").append(errorAlert); // Insert error message into the alert container
            },
        });
    });
</script>
@endpush
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
    $(document).on('click', '.update-status', function () {
        const bookingId = $(this).data('id');
        const status = $(this).data('status');

        $.ajax({
            url: `/bookings/${bookingId}/update-status`,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                status: status,
            },
            success: function (response) {
                // Update the status on the page
                $(`#status-${bookingId}`).html(`
                    <span class="badge bg-${status === 'Approved' ? 'success' : 'danger'}">
                        ${status}
                    </span>
                `);
                alert('Booking status updated successfully!');
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.error || 'An error occurred. Please try again.');
            }
        });
    });
</script>
@endpush
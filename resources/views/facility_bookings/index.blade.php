@extends('layouts.app')

@section('title', 'Sports Facility Booking')

@section('content')
<div class="container">
    <!-- Hero Banner -->
    <div class="hero-banner text-center my-4 p-5 rounded shadow">
        <h1 class="display-4">Book a Sports Facility</h1>
        <p class="lead">Choose your favorite sport and book facilities conveniently.</p>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-md-6">
            <input type="text" id="search-bar" class="form-control" placeholder="Search for a facility...">
        </div>
        <div class="col-md-6">
            <select id="sports-filter" class="form-control">
                <option value="">Filter by Sport</option>
                @foreach ($facilities->groupBy('type') as $type => $group)
                    <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Facilities grouped by sport type -->
    <div id="facility-list" class="sports-sections">
        @foreach ($facilities->groupBy('type') as $type => $facilityGroup)
            <h2 class="text-center mt-5">{{ ucfirst($type) }}</h2>
            <div class="row">
                @foreach ($facilityGroup as $facility)
                    <div class="col-md-4 mb-4">
                        <div class="card facility-card shadow-sm" data-type="{{ $facility->type }}" data-name="{{ $facility->name }}">
                            <img src="{{ asset($facility->image_path) }}" class="card-img-top" alt="{{ $facility->name }}">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $facility->name }}</h5>
                                <p class="card-text text-muted">Location: {{ $facility->location }}</p>
                                <button class="btn btn-primary select-btn" data-id="{{ $facility->id }}">Book Now</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="booking-modal" tabindex="-1" role="dialog" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bookingModalLabel">Book Facility</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <img id="modal-facility-image" src="" class="img-fluid rounded" alt="Facility Image">
                    </div>
                    <div class="col-md-6">
                        <h3 id="modal-facility-name"></h3>
                        <p id="modal-sport-type"></p>
                        <label for="booking-date">Select Date:</label>
                        <input type="date" id="booking-date" class="form-control">
                        <div id="time-slots" class="mt-3"></div>
                        <div id="slot-message" class="text-danger mt-2"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="book-now" class="btn btn-success">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hero-banner {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
    }

    .facility-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .facility-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    #time-slots button {
        margin: 5px;
    }

    #time-slots button.active {
        background-color: #28a745;
        color: white;
    }

    .select-btn {
        background-color: #007bff;
        border: none;
    }

    .select-btn:hover {
        background-color: #0056b3;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = $("#booking-modal");
    let selectedFacilityId = null;

    // Handle select button click
    $(document).on("click", ".select-btn", function () {
        selectedFacilityId = $(this).data("id");
        const facilityCard = $(this).closest(".card");
        const facilityName = facilityCard.find(".card-title").text();
        const sportType = facilityCard.find(".card-text").text();
        const imageUrl = facilityCard.find("img").attr("src");

        $("#modal-facility-name").text(facilityName);
        $("#modal-sport-type").text(sportType);
        $("#modal-facility-image").attr("src", imageUrl);

        $("#booking-date").val("");
        $("#time-slots").empty();
        $("#slot-message").text("");

        modal.modal("show");
    });

    // Fetch time slots based on the selected date
    $("#booking-date").on("change", function () {
        const selectedDate = $(this).val();
        if (selectedDate && selectedFacilityId) {
            $.ajax({
                url: `/facility/${selectedFacilityId}/availability`,
                type: "GET",
                data: { date: selectedDate },
                success: function (response) {
                    const timeSlotContainer = $("#time-slots");
                    timeSlotContainer.empty(); // Clear previous time slots

                    response.timeSlots.forEach(slot => {
                        const btn = $(`<button class="btn m-1" data-id="${slot.id}">${slot.time}</button>`);
                        if (!slot.available) {
                            btn.addClass("btn-danger").text(`${slot.time} (Booked)`).attr("disabled", true);
                        } else {
                            btn.addClass("btn-outline-primary").on("click", function () {
                                timeSlotContainer.find("button").removeClass("btn-success");
                                $(this).addClass("btn-success");
                            });
                        }
                        timeSlotContainer.append(btn);
                    });
                },
                error: function (xhr) {
                    console.error("Error fetching time slots:", xhr.responseText || xhr.statusText);
                    alert("Failed to load time slots. Please try again.");
                }
            });
        }
    });

    // Submit booking
    $("#book-now").on("click", function () {
        const selectedDate = $("#booking-date").val();
        const selectedTimeSlotId = $("#time-slots .btn-success").data("id");

        if (!selectedDate || !selectedTimeSlotId) {
            $("#slot-message").text("Please select a date and time slot!");
            return;
        }

        $.ajax({
            url: `/bookings`,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                facility_id: selectedFacilityId,
                booking_date: selectedDate,
                time_slot: selectedTimeSlotId, // Pass the correct time_slot ID
            },
            success: function () {
                alert("Booking confirmed!");
            },
            error: function (xhr) {
                console.error("Error during booking:", xhr.responseText);
                alert("An error occurred. Please try again.");
            },
        });
    });
});
</script>
@endpush
@extends('layouts.app')

@section('title', 'Sports Facility Booking')

@section('content')
<div class="container">
    <h1 class="text-center my-4">Book a Sports Facility</h1>

    <!-- Search Bar -->
    <div class="row mb-3">
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

    <!-- Segregated Facilities by Sport Type -->
    <div class="sports-sections">
        @foreach ($facilities->groupBy('type') as $type => $facilityGroup)
            <h2 class="text-center mt-4">{{ ucfirst($type) }}</h2>
            <div id="carousel-{{ $type }}" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($facilityGroup->chunk(3) as $chunkIndex => $facilityChunk)
                        <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                            <div class="row">
                                @foreach ($facilityChunk as $facility)
                                    <div class="col-md-4">
                                        <div class="card">
                                            <img src="{{ $facility->image_url }}" class="card-img-top" alt="{{ $facility->name }}">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $facility->name }}</h5>
                                                <p class="card-text">Sport: {{ ucfirst($facility->type) }}</p>
                                                <button class="btn btn-primary select-btn" data-id="{{ $facility->id }}">Select</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
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
                        <img id="modal-facility-image" src="" class="img-fluid" alt="Facility Image">
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
                <button type="button" id="book-now" class="btn btn-success">Book Now</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = $("#booking-modal");
        let selectedFacilityId = null;

        // Handle select button click
        $(".select-btn").on("click", function () {
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

        // Fetch timeslots based on date
        $("#booking-date").on("change", function () {
            const selectedDate = $(this).val();
            if (selectedDate && selectedFacilityId) {
                $.ajax({
                    url: `/facility/${selectedFacilityId}/availability`,
                    type: "GET",
                    data: { date: selectedDate },
                    success: function (response) {
                        const timeSlotContainer = $("#time-slots");
                        timeSlotContainer.empty(); // Clear previous timeslots

                        response.timeSlots.forEach(slot => {
                            const btn = $(`<button class="btn m-1">${slot.time}</button>`);
                            if (!slot.available) {
                                btn.addClass("btn-danger").text(`${slot.time} (Booked)`).attr("disabled", true); // Mark as unavailable
                            } else {
                                btn.addClass("btn-outline-primary").on("click", function () {
                                    timeSlotContainer.find("button").removeClass("btn-success");
                                    $(this).addClass("btn-success");
                                });
                            }
                            timeSlotContainer.append(btn);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching timeslots:", xhr.responseText || error);
                        alert("Failed to load time slots. Please try again.");
                    }
                });
            }
        });

        // Submit booking
        $("#book-now").on("click", function () {
            const selectedDate = $("#booking-date").val();
            const selectedTimeSlot = $("#time-slots .btn-success").text();

            if (!selectedDate || !selectedTimeSlot) {
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
                    time_slot: selectedTimeSlot,
                },
                success: function () {
                    const summary = `
                        <h4>Booking Summary</h4>
                        <p><strong>Facility:</strong> ${$("#modal-facility-name").text()}</p>
                        <p><strong>Date:</strong> ${selectedDate}</p>
                        <p><strong>Time Slot:</strong> ${selectedTimeSlot}</p>
                        <p><strong>Status:</strong> <span class="badge bg-warning text-dark">Pending</span></p>
                    `;
                    $(".modal-body").html(summary);
                    $("#book-now").hide();
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        $("#slot-message").text(xhr.responseJSON.error);
                    } else {
                        alert("An error occurred. Please try again.");
                    }
                }
            });
        });
    });
</script>
@endpush
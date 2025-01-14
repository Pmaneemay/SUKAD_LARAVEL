@if($selections->isEmpty())
    <p>No Team selections created for this desasiswa yet!</p>
@else
    @foreach($selections as $selection)
        <!-- Event Details Header -->
        <div class="registration-deadline" style="background-color: #0056b3; color: white; padding: 10px 0; width: 100%; text-align: left; margin: 0;">
            <span style="font-weight: bold; margin-left: 20px;">Event Details</span>
            <span class="deadline-time" style="float: right; margin-right: 20px;">
                Deadline : {{$selection->registration_deadline}}
            </span>
        </div>

        <!-- Event Card -->
        <div class="event-card" style="border: 1px solid #ccc; margin-bottom: 20px; border-radius: 5px; overflow: hidden;">
            <div class="event-details" style="padding: 15px;">
                <h4>{{ $selection->club_name }}</h4>
                <p><strong>Date:</strong> {{ $selection->selection_date }}</p>
                <p><strong>Venue:</strong> {{ $selection->venue }}</p>
                <p><strong>Time:</strong> {{ $selection->time_start }} - {{ $selection->time_end }}</p>
                <p><strong>Available Team Spots:</strong> {{ $selection->available }}</p>
                <p style="font-weight: bold; margin-top: 10px;">Extra Notes:</p>
            </div>

            <!-- Comment Box -->
            <div class="comment-box" style="border: 1px solid grey; padding: 10px; margin: 10px 15px; border-radius: 5px;">
                <p>{{ $selection->note ?? 'No additional comments' }}</p>
            </div>

            <!-- Registration Buttons -->
            @if(!$selection->is_ended && !$selection->is_registered)
                <span 
                    class="register-button" 
                    id="registerBtn-{{ $selection->selection_id }}" 
                    data-id = '{{ $selection->selection_id }}'
                    style="background-color: green; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
                    Register
                </span>
            @elseif($selection->is_registered)
                <span 
                    class="registered-message" 
                    style="background-color: #007bff; color: white; padding: 5px 10px; border-radius: 5px; cursor: not-allowed;">
                    Registered
                </span>
            @else
                <span 
                    class="registration-closed-message" 
                    style="background-color: red; color: white; padding: 5px 10px; border-radius: 5px; cursor: not-allowed;">
                    Registration Closed
                </span>
            @endif
        </div>
    @endforeach
@endif


@if(!$selection->isEmpty())
    @foreach($selection as $item)
        <div class="event-details-card" style="border: 1px solid #ccc; border-radius: 5px; padding: 15px; margin-bottom: 20px; background-color: white;"
        data-available="{{$item->available}}"
            data-id="{{$item->selection_id}}"
            data-date="{{$item->selection_date}}"
            data-venue="{{$item->venue}}"
            data-time_start="{{$item->time_start}}"
            data-time_end="{{$item->time_end}}"
            data-notes="{{$item->note}}"
            data-registration_deadline="{{$item->registration_deadline}}">
            <h4>{{ $item->club_name }}</h4>
            <p><strong>Date:</strong> {{ $item->selection_date }}</p>
            <p><strong>Venue:</strong> {{ $item->venue }}</p>
            <p><strong>Time:</strong> {{ $item->time_start }} - {{ $item->time_end }}</p>
            <p><strong>Available Team Spot:</strong> {{ $item->available }}</p>
            <p><strong>Registration deadline:</strong> {{ $item->registration_deadline }}</p>
            <p style="font-weight: bold; margin-top: 10px;">Extra Notes:</p>
            <div class="comment-box" style="border: 1px solid grey; padding: 10px; margin: 10px 0; border-radius: 5px;">
                <p>{{ $item->note ?? "No additional notes" }}</p>
            </div>
            <button class="editSelectionBtn" style="padding: 5px 10px; background-color: #0056b3; color: white; border-radius: 5px; cursor: pointer;">Edit Selection</button>
        </div>

        <div style="margin-bottom: 10px;">
            <button id="update-btn" class="update-btn" style="padding: 5px 10px; background-color: #0056b3; color: white; border-radius: 5px; cursor: pointer;">Update</button>
            <button id="save-btn" class="save-btn" style="padding: 5px 10px; background-color: #28a745; color: white; border-radius: 5px; cursor: pointer; display: none;">Save</button>
        </div>

        <table class="registration-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if(!$item->participants->isEmpty())
                    @foreach($item->participants as $participant)
                        <tr data-id="{{ $participant->student_id }}">
                            <td>{{ $participant->student->name }}</td>
                            <td>{{ $participant->student->matrics_no }}</td>
                            <td class="status-text">{{ $participant->status->description }}</td>
                            <td>
                                <span class="priority-text">{{ $participant->priority ?? '-' }}</span>
                                <input type="number" class="priority-input" value="{{ $participant->priority ?? 0 }}" style="width: 70px; display: none;" disabled />
                            </td>
                            <td>
                                <select class="update-status-select" disabled>
                                    @foreach($selection_status as $status)
                                        @if($status->id == 0)
                                            <option value="{{ $status->id }}" {{ $participant->selection_status == $status->id ? "selected" : "" }}>
                                                SELECT
                                            </option>
                                        @else
                                            <option value="{{ $status->id }}" {{ $participant->selection_status == $status->id ? "selected" : "" }}>
                                                {{ $status->type }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No student registered yet</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach
    @else
        <p>No selection event created yet!</p>
        <button class="createSelectionBtn"style="padding: 5px 10px; background-color: #0056b3; color: white; border-radius: 5px; cursor: pointer;">Create</button>
@endif

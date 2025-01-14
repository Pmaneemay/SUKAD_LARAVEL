@if($registered->isEmpty())
    <p style="text-align: center;">You have not registered for any selection</p>
@else
    <table class="StudEvent-table" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Team</th>
                <th>Status</th>
                <th>Venue</th>
                <th>Details</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($registered as $item)
                <tr style="border-bottom: 1px solid #ccc;">
                    <td>
                        <div>{{ $item->club_name }}</div>
                        <span>
                            @if($item->is_ended == 0)
                                <div style="
                                    display: inline-block; 
                                    background-color: green; 
                                    color: white; 
                                    padding: 2px 10px; 
                                    font-size: 0.8rem; 
                                    font-weight: bold; 
                                    border-radius: 10px; 
                                    margin-top: 4px;">
                                    Open
                                </div>
                            @else
                                <div style="
                                    display: inline-block; 
                                    background-color: red; 
                                    color: white; 
                                    padding: 2px 10px; 
                                    font-size: 0.8rem; 
                                    font-weight: bold; 
                                    border-radius: 10px; 
                                    margin-top: 4px;">
                                    Closed
                                </div>
                            @endif
                        </span>
                    </td>
                    <td>{{ $item->status_text }}</td>
                    <td>{{ $item->venue }}</td>
                    <td>
                        <div class="details-container" style="color: #555;">
                            <p>{{ $item->venue }}</p>
                            <p>{{ $item->selection_date }}</p>
                            <p>{{ $item->time_start }} - {{ $item->time_end }}</p>
                        </div>
                    </td>
                    <td>
                        <div class="btn-group" style="display: flex; justify-content: flex-start; gap: 10px;">
                            <button class="view-details" 
                                style="background-color: forestgreen; 
                                       padding: 5px 10px;
                                       border-radius: 5px; 
                                       cursor: pointer;"
                                data-club_name="{{$item->club_name}}"
                                data-selection_date="{{$item->selection_date}}"
                                data-registration_deadline="{{$item->registration_deadline}}"
                                data-venue="{{$item->venue}}"
                                data-available="{{$item->available}}"
                                data-start_time="{{$item->time_start}}"
                                data-end_time="{{$item->time_end}}"
                                data-notes="{{$item->note}}"
                                       >
                                View Details
                            </button>
                            @if($item->status_type == "PENDING" && $item->is_ended == false)
                                <button class="delete" 
                                    style="background-color: red; 
                                           padding: 5px 10px;
                                           border-radius: 5px; 
                                           cursor: pointer; 
                                           border-color: darkred;"
                                    data-selection_id="{{ $item->selection_id }}"
                                    data-club_name="{{$item->club_name}}">
                                    Delete
                                </button>
                            @elseif($item->status_type == "PASS")
                                <button class="accept" 
                                    style="background-color: #194d9b; 
                                           padding: 5px 10px;
                                           border-radius: 5px; 
                                           cursor: pointer; 
                                           border-color: #194d9b;"
                                    data-selection_id="{{ $item->selection_id }}">
                                    Accept
                                </button>
                            @elseif($item->status_type == 'ACCEPT')
                                <button class="accepted disabled" 
                                    style="background-color: #007bff; 
                                           padding: 5px 10px;
                                           border-radius: 5px; 
                                           cursor: not-allowed; 
                                           border-color: #007bff;">
                                    Accepted!
                                </button>
                            @elseif($item->status_type == 'REJECT')
                                <button class="declined disabled" 
                                    style="background-color: red; 
                                           padding: 5px 10px;
                                           border-radius: 5px; 
                                           cursor: not-allowed; 
                                           border-color: darkred;">
                                    [Declined!]
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if(session('role') == 'DSAD' && $clubs->isEmpty())
    <p style="text-align: center;">No teams available for this Desasiswa.</p>
@elseif(session('role') == 'STUD' && $clubs->isEmpty())
    <p style="text-align: center;">You are not part of Any Team.</p>
@else
    @foreach ($clubs as $club)
        <div class="team-card">
            <h3>{{ $club->club_name }}</h3>
            <p>Sport: {{ $club->sport_name }}</p>
            <p>Team Manager: {{ $club->manager_name ?? 'N/A' }}</p>
            <p>Current Members: {{ $club->total_players }} / {{ $club->team_size }}</p>
            <button class="toggle-members">Show Members</button>
            <div class="members-list" style="display: none;">
                <table class="members-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($club->players->isEmpty())
                            <tr>
                                <td colspan="3" style="text-align: center;">Team members haven't been selected</td>
                            </tr>
                        @else
                            @foreach($club->players as $player)
                                <tr>
                                    <td>{{ $player->name }}</td>
                                    <td>{{ $player->matrics_no }}</td>
                                    <td>{{ $player->credentials->email ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endif

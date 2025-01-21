<div class="create-btn-container">
    <p>Total: {{ $total_manager }}</p>
    <button class="createBtn">Create</button>
</div>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th>Team</th>
                <th>Sport</th>
                <th>Registration Code</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if ($total_manager > 0)
                @foreach ($managers as $manager)
                    <tr data-id="{{ $manager->user_id }}">
                        <td class="managerName">{{ $manager->name ?? 'N/A' }}</td>
                        <td class="managerEmail">{{ $manager->email ?? 'N/A' }}</td>
                        <td class="managerTeam">{{ $manager->club_name ?? 'N/A' }}</td>
                        <td class="managerSport">{{ $manager->sport_name ?? 'N/A' }}</td>
                        <td class="managerSportID" style="display: none;">{{ $manager->Sport_id ?? 'N/A' }}</td>
                        <td>
                            <button class="manager-code" data-manager-id="{{ $manager->user_id }}">Copy</button>
                        </td>
                        <td>
                            <div class="button-group">
                                <button class="deleteBtn" data-id="{{ $manager->user_id }}">Delete</button>
                                <button class="editBtn" data-id="{{ $manager->user_id }}" data-club_id="{{ $manager->club_id }}">Edit</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" style="text-align: center;">There are currently no team managers registered yet.</td>
                </tr>
            @endif
        </tbody>
    </table>

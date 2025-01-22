<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/C_TaskBar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/M_team_management.css') }}">
    <title>Team Management Page</title>
</head>
<body>

    <x-taskbar />
    <div id="dashboard">
        <div id="team_profile">
            <div id="desasiswa_banner">{{$desasiswa->desasiswa_name}}</div>
            <ul>
                <li>
                    <div id="team_logo"; style="background-color: white;">
                    <img src="{{ asset($desasiswa->logo_path) }}" alt="{{ $desasiswa->desasiswa_name }} Logo" style="width: 100%; height: auto;">
                    </div>
                </li>
                <li>
                    <div id="team_stat">
                        <ul>
                            <li>
                                Name :  {{ Auth()->user()->profile->name }}
                            </li>
                            <li>
                                Email : {{ Auth()->user()->email }}
                            </li>
                        </ul>
                    </div>
                </li>
            </ul> 
        </div>

        <x-M_team_navigation />
        

        <div id="table_container"></div>
    </div>

@if(session('role') == 'DSAD')
    <div id="edit-create-ManagerModal" class="modal">
        <div class="modal-content">
        <h2>Add Manager</h2>
        <form id="managerForm">
            <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
            <label for="Team">Team</label>
            <select id="Team" name="Team" required>
                <option value="">Select Team</option>
            </select>
            </div>
        </form>
        </div>
    <div class="modal-actions">
      <button id="modalSaveBtn" class="modal-btn ">Save</button>
      <button id="modalCancelBtn" class="modal-btn cancel">Cancel</button>
    </div>
  </div>

@elseif(session('role') == 'STUD')
  <div id="eventDetailsModal" class="modal" style="display: none; padding: 20px;">
    <div class="modal-content" style="background-color: #fff; border: 1px solid #ccc; border-radius: 5px; overflow: hidden;">
    </div>
    <div class="modal-actions" style="padding: 10px; text-align: center;">
      <button id="modalCloseBtn" class="modal-btn" style="background-color: #0056b3; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
        Close
      </button>
    </div>
  </div>

@elseif(session('role') == 'TMNG')
<div id="eventModal" class="modal" style="display: none;">
    <div class="modal-content" style="padding: 20px;"> <!-- Modal Title -->
        <h2 id="modalTitle" style="text-align: center; color: #0056b3; font-weight: bold; margin-bottom: 20px;">Create/Edit Selection Event</h2>

        <form id="eventForm" method="POST" action="{{route('Event.submit')}}">
            @csrf
            <!-- Hidden field to determine if editing -->
            <input type="hidden" name="is_edit" id="is_edit" value="0">
            <input type="hidden" name="selection_id" id="selection_id" value="">
            <!-- Form Fields -->
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="date" style="display: block; font-weight: bold;">Date:</label>
                <input type="date" id="date" name="date" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="venue" style="display: block; font-weight: bold;">Venue:</label>
                <input type="text" id="venue" name="venue" required placeholder="Enter venue" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="time_start" style="display: block; font-weight: bold;">Time Start:</label>
                <input type="time" id="time_start" name="time_start" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="time_end" style="display: block; font-weight: bold;">Time End:</label>
                <input type="time" id="time_end" name="time_end" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="registration_deadline" style="display: block; font-weight: bold;">Registration Deadline:</label>
                <input type="date" id="registration_deadline" name="registration_deadline" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="notes" style="display: block; font-weight: bold;">Notes:</label>
                <textarea id="notes" name="notes" rows="4" placeholder="Enter any additional details..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; resize: none;"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="modal-actions" style="text-align: right; margin-top: 20px;">
                <button type="button" class="modal-btn cancel" onclick="closeModal()" style="background-color: #ccc; border: none; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer; margin-right: 10px;">Cancel</button>
                <button type="submit" class="modal-btn" style="background-color: #0056b3; border: none; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; cursor: pointer;">Save</button>
            </div>
        </form>
    </div>
</div>
@endif

</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let is_edit = false;  
let user_id = '';
let handleTableClick;
let handleEditEventTableClick;


document.addEventListener("DOMContentLoaded", function () {
        var role = "{{ session('role') }}";
        setDashboard(role);
       
});


function setDashboard(role) {
    if(role == 'DSAD') {
        setupAdminDashboard();
    } else if(role == 'STUD') {
        setupStudentDashboard();
    } else {
        setupManagerDashboard();
    }
}

function setupAdminDashboard() {
    get_team_Manager();
    document.getElementById('AdminmanagerlistBtn').addEventListener('click', function() {
        get_team_Manager();
        setActiveButton(this);
    });
    document.getElementById('AdminclublistBtn').addEventListener('click', function() {
        get_sport_team();
        setActiveButton(this);
    });
}

function setupStudentDashboard() {
    get_sport_team();
    document.getElementById('StudentclublistBtn').addEventListener('click', function() {
        get_sport_team();
        setActiveButton(this);
    });
    document.getElementById('AllSelectionEventlistBtn').addEventListener('click', function() {
        get_selection_event();
        setActiveButton(this);
    });
    document.getElementById('RegisteredEventsBtn').addEventListener('click', function() {
        get_registered_event();
        setActiveButton(this);
    });
}

function setupManagerDashboard() {
    get_sport_team();
    document.getElementById('ManagerclublistBtn').addEventListener('click', function() {
        get_sport_team();
        setActiveButton(this);
    });

    document.getElementById('TMNGSelectionEventBtn').addEventListener('click', function() {
        get_team_selection();
        setActiveButton(this);
    });
}


document.addEventListener('click', function (event) {
    if (event.target && event.target.id === 'modalCancelBtn') {
        const modal = document.getElementById('edit-create-ManagerModal');
        const form = document.querySelector('#managerForm');
        const dropdown = document.querySelector('#Team');

        modal.style.display = 'none'; // Hide the modal
        form.reset(); // Reset the form fields
        dropdown.disabled = false;

        is_edit = false;
    }
});
document.addEventListener('click', function (event) {
    if (event.target && event.target.id === 'modalSaveBtn') {
        console.log('Save button clicked');

        // Get session variable for desasiswaId
        var desasiswaId = "{{ session('profile')->desasiswa_id }}";
        if (!desasiswaId) {
            console.error("Desasiswa ID is missing in session.");
            alert("An error occurred. Please refresh the page and try again.");
            return;
        }

        const form = document.querySelector('#managerForm');
        const username = form.username.value.trim();
        const email = form.email.value.trim();
        const team = form.Team.value.trim();

        // Input validation
        if (!username || !email || !team) {
            alert("All fields are required. Please fill in the form completely.");
            return;
        }

        console.log('Data to save:', { username, email, team, is_edit });

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('CreateEditManager') }}",
            data: {
                user_id : user_id,
                desasiswa_id: desasiswaId,
                username: username,
                email: email,
                team: team,
                is_edit: is_edit
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                console.log(res);
                alert(res.message);
                const modal = document.getElementById('edit-create-ManagerModal');
                modal.style.display = 'none';
                form.reset();
                location.reload();
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                // Display error to the user
                alert("An error occurred: " + (xhr.responseJSON?.message || "Unknown error."));
            }
        });
    }
});
//submit form
document.addEventListener("DOMContentLoaded", function (){
    // Intercept form submission
    $('#eventForm').submit(function(e) {
        e.preventDefault();  // Prevent normal form submission

        var formData = new FormData(this); // Create FormData object to handle form data, including file uploads

        $.ajax({
            url: $(this).attr('action'),  // Use the form's action URL
            method: 'POST',
            data: formData,
            processData: false,  // Prevent jQuery from processing the data
            contentType: false,  // Let the browser set the correct content-type
            success: function(response) {
                // Handle the response if submission is successful
                if (response.success) {
                    alert('Selection saved successfully!');
                    closeModal(); 
                    get_team_selection();
                } else {
                    alert('There was an error saving the event.');
                }
            },
            error: function(xhr, status, error) {
                // Handle errors (display validation errors or generic error)
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    $.each(errors, function(field, messages) {
                        // Display error messages next to the relevant fields
                        var errorMessage = messages.join('<br>');
                        $('#' + field).after('<div class="error-message" style="color: red;">' + errorMessage + '</div>');
                    });
                } else {
                    alert('An error occurred, please try again.');
                }
            }
        });
    });
});

function closeModal() {
    const modal = document.getElementById('eventModal');
    modal.style.display = 'none';
}

function showLoader() {
    const container = document.getElementById('table_container');
    container.innerHTML = '<div style="text-align: center; padding: 20px;"><img src="/loader/ZZ5H.gif" alt="Loading..." style="width: 50px; height: 50px;"></div>';
}

function setActiveButton(clickedButton) {
    const managerListBtn = document.getElementById('AdminmanagerlistBtn');
    const clubListBtn = document.getElementById('AdminclublistBtn');
    const TMNGclubListBtn = document.getElementById('ManagerclublistBtn');
    const STUDteamBtn = document.getElementById( 'StudentclublistBtn');
    const AllEventlistBtn = document.getElementById( 'AllSelectionEventlistBtn');
    const RegisteredEventsBtn = document.getElementById( 'RegisteredEventsBtn'); 
    const TMNGSelectionEventBtn = document.getElementById( 'TMNGSelectionEventBtn');

    // Remove active class from both buttons
    if(managerListBtn != null){
        managerListBtn.classList.remove('active-btn');
    }

    if( clubListBtn != null){
        clubListBtn.classList.remove('active-btn');
    }

    if( TMNGclubListBtn != null){
        TMNGclubListBtn.classList.remove('active-btn');
    }

    if( STUDteamBtn != null){
        STUDteamBtn.classList.remove('active-btn');
    }

    if( AllEventlistBtn != null){
        AllEventlistBtn.classList.remove('active-btn');
    }

    if( RegisteredEventsBtn != null){
        RegisteredEventsBtn.classList.remove('active-btn');
    }
    
    if( TMNGSelectionEventBtn != null){
        TMNGSelectionEventBtn.classList.remove('active-btn');
    }
    // Add active class to the clicked button
    clickedButton.classList.add('active-btn');
}

function get_team_Manager(){
    var desasiswaId = "{{ session('profile')->desasiswa_id }}";
    showLoader();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : "{{ url('getManagers') }}",
        data : {desasiswa_id : desasiswaId},
        type : 'GET',
        dataType : 'json',
        success : function(res){
            $('#table_container').html(res.html);
            attachCopyEventListeners();
            attachDeleteEventListeners();
            attachCreateEventListeners();
            attachEditEventListeners();

        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
        }
    });
};

function get_sport_team(){
    var desasiswaId = "{{ session('profile')->desasiswa_id }}";
    showLoader();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : "{{ url('getSportTeams') }}",
        data : {desasiswa_id : desasiswaId},
        type : 'GET',
        dataType : 'json',
        success : function(res){
            $('#table_container').html(res.html);
            attachToggleEventListerners();
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
        }
    });
}

function get_team_selection(){
    showLoader();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : "{{ url('getClubSelection') }}",
        data : {},
        type : 'GET',
        dataType : 'json',
        success : function(res){
            $('#table_container').html(res.html);
            attacEditClubSelectionEventListeners();
            
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
        }
    });
}

function attachToggleEventListerners(){
    var toggleButtons = document.querySelectorAll('.toggle-members');
            toggleButtons.forEach(function(toggleButton) {
                var teamDiv = toggleButton.closest('.team-card'); // Find the closest team card div
                var membersListDiv = teamDiv.querySelector('.members-list'); // Find the members list div

                toggleButton.addEventListener('click', function() {
                    if (membersListDiv.style.display == 'none') {
                        membersListDiv.style.display = 'block';
                        toggleButton.textContent = 'Hide Members';
                    } else {
                        membersListDiv.style.display = 'none';
                        toggleButton.textContent = 'Show Members';
                    }
                });
            });
}

function get_selection_event(){
     var desasiswaId = "{{ session('profile')->desasiswa_id }}";
     showLoader();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : "{{ url('getSelectionEvents') }}",
        data : {desasiswa_id : desasiswaId},
        type : 'GET',
        dataType : 'json',
        success : function(res){
            $('#table_container').html(res.html);
            attachRegisterEventListeners();
            
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
        }
    });
}

function get_registered_event(){
    showLoader();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url : "{{ url('getRegistered') }}",
        data : {},
        type : 'GET',
        dataType : 'json',
        success : function(res){
            $('#table_container').html(res.html);
            attachViewSelectionEventListeners();
            attachDeleteSelectionEventListeners();
            attachAcceptEventListeners();
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
        }
    });
}

function attachCopyEventListeners() {
    document.querySelector('table').addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('manager-code')) {
            const manager_id = event.target.getAttribute('data-manager-id');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url : "{{ url('getRegistrationCode') }}",
                data : {manager_id:manager_id},
                type : 'GET',
                dataType : 'json',
                success : function(res){
                    navigator.clipboard.writeText(res.code).then(() => {
                        alert('Code copied to clipboard!');
                    });
                },
                error: function(xhr, status, error) {
                    alert("Error:", error);
                }
            });
           
        }
    });
}

function attachDeleteEventListeners() {
    document.querySelector('table').addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('deleteBtn')) {
            const id = event.target.getAttribute('data-id');
            const name = event.target.closest('tr').querySelector('.managerName')?.textContent.trim();
            if (confirm(`Are you sure you want to delete ${name}?`)) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url : "{{ url('deleteManager') }}",
                    data : {user_id : id},
                    type : 'DELETE',
                    dataType : 'json',
                    success : function(res){
                        alert(res.message);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert("An error occurred: " + (xhr.responseJSON?.message || "Unknown error."));
                    }
                });
            }
        }
    });
}

function attachEditEventListeners() {
    document.querySelector('table').addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('editBtn')) {
            is_edit = true;
            user_id = event.target.getAttribute('data-id');
            const club_id = event.target.getAttribute('club_id');
            const name = event.target.closest('tr').querySelector('.managerName')?.textContent.trim();
            const email = event.target.closest('tr').querySelector('.managerEmail')?.textContent.trim();
            const team = event.target.closest('tr').querySelector('.managerTeam')?.textContent.trim();
            
            const modal = document.getElementById('edit-create-ManagerModal');
            modal.querySelector('#username').value = name;
            modal.querySelector('#email').value = email;

            // Populate the Team dropdown
            const teamDropdown = modal.querySelector('#Team');
            teamDropdown.innerHTML = ''; // Clear existing options
            const option = document.createElement('option');
            option.value = club_id;
            option.textContent = team;
            option.selected = true; // Mark the option as selected
            teamDropdown.appendChild(option);

            // Set the dropdown as read-only (disable editing)
            teamDropdown.disabled = true;

            // Open the modal
            modal.style.display = 'block';

        }
    });
}

function attachCreateEventListeners() {
    var desasiswaId = "{{ session('profile')->desasiswa_id }}";
    document.addEventListener('click', function (event) {
        if (event.target && event.target.classList.contains('createBtn')) {
            const modal = document.getElementById('edit-create-ManagerModal');
            const teamSelect = document.getElementById('Team');

            // Clear all options except for the default 'Select Team' option
            const defaultOption = teamSelect.querySelector('option[value=""]');
            teamSelect.innerHTML = '';  // Clear the dropdown
            teamSelect.appendChild(defaultOption);  // Re-add the default option

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url : "{{ url('getClubs') }}",
                data : {desasiswa_id : desasiswaId},
                type : 'GET',
                dataType : 'json',
                success : function(res){
                    if (res.clubs && res.clubs.length > 0) {
                        console.log(res.clubs);
                            res.clubs.forEach(function (club) {
                                const option = document.createElement('option');
                                option.value = club.club_id;
                                option.textContent = club.club_name;
                                teamSelect.appendChild(option);
                            });
                        }

                    modal.style.display = 'block';
                },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
        }
    });
}
function attachRegisterEventListeners() {
    // Remove any existing event listeners first
    const tableContainer = document.querySelector('#table_container');
    tableContainer.removeEventListener('click', handleRegisterSelection);
    // Attach the new event listener
    tableContainer.addEventListener('click', handleRegisterSelection);
}

function attachAcceptEventListeners() {
    // Remove any existing event listeners first
    const tableContainer = document.querySelector('#table_container');
    tableContainer.removeEventListener('click', handleAcceptSelection);
    // Attach the new event listener
    tableContainer.addEventListener('click', handleAcceptSelection);
}

function handleAcceptSelection(event) {
    if (event.target.classList.contains('accept')) {
        // Get the selection ID from the clicked button's data attribute
        const acceptSelectionId = event.target.dataset.selection_id;
        
        // Get the closest row (tr) to the clicked button
        const row = event.target.closest('tr');
        
        // Retrieve the team name from the closest row's data-selection_team attribute
        const teamName = row.dataset.selection_team;
        const team_id = row.dataset.selection_team_id;

        // Get all rows of the table
        const rows = document.querySelectorAll('.StudEvent-table tbody tr');

        // Array to store the selection IDs where status is "PASS"
        const reject_passed_selections = [];

        rows.forEach(row => {
            // Check if the status is "PASS"
            const status = row.dataset.selection_status;
            if (status === 'PASS') {
                const selectionId = row.dataset.selection_id;
                if (selectionId && selectionId !== acceptSelectionId) {
                    reject_passed_selections.push(selectionId);
                }
            }
        });

        // Confirmation message
        const confirmMessage = `Accept Team ${teamName}? Other Team offers will be automatically declined.`;
        // Confirm the action
        if (confirm(confirmMessage)) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ url('Acceptselection') }}",
                data: {
                    accept_selection_id : acceptSelectionId,
                    accept_team_id : team_id,
                    reject_passed_selections: reject_passed_selections,
                },
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    alert(res.message);
                    get_registered_event();
                    
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                    // Display error to the user
                    alert("An error occurred: " + (xhr.responseJSON?.message || "Unknown error."));
                }
            });
        }
    }
}

function handleRegisterSelection(event) {
    if (event.target.classList.contains('register-button')) {
        const selectionId = event.target.dataset.id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('Registerselection') }}",
            data: {
                selection_id: selectionId,
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                alert(res.message);
                get_selection_event();
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                // Display error to the user
                alert("An error occurred: " + (xhr.responseJSON?.message || "Unknown error."));
            }
        });
    }
}

function attachViewSelectionEventListeners() {
    document.querySelector('#table_container').addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('view-details')) {
            const club_name = event.target.dataset.club_name;
            const selection_date = event.target.dataset.selection_date;
            const venue = event.target.dataset.venue;
            const available = event.target.dataset.available;
            const start_time = event.target.dataset.start_time;
            const end_time = event.target.dataset.end_time;
            const deadline = event.target.dataset.registration_deadline;
            const notes = event.target.dataset.notes;

            const modal = document.getElementById('eventDetailsModal');
            const modalContent = modal.querySelector('.modal-content');

            // Set the content dynamically inside the modal
            modalContent.innerHTML = `
            <!-- Registration Deadline Banner -->
            <div class="registration-deadline" style="background-color: #0056b3; color: white; padding: 10px 0; width: 100%; text-align: left; margin: 0;">
                <span style="font-weight: bold; margin-left: 20px;">Event Details</span>
                <span style="float: right; margin-right: 20px;" id="modalDeadlineText">Registration Deadline: <span id="modalDeadline">${deadline|| "TBD"}</span></span>
            </div>

            <!-- Event Details Section -->
            <div class="event-details" style="padding: 15px;">
                <h4>${club_name}</h4>
                <p><strong>Date:</strong> ${selection_date}</p>
                <p><strong>Venue:</strong> ${venue}</p>
                <p><strong>Time:</strong> ${start_time} - ${end_time}</p>
                <p><strong>Available Team Spot:</strong> ${available}</p>
                <p style="font-weight: bold; margin-top: 10px;">Extra Notes:</p>
                <div class="comment-box" style="border: 1px solid grey; padding: 10px; margin: 10px 0; border-radius: 5px;">
                <p>${notes || "No additional notes"}</p>
                </div>
            </div>
            `;

            // Show modal
            modal.style.display = 'block';

            // Close modal on clicking the Close button
            const cancelBtn = modal.querySelector('#modalCloseBtn');
            cancelBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            });
            
        }
    });
}

function attachDeleteSelectionEventListeners() {
    // Remove any existing event listeners first
    const tableContainer = document.querySelector('#table_container');
    tableContainer.removeEventListener('click', handleDeleteSelection);
    // Attach the new event listener
    tableContainer.addEventListener('click', handleDeleteSelection);
}

function handleDeleteSelection(event) {
    if (event.target && event.target.classList.contains('delete')) {
        const selection_id = event.target.dataset.selection_id;
        const name = event.target.dataset.club_name;
        if (confirm(`Are you sure you want to delete registration for ${name}?`)) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url : "{{ url('deleteRegistration') }}",
                data : {selection_id : selection_id},
                type : 'DELETE',
                dataType : 'json',
                success : function(res){
                    alert(res.message);
                    get_registered_event();
                },
                error: function(xhr, status, error) {
                    alert("An error occurred: " + (xhr.responseJSON?.message || "Unknown error."));
                }
            });
        }
    }
}
 
function attacEditClubSelectionEventListeners() {
    const tableContainer = document.querySelector('#table_container');

    if (!tableContainer) return;

    // Remove the existing listener if it exists
    if (handleTableClick) {
        tableContainer.removeEventListener('click', handleTableClick);
    }

    // Define and attach the event listener
    handleTableClick = (event) => {
        if (event.target && event.target.classList.contains('update-btn')) {
            handleUpdateButtonClick(event.target);
        }

        if (event.target && event.target.classList.contains('editSelectionBtn')) {
            handleEditEventButtonClick(event.target);
        }

        if (event.target && event.target.classList.contains('createSelectionBtn')) {
            handleCreateEventButtonClick(event.target);
        }
    };
    tableContainer.addEventListener('click', handleTableClick);
}

function handleUpdateButtonClick(updateButton) {
    const table = document.querySelector('.registration-table');

    if (table) {
        const statusSelects = table.querySelectorAll('.update-status-select');

        statusSelects.forEach(select => {
            const associatedPriorityInput = select.closest('tr').querySelector('.priority-input');
            const associatedPriorityText = select.closest('tr').querySelector('.priority-text');
             // If the selected value is 4 or 5 (student accepts/rejects offer), disable the select dropdown
             if (select.value == 4 || select.value == 5) {
                select.disabled = true;
            } else {
                select.disabled = false; // Enable dropdown if the selected value is not 4 or 5
            }

            // Disable options3, 4 and 5 in the dropdown
                const option3 = select.querySelector('option[value="3"]');
                const option4 = select.querySelector('option[value="4"]');
                const option5 = select.querySelector('option[value="5"]');
                if (option3) option3.disabled = true;
                if (option4) option4.disabled = true;
                if (option5) option5.disabled = true;

                // Handle initial setup based on current status value
                updateOptionsAvailability();

            if (select.value == '3') {
                associatedPriorityText.style.display = 'none';  // Hide priority text
                associatedPriorityInput.style.display = 'inline-block'; // Show priority input
                associatedPriorityInput.disabled = false;
                
            } else {
                associatedPriorityText.style.display = 'inline-block'; // Show priority text if status is not '3'
                associatedPriorityInput.style.display = 'none'; // Hide priority input if status is not '3'
            }

                // Add event listener to update input dynamically
                select.addEventListener('change', () => {
                handlePriorityVisibility(select);
                updateOptionsAvailability();
            });
        });

        // Ensure that priority input values never go below 1
        table.addEventListener('input', (event) => {
            if (event.target && event.target.classList.contains('priority-input')) {
                ensurePriorityMinimum(event.target);
            }
        });

        function ensurePriorityMinimum(inputElement) 
        {
            if (inputElement && inputElement.value < 1) {
                inputElement.value = 1;  // Set value to 1 if it's less than 1
            }
        }

        function updateOptionsAvailability() 
        {
            const available = parseInt(document.querySelector('.event-details-card').getAttribute('data-available'), 10) || 0;
            let status1Count = 0;

            // Track all the status selects and their associated priority inputs
            statusSelects.forEach(select => {
                const associatedPriorityInput = select.closest('tr').querySelector('.priority-input');
                
                if (select.value === '1') {
                    status1Count++; // Count "Accept" statuses
                }
            });

            // Check if the number of "Accept" statuses is below the available limit
            if (status1Count >= available) {
                // If Accept count is at or above available, disable "Accept" and enable "Wait"
                statusSelects.forEach(select => {
                    const option1 = select.querySelector('option[value="1"]');
                    const option3 = select.querySelector('option[value="3"]');
                    
                    if (option1) option1.disabled = true; // Disable "Accept"
                    if (option3) option3.disabled = false; // Enable "Wait"
                });
            } else {
                // If Accept count is below available, disable "Wait" and enable "Accept"
                statusSelects.forEach(select => {
                    const option1 = select.querySelector('option[value="1"]');
                    const option3 = select.querySelector('option[value="3"]');
                    
                    if (option1) option1.disabled = false; // Enable "Accept"
                    if (option3) option3.disabled = true; // Disable "Wait"
                });
            }

            // When the "Accept" status count is less than available, reset "Wait" (option 3) to "Pending" (option 0)
            if (status1Count < available) {
                statusSelects.forEach(select => {
                    if (select.value === '3') { // Check if the current value is '3'
                        select.value = '0'; // Adjust the value to '0' ("Pending")
                        handlePriorityVisibility(select); 
                    }
                });
            }


            // Ensure "Accept" is disabled when the limit is reached, and vice versa for "Wait"
            statusSelects.forEach(select => {
                const option1 = select.querySelector('option[value="1"]');
                const option3 = select.querySelector('option[value="3"]');
                
                // Disable "Accept" and enable "Wait" if the "Accept" count is at or above available
                if (status1Count >= available) {
                    if (option1) option1.disabled = true; // Disable "Accept"
                    if (option3) option3.disabled = false; // Enable "Wait"
                } else {
                    // Enable "Accept" and disable "Wait" if the "Accept" count is below available
                    if (option1) option1.disabled = false; // Enable "Accept"
                    if (option3) option3.disabled = true; // Disable "Wait"
                }
            });
        }

        // Show the "Save" button and hide the "Update" button
        const saveButton = document.querySelector('.save-btn');
        const updateButton = document.querySelector('.update-btn'); // Ensure this is defined elsewhere

        if (saveButton) saveButton.style.display = 'inline-block';
        if (updateButton) updateButton.style.display = 'none'; // Hide the "Update" button

        if (saveButton) {
            saveButton.addEventListener('click', () => {
                const table = document.querySelector('.registration-table');
                if (table) {
                    const priorityInputs = table.querySelectorAll('.priority-input');
                    const errors = [];
                    const priorities = []; // Array to store input elements and their priorities
                    const seenPriorities = []; // Array to track numeric priority values

                    // Collect current priorities, skipping hidden or disabled ones
                    priorityInputs.forEach(input => {
                        const associatedSelect = input.closest('tr').querySelector('.update-status-select');

                        // Check if the priority input is visible and not disabled
                        if (input.style.display !== 'none' && !input.disabled && associatedSelect.value !== '4' && associatedSelect.value !== '5') {
                            const priorityValue = parseInt(input.value, 10);

                            if (isNaN(priorityValue)) {
                                errors.push('Priority must be a number.');
                            } else if (seenPriorities.includes(priorityValue)) {
                                // Check for duplicate priority values
                                errors.push('Cannot duplicate priority.');
                            } else {
                                seenPriorities.push(priorityValue); // Add priority to tracking array
                                priorities.push({ input, priority: priorityValue }); // Add input and value to the priorities array
                            }
                        }
                    });

                    // Show errors if any
                    priorityInputs.forEach(input => {
                        const errorDiv = input.closest('td').querySelector('.priority-error');
                        if (errorDiv) {
                            errorDiv.remove();
                        }

                        if (errors.length > 0 && input.style.display !== 'none' && !input.disabled) {
                            const errorMessage = document.createElement('div');
                            errorMessage.classList.add('priority-error');
                            errorMessage.textContent = errors.join(', ');
                            input.closest('td').appendChild(errorMessage);
                        }
                    });

                    // If there are no errors, update the priorities
                    if (errors.length === 0) {
                        // Sort priorities in ascending order
                        priorities.sort((a, b) => a.priority - b.priority);

                        // Assign sequential priorities starting from 1
                        let priorityCounter = 1;
                        priorities.forEach(({ input }) => {
                            input.value = priorityCounter++; // Update input value
                        });

                        // Hide any remaining error messages
                        priorityInputs.forEach(input => {
                            const errorDiv = input.closest('td').querySelector('.priority-error');
                            if (errorDiv) {
                                errorDiv.remove();
                            }
                        });

                        // Call the update function to save changes
                        update_participants_status();

                        // Optionally, hide the "Save" button and show the "Update" button if necessary
                        saveButton.style.display = 'none';
                        if (updateButton) updateButton.style.display = 'inline-block';
                    }
                }
            });
        }

    }
}

function handlePriorityVisibility(select) {
            const associatedPriorityInput = select.closest('tr').querySelector('.priority-input');
            const associatedPriorityText = select.closest('tr').querySelector('.priority-text');

            if (select.value === '3') {
                associatedPriorityText.style.display = 'none';
                associatedPriorityInput.style.display = 'inline-block';
                associatedPriorityInput.disabled = false;
            } else {
                associatedPriorityText.style.display = 'inline-block';
                associatedPriorityText.textContent = '-';
                associatedPriorityInput.style.display = 'none';
            }
        }

function update_participants_status() {
    const table = document.querySelector('.registration-table');
    const studentData = [];
    if (table) {
        const rows = table.querySelectorAll('tr'); 
        rows.forEach(row => {
            const studentId = row.getAttribute('data-id');
            const priorityInput = row.querySelector('.priority-input');
            const statusSelect = row.querySelector('.update-status-select');

            // Set priority to null if input is not available or visible
            let priority = null;
            if (priorityInput && priorityInput.style.display !== 'none' && !priorityInput.disabled) {
                priority = priorityInput.value;
            }

            // Ensure status option value is used
            let status = null;
            if (statusSelect) {
                const selectedOption = statusSelect.options[statusSelect.selectedIndex];
                if (selectedOption) {
                    status = selectedOption.value; // Use the option value
                }
            }

            // Collect the data for each student
            if (studentId) {
                studentData.push({
                    student_id: studentId,
                    status: status,
                    priority: priority
                });
            }
        });

        // Perform the AJAX request to update the data
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('updateParticipantStatus') }}",
            data: {
                selection_id: document.querySelector('.event-details-card').getAttribute('data-id'),
                participants: studentData,
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                alert(res.message);
                // After the data is updated, re-render the table with the updated values
                get_team_selection(); // Function to refresh or reload the table data
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                alert("An error occurred: " + (xhr.responseJSON?.message || "Unknown error."));
                get_team_selection();
            }
        });
    }
}

function handleCreateEventButtonClick(CreateSelectionBtn){
    document.getElementById("is_edit").value = 0;
    document.getElementById("modalTitle").textContent = "Create Selection Event";
    document.getElementById("eventModal").style.display = "block";
};

function handleEditEventButtonClick(editSelectionBtn) {
    // Get the parent event details card where the button is clicked
    var eventCard = editSelectionBtn.closest(".event-details-card");

    // Extract data attributes from the event card
    var selectionId = eventCard.getAttribute("data-id");
    var date = eventCard.getAttribute("data-date");
    var venue = eventCard.getAttribute("data-venue");
    var timeStart = eventCard.getAttribute("data-time_start");
    var timeEnd = eventCard.getAttribute("data-time_end");
    var registrationDeadline = eventCard.getAttribute("data-registration_deadline");
    var notes = eventCard.getAttribute("data-notes");

    console.log(timeStart);

    // Populate the modal fields with the extracted data
    document.getElementById("selection_id").value = selectionId;
    document.getElementById("date").value = date;
    document.getElementById("venue").value = venue;
    document.getElementById("time_start").value = timeStart;
    document.getElementById("time_end").value = timeEnd;
    document.getElementById("registration_deadline").value = registrationDeadline;
    document.getElementById("notes").value = notes;

    // Set the hidden field to indicate edit mode
    document.getElementById("is_edit").value = 1;

    // Update the modal title to "Edit Selection Event"
    document.getElementById("modalTitle").textContent = "Edit Selection Event";

    // Show the modal
    document.getElementById("eventModal").style.display = "block";
}



</script>
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
            <div id="desasiswa_banner"></div>
            <ul>
                <li>
                    <div id="team_logo"; style="background-color: white;"></div>
                </li>
                <li>
                    <div id="team_stat">
                        
                    </div>
                </li>
            </ul> 
        </div>

        <x-M_team_navigation />

        <div id="table_container"></div>
    </div>

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

  <div id="eventDetailsModal" class="modal" style="display: none; padding: 20px;">
    <div class="modal-content" style="background-color: #fff; border: 1px solid #ccc; border-radius: 5px; overflow: hidden;">
    </div>
    <div class="modal-actions" style="padding: 10px; text-align: center;">
      <button id="modalCloseBtn" class="modal-btn" style="background-color: #0056b3; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer;">
        Close
      </button>
    </div>
  </div>

</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let is_edit = false;  
let user_id = '';


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
    document.getElementById('ManagerclublistBtn').addEventListener('click', function() {
        get_sport_team();
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
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
        }
    });
}

function attachCopyEventListeners() {
    document.querySelector('table').addEventListener('click', function(event) {
        if (event.target && event.target.classList.contains('manager-code')) {
            const code = event.target.getAttribute('data-code');
            if (code) {
                // Copy to clipboard
                navigator.clipboard.writeText(code).then(() => {
                    alert('Code copied to clipboard!');
                }).catch(err => {
                    console.error('Error copying to clipboard:', err);
                    alert('Failed to copy code.');
                });
            }
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
            const notes = event.target.dataset.note;

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





</script>
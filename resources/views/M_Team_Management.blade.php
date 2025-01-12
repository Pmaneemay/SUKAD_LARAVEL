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


</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let is_edit = false;  
    document.addEventListener("DOMContentLoaded", function () {
    
    get_team_Manager();
});

function get_team_Manager(){
    var desasiswaId = "{{ session('profile')->desasiswa_id }}";
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

        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
        }
    });
};

// Use event delegation to handle dynamically added buttons
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
                alert("deleted!");
            }
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
                desasiswa_id: desasiswaId,
                username: username,
                email: email,
                team: team,
                is_edit: is_edit
            },
            type: 'POST',
            dataType: 'json',
            success: function (res) {
                alert("Manager created successfully!");
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




</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <title>SUKAD Matchup Schedule</title>
    <link rel="stylesheet" href="{{ asset('css/C_MatchupSchedule.css') }}">
    <link rel="stylesheet" href="{{ asset('css/C_TaskBar.css') }}">
</head>

<body>
    <x-taskbar />

    <main>
        <div class="title-schedule">
            <h1>SUKAD MATCHUP SCHEDULE</h1>
        </div>
        <div class="button-sukad">
            @if(session('role') == 'EORG')
                @if ($status->start)
                    <button id="start-button" class="start" onclick="startSukad()" disabled>START SUKAD</button>
                    <button id="end-button" class="end" onclick="endSukad()">END SUKAD</button>
                @else
                    <button id="start-button" class="start" onclick="startSukad()">START SUKAD</button>
                    <button id="end-button" class="end" onclick="endSukad()" disabled>END SUKAD</button>
                @endif
            @endif
        </div>
    </main>

    <section>
        <div class="button-container">
            <button class="sport-button" onclick="loadMatchups('FOOTBALL')">
                FOOTBALL
            </button>
            <button class="sport-button" onclick="loadMatchups('NETBALL')">
                NETBALL
            </button>
            <button class="sport-button" onclick="loadMatchups('BASKETBALL')">
                BASKETBALL
            </button>
            <button class="sport-button" onclick="loadMatchups('TENNIS')">
                TENNIS
            </button>
        </div>

        <div id="matchups">
            </div>

        <div id="loading" class="loading">Loading matchups...</div>

        <div id="notification-modal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="closeNotification()">&times;</span>
                <p id="notification-message"></p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 SUKAD Event Management</p>
    </footer>

    <script>
        // Function to get the SUKAD status and update the buttons
        function updateSukadButtons() {
            fetch("{{ route('getSukadStatus') }}")
                .then(response => response.json())
                .then(data => {
                    const startButton = document.getElementById('start-button');
                    const endButton = document.getElementById('end-button');

                    if (data.start) {
                        startButton.disabled = true;
                        endButton.disabled = false;
                    } else {
                        startButton.disabled = false;
                        endButton.disabled = true;
                        matchupsDiv.innerHTML = "<p class='no-matchups-message'>No matchups available. SUKAD has not yet started.</p>";
                    }
                })
                .catch(error => {
                    console.error('Error getting SUKAD status:', error);
                });
        }

        // Call updateSukadButtons on page load
        updateSukadButtons();

        function startSukad() {
            // Disable the "START SUKAD" button and enable the "END SUKAD" button
            document.getElementById('start-button').disabled = true;
            document.getElementById('end-button').disabled = false;

            const matchupsDiv = document.getElementById('matchups');
            matchupsDiv.innerHTML = ''; // Clear previous content

            displayNotification("Starting the SUKAD event...", 5000)
                .then(() => {
                    return fetch("{{ route('startSukad') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                })
                .then(response => response.json())
                .then(data => {
                    closeNotification();
                    matchupsDiv.innerHTML = '';

                    // Show a success notification
                    showNotification("SUKAD has started! Schedules generated for all sports.");
                })
                .catch(error => {
                    closeNotification();
                    console.error('Error starting SUKAD:', error);

                    matchupsDiv.innerHTML = '';
                    showNotification("SUKAD has started! Schedules generated for all sports.");
                });
            
            document.getElementById('start-button').disabled = true;
            document.getElementById('end-button').disabled = false;
        }

        function endSukad() {
            if (!confirm("Are you sure you want to end SUKAD? This will delete all matchup schedules.")) {
                return; // Cancel if the user clicks "Cancel"
            }

            const matchupsDiv = document.getElementById('matchups');
            matchupsDiv.innerHTML = ''; // Clear previous content

            // Disable the "END SUKAD" button and enable the "START SUKAD" button
            document.getElementById('end-button').disabled = true;
            document.getElementById('start-button').disabled = false;

            displayNotification("Ending the SUKAD event...", 3000) 
                .then(() => {
                    return fetch("{{ route('endSukad') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                })
                .then(response => response.json())
                .then(data => {
                    // Show a success notification
                    showNotification(data.message);

                    // Clear the displayed matchups
                    matchupsDiv.innerHTML = "<p class='no-matchups-message'>No matchups available. SUKAD has not yet started.</p>"; 
                })
                .catch(error => {
                    console.error('Error ending SUKAD:', error);

                    // Show an error notification
                    showNotification('An error occurred while ending SUKAD.');

                    // Re-enable the "END SUKAD" button
                    document.getElementById('end-button').disabled = false; 
                });
        }

        function loadMatchups(sport) {
            selectedSport = sport; 
            const matchupsDiv = document.getElementById('matchups');
            matchupsDiv.innerHTML = '';  // Clear previous content

            // Remove "active" class from all sport buttons
            const buttons = document.querySelectorAll('.sport-button');
            buttons.forEach(button => button.classList.remove('active'));

            // Add "active" class to the clicked button
            const activeButton = document.querySelector(`.sport-button[onclick="loadMatchups('${sport}')"]`);
            activeButton.classList.add('active');

            showLoading()

            // Make an AJAX request to the getMatchupsData route
            fetch(`{{ route('getMatchupsData') }}?sport=${sport}`)
                .then(response => response.json())
                .then(data => {
                    // Hide the loading state
                    document.getElementById('loading').style.display = 'none';

                    // Display the matchups in the "matchups" div
                    displayMatchups(data);
                })
                
        }

        function displayMatchups(matchups) {
            const matchupsDiv = document.getElementById('matchups');
            matchupsDiv.innerHTML = ''; // Clear previous matchups

            if (matchups.length === 0) {
                matchupsDiv.innerHTML = "<p class='no-matchups-message'>No matchups available. SUKAD has not yet started.</p>";
                return;
            }

            // Group matchups by group_name
            const groupedMatchups = matchups.reduce((acc, matchup) => {
                acc[matchup.group_name] = (acc[matchup.group_name] || []).concat(matchup);
                return acc;
            }, {});

            // Iterate over each group and display matchups
            for (const groupName in groupedMatchups) {
                const groupTitle = document.createElement('h2');
                groupTitle.textContent = `Group ${groupName}`;
                matchupsDiv.appendChild(groupTitle);

                groupedMatchups[groupName].forEach((matchup, index) => {
                    const matchDiv = document.createElement('div');
                    matchDiv.classList.add('matchup');

                    const matchNumber = document.createElement('div');
                    matchNumber.classList.add('match-number');
                    matchNumber.textContent = `MATCH ${index + 1}`;

                    const matchupContainer = document.createElement('div');
                    matchupContainer.classList.add('matchup-container');

                    const team1Div = document.createElement('div');
                    team1Div.classList.add('team');

                    const vsDiv = document.createElement('span');
                    vsDiv.classList.add('vs');
                    vsDiv.textContent = 'VS';

                    const team2Div = document.createElement('div');
                    team2Div.classList.add('team');

                    // Fetch team logos for Team 1
                    fetch(`/getDesasiswaLogo?desasiswa_id=${matchup.team1_id}`)
                        .then(response => response.json())
                        .then(data => {
                            team1Div.innerHTML = `
                                <img src="${data.logo_path}" class="team-logo" alt="${matchup.team1_name} logo">
                                <span>${matchup.team1_name}</span>
                            `;
                        })
                        .catch(error => {
                            console.error('Error fetching team 1 logo:', error);
                            team1Div.innerHTML = `<span>${matchup.team1_name}</span>`;
                        });

                    // Add the VS element to the container
                    matchupContainer.appendChild(team1Div);
                    matchupContainer.appendChild(vsDiv);

                    // Fetch team logos for Team 2
                    fetch(`/getDesasiswaLogo?desasiswa_id=${matchup.team2_id}`)
                        .then(response => response.json())
                        .then(data => {
                            team2Div.innerHTML = `
                                <img src="${data.logo_path}" class="team-logo" alt="${matchup.team2_name} logo">
                                <span>${matchup.team2_name}</span>
                            `;
                        })
                        .catch(error => {
                            console.error('Error fetching team 2 logo:', error);
                            team2Div.innerHTML = `<span>${matchup.team2_name}</span>`;
                        });

                    // Add team2Div after the fetch
                    matchupContainer.appendChild(team2Div);

                    matchDiv.appendChild(matchNumber);
                    matchDiv.appendChild(matchupContainer);
                    matchupsDiv.appendChild(matchDiv);
                });
            }
        }

        function showNotification(message) {
            const modal = document.getElementById('notification-modal');
            const messageElement = document.getElementById('notification-message');

            // Set the message in the pop-up
            messageElement.textContent = message;

            // Show the modal
            modal.style.display = 'flex';

            // Automatically close the pop-up after 2 seconds
            setTimeout(() => {
                closeNotification();
            }, 2000);
        }

        function displayNotification(message, delay = 5000) {
            return new Promise((resolve) => {
                const modal = document.getElementById('notification-modal');
                const messageElement = document.getElementById('notification-message');

                messageElement.textContent = message;
                modal.style.display = 'flex';

                setTimeout(() => {
                    closeNotification();
                    resolve(); 
                }, delay);
            });
        }

        function closeNotification() {
            document.getElementById('notification-modal').style.display = 'none';
        }

        // Show loading state
        function showLoading() {
            document.getElementById('loading').style.display = 'block';
        }

        // Hide loading state
        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }
    </script>
</body>
</html>
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
                <button id="start-button" class="start">START SUKAD</button>
                <button id="end-button" class="end" disabled>END SUKAD</button>
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
    
        <div id="matchups"></div>
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

    <script src="{{ asset('js/C_taskbar.js') }}"></script> 
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initializeTaskBar();

            const startButton = document.getElementById('start-button');
            const endButton = document.getElementById('end-button');

            if (startButton) {
                startButton.addEventListener('click', startSukad);
            }

            if (endButton) {
                endButton.addEventListener('click', endSukad);
            }
        });

        function startSukad() {
            fetch('/start-sukad', { 
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                displayNotification(data.message);
            })
            .catch(error => console.error('Error starting SUKAD:', error));
        }

        function endSukad() {
            fetch('/end-sukad', { 
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                const matchupsDiv = document.getElementById('matchups');
                matchupsDiv.innerHTML = '';
                displayNotification(data.message);
            })
            .catch(error => console.error('Error ending SUKAD:', error));
        }

        function loadMatchups(sport) {
            selectedSport = sport;
            const matchupsDiv = document.getElementById('matchups');
            matchupsDiv.innerHTML = '';

            const buttons = document.querySelectorAll('.sport-button');
            buttons.forEach(button => button.classList.remove('active'));
            const activeButton = document.querySelector(`.sport-button[onclick="loadMatchups('${sport}')"]`);
            activeButton.classList.add('active');

            showLoading();

            fetch(`/get-matchups/${sport}`) 
                .then(response => response.json())
                .then(matchups => {
                    setTimeout(() => {
                        const round1Title = document.createElement('h2');
                        round1Title.textContent = 'ROUND 1';
                        matchupsDiv.appendChild(round1Title);

                        matchups.forEach((match, index) => {
                            const matchDiv = document.createElement('div');
                            matchDiv.classList.add('matchup');

                            const matchNumber = document.createElement('div');
                            matchNumber.classList.add('match-number');
                            matchNumber.textContent = `MATCH ${index + 1}`;

                            const matchupContainer = document.createElement('div');
                            matchupContainer.classList.add('matchup-container');

                            const team1Div = document.createElement('div');
                            team1Div.classList.add('team');
                            team1Div.innerHTML = `
                                <img src="Image/${match.team1.desasiswa_id.toLowerCase()}.png" class="team-logo" alt="${match.team1.desasiswa_name} logo">
                                <span>${match.team1.desasiswa_name}</span>
                            `;

                            const vsDiv = document.createElement('span');
                            vsDiv.classList.add('vs');
                            vsDiv.textContent = 'VS';

                            const team2Div = document.createElement('div');
                            team2Div.classList.add('team');
                            team2Div.innerHTML = `
                                <img src="Image/${match.team2.desasiswa_id.toLowerCase()}.png" class="team-logo" alt="${match.team2.desasiswa_name} logo">
                                <span>${match.team2.desasiswa_name}</span>
                            `;

                            matchupContainer.appendChild(team1Div);
                            matchupContainer.appendChild(vsDiv);
                            matchupContainer.appendChild(team2Div);

                            matchDiv.appendChild(matchNumber);
                            matchDiv.appendChild(matchupContainer);
                            matchupsDiv.appendChild(matchDiv);
                        });

                        hideLoading();
                    }, 1000);
                })
                .catch(error => console.error('Error loading matchups:', error));
        }

        function showLoading() {
            document.getElementById('loading').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }

        function displayNotification(message, callback) {
            const modal = document.getElementById('notification-modal');
            const messageElement = document.getElementById('notification-message');
            messageElement.textContent = message;
            modal.style.display = 'flex';
            setTimeout(() => {
                closeNotification();
                if (callback) {
                    callback();
                }
            }, 2000);
        }

        function closeNotification() {
            const modal = document.getElementById('notification-modal');
            modal.style.display = 'none';
        }
    </script>
</body>
</html>
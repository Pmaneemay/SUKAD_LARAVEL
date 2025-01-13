<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"> 

    <link rel="stylesheet" href="{{ asset('css/A_ScoreInput.css') }}">
    <link rel="stylesheet" href="{{ asset('css/C_TaskBar.css') }}">

</head>
<body>
    <!-- Taskbar Component -->
    <x-taskbar />
    <!-- Sport Selection and Score Input Containers -->
    <div id="sport-selection-container" class="sport-selection-container"></div>
    <div id="score-input-container" class="score-input-container"></div>  

    <!-- Inline JS -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // Data from the server-side grouped by sport
        const groupedMatchups = @json($groupedMatchups);

        // Convert grouped matchups into a simpler structure for JS processing
        const formattedData = {};
        Object.keys(groupedMatchups).forEach(sportId => {
            const matches = groupedMatchups[sportId].map(match => ({
                match: `Match ID: ${match.match_id}`,
                matchCode: match.match_id,
                teamA: match.teamA,
                teamB: match.teamB,
            }));
            formattedData[`${sportId}`] = matches;
        });

        displaySportButtons(formattedData); // Display sport buttons with matchups
    });

        let savedMatches = {}; // Track saved scores for each sport
        let tableCreated = {}; // To track if table has been created for each sport

        // Display sport selection buttons
        function displaySportButtons(data) {
            const container = document.getElementById("sport-selection-container");

            // Iterate over each sport in the static data
            Object.keys(data).forEach((sport) => {
                const button = document.createElement("button");
                button.textContent = sport;

                // Add event listener for the sport button click
                button.onclick = () => {
                    // Remove the 'active' class from all buttons
                    const allButtons = container.querySelectorAll("button");
                    allButtons.forEach((btn) => btn.classList.remove("active"));

                    // Add 'active' class to the clicked button
                    button.classList.add("active");

                    // Show the matches for the selected sport
                    showSportMatches(sport, data[sport]);

                    // Initialize saved matches for each sport (only the first time)
                    if (!savedMatches[sport]) {
                        savedMatches[sport] = [];
                    }

                    // Initialize table creation state for the sport (only the first time)
                    if (!tableCreated[sport]) {
                        tableCreated[sport] = false;
                    }
                };

                container.appendChild(button);
            });
        }

        function showSportMatches(sport, matches) {
    const container = document.getElementById("score-input-container");
    container.innerHTML = `<h3>Enter Match Scores for ${sport}</h3>`; // Clear previous content

    // Group matches by group_name
    const groupedMatches = matches.reduce((acc, match) => {
        acc[match.group_name] = (acc[match.group_name] || []).concat(match);
        return acc;
    }, {});

    // Create an array with groups in order (alternating)
    const groupsOrder = Object.keys(groupedMatches);
    let alternatingGroups = [];

    while (groupsOrder.length) {
        alternatingGroups.push(groupsOrder.shift()); // Add Group A first
        if (groupsOrder.length) alternatingGroups.push(groupsOrder.shift()); // Then Group B
    }

    // Iterate over alternating groups and create score input forms
    alternatingGroups.forEach((groupName) => {
        // Iterate over the matches in the current group
        groupedMatches[groupName].forEach((match, index) => {
            const matchBox = document.createElement("div");
            matchBox.classList.add("score-form");
            matchBox.id = `match-${sport}-${index}`; // Add unique ID for each match

            // Display only the match number
            const matchTitle = `Match ${index + 1}`; // Match 1, Match 2, etc.

            matchBox.innerHTML = `
                <div class="match-box">
                    <div class="match-header">
                        <strong>${matchTitle}</strong> <!-- Display Match Number -->
                    </div>
                    <div class="match-details">
                        <div class="team-container">
                            <img src="${match.teamA.logo}" alt="${match.teamA.name}" class="team-logo">
                            <input type="number" id="scoreA-${sport}-${index}" placeholder="Score" min="0" value="${match.scoreA || ''}">
                        </div>
                        <span class="vs">VS</span>
                        <div class="team-container">
                            <input type="number" id="scoreB-${sport}-${index}" placeholder="Score" min="0" value="${match.scoreB || ''}">
                            <img src="${match.teamB.logo}" alt="${match.teamB.name}" class="team-logo">
                        </div>
                    </div>
                    <div class="save-button-container">
                        <button id="saveBtn-${sport}-${index}" onclick="saveScores('${sport}', ${index}, ${JSON.stringify(matches).replace(/"/g, '&quot;')})">Save Score</button>
                    </div>
                </div>
            `;
            container.appendChild(matchBox);
        });
    });
}

function saveScores(sport, matchIndex, matches) {
    const scoreA = document.getElementById(`scoreA-${sport}-${matchIndex}`).value;
    const scoreB = document.getElementById(`scoreB-${sport}-${matchIndex}`).value;

    if (scoreA !== "" && scoreB !== "") {
        // Save the scores to the matches array
        matches[matchIndex].scoreA = parseInt(scoreA, 10);
        matches[matchIndex].scoreB = parseInt(scoreB, 10);

        // Send AJAX request to save the scores in the database
        const match = matches[matchIndex]; // Get the match details
        const matchId = match.matchCode; // Match ID
        const data = {
            match_id: matchId,
            team1_score: scoreA,
            team2_score: scoreB,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content') // CSRF token for security
        };

        // Sending the request to the controller method
        fetch('/save-scores', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
.then(data => {
    if (data.success) {
        // Update the score table immediately
        updateScoreTable(sport, match);

        // Fetch and display the latest scores for this sport
        fetchScoresForSport(sport);

        // Disable inputs and the save button for the saved match
        document.getElementById(`scoreA-${sport}-${matchIndex}`).disabled = true;
        document.getElementById(`scoreB-${sport}-${matchIndex}`).disabled = true;

        const saveButton = document.getElementById(`saveBtn-${sport}-${matchIndex}`);
        if (saveButton) {
            saveButton.disabled = true;
        }
    } else {
        alert('Failed to save the score. Please try again.');
    }
})

        .catch(error => console.error('Error:', error));
    }
}

function fetchScoresForSport(sport) {
    fetch(`/get-scores/${sport}`)
    .then(response => response.json())
    .then(data => {
        // Process and display the scores for the given sport
        showSportMatches(sport, data.matches); // Assume data.matches contains the updated match scores
    })
    .catch(error => console.error('Error fetching scores:', error));
}



        // Update the score table for the sport
        function updateScoreTable(sport, match) {
            const tableBody = document.querySelector(`#score-table-${sport} tbody`);
            
            // Check if a row for this match already exists
            const existingRow = Array.from(tableBody.rows).find(row => row.dataset.matchCode === match.matchCode);
            
            if (existingRow) {
                // If the row exists, update the score
                existingRow.cells[4].textContent = `${match.scoreA} - ${match.scoreB}`;
            } else {
                // If the row doesn't exist, create a new row
                const row = document.createElement("tr");
                row.dataset.matchCode = match.matchCode; // Store the matchCode in the row for easy identification
                row.innerHTML = 
                    `<td>${match.match}</td>
                    <td>
                        <img src="${match.teamA.logo}" alt="${match.teamA.name}" class="team-logo-small">
                        ${match.teamA.name}
                    </td>
                    <td>VS</td>
                    <td>
                        <img src="${match.teamB.logo}" alt="${match.teamB.name}" class="team-logo-small">
                        ${match.teamB.name}
                    </td>
                    <td>${match.scoreA} - ${match.scoreB}</td>`;
                tableBody.appendChild(row);
            }
        }
    </script>
</body>
</html>

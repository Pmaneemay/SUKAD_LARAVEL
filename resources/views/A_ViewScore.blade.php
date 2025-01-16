<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/A_ViewScore.css') }}">
    <link rel="stylesheet" href="{{ asset('css/C_TaskBar.css') }}">
</head>
<body>
    <!-- Taskbar Component -->
    <x-taskbar />

    <!-- Sport Selection and Score View Containers -->
    <div id="sport-selection-container" class="sport-selection-container"></div>
    <div id="score-view-container" class="score-view-container"></div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Data passed from the controller, grouped by sport
            const groupedMatches = @json($groupedMatches);

            displaySportButtons(groupedMatches); // Display sport buttons with matchups
        });

        // Display sport selection buttons
        function displaySportButtons(data) {
            const container = document.getElementById("sport-selection-container");

            // Iterate over each sport in the data
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
                };

                container.appendChild(button);
            });
        }

function showSportMatches(sport, matches) {
    const container = document.getElementById("score-view-container");
    container.innerHTML = `<h3>Match Scores for ${sport}</h3>`;

    const cardContainer = document.createElement("div");
    cardContainer.classList.add("card-container");
    container.appendChild(cardContainer);

    // Reverse the matches to show the most recent first
    matches.forEach((match) => {
        createMatchCard(sport, match, cardContainer);
    });
}

function createMatchCard(sport, match, container) {
    const existingCard = container.querySelector(`[data-match-id="${match.match_id}"]`);

    // If a card with the same match_id exists, update it. Otherwise, create a new one.
    if (existingCard) {
        existingCard.querySelector(".score").textContent = `${match.scoreA} - ${match.scoreB}`;
    } else {
        // Create a new card if it doesn't exist
        const card = document.createElement("div");
        card.classList.add("match-card");
        card.dataset.matchId = match.match_id; // Use match_id as the unique identifier

        card.innerHTML = `
            <div class="match-header">
                <span class="match-number">Match ${match.match_no}</span>
            </div>
            <div class="match-details">
                <div class="team">
                    <img src="${match.teamA.logo}" alt="${match.teamA.name}" class="team-logo">
                </div>
                <span class="score">${match.scoreA} - ${match.scoreB}</span>
                <div class="team">
                    <img src="${match.teamB.logo}" alt="${match.teamB.name}" class="team-logo">
                </div>
            </div>
        `;

        container.appendChild(card);
    }
}

    </script>
</body>
</html>

document.addEventListener("DOMContentLoaded", () => {
    fetch("A_matches.json")
        .then((response) => response.json())
        .then((data) => {
            displaySportButtons(data); // Display sport selection buttons

        });
});

function displaySportButtons(data) {
    const container = document.getElementById("sport-selection-container");

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

            // Always fetch fresh data from localStorage when a sport button is clicked
            const storedData = JSON.parse(localStorage.getItem(sport)) || data[sport];
            showSportScores(sport, storedData);
        };

        container.appendChild(button);
    });
}

//Function to display sport scores
function showSportScores(sport, matches) {
    // Fetch the latest data for the sport from localStorage
    const updatedMatches = JSON.parse(localStorage.getItem(sport)) || matches;

    const container = document.getElementById("score-view-container");
    container.innerHTML = `<h3>Match Scores for ${sport}</h3>`;

    const cardContainer = document.createElement("div");
    cardContainer.classList.add("card-container");
    container.appendChild(cardContainer);

    // Reverse the matches to show the most recent first
    updatedMatches.reverse().forEach((match) => {
        createMatchCard(sport, match, cardContainer);
    });
}



function createMatchCard(sport, match, container) {
    const existingCard = container.querySelector(`[data-match-code="${match.matchCode}"]`);

    if (existingCard) {
        // Update the card if it exists
        existingCard.querySelector(".score").textContent = `${match.scoreA} - ${match.scoreB}`;
    } else {
        // Create a new card if it doesn't exist
        const card = document.createElement("div");
        card.classList.add("match-card");
        card.dataset.matchCode = match.matchCode;

        card.innerHTML = `
            <div class="match-header">
                <span class="match-number">${match.match}</span>
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


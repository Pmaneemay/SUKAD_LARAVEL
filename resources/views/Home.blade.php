<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUKAD Management System - Homepage</title>
    <link rel="stylesheet" href="{{ asset('css/C_TaskBar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/C_HomePage.css')}}">
</head>
<body>
    <x-taskbar />

    <main class="main-content">
        <h1>SUKAN ANTARA DESASISWA</h1>
        <p>UNIVERSITI SAINS MALAYSIA</p>
    </main>

    <section class="announcement">
        <h1>ANNOUNCEMENT</h1>
        <div id="announcementSection"></div> 
    </section>

    <section class="about">
        <h1>ABOUT</h1>
        <div class="about-content">
            <p>SUKAD, also known as Sukad Antara Desasiswa, is a student sports tournament that hosts over 30 events involving students residing across all three campuses: Main Campus, Engineering Campus, and Health Campus, including those living off-campus under PETAS. This tournament is supervised by the Organizing Committee, consisting of officers from the Sports Unit and Desasiswa Heads who participate. The event is organized by the students of USM.</p>
            <img src="{{ asset('images/about.jpg') }}" alt="About Us Image" class="about-img">
        </div>
    </section>

    <footer>
        <p>&copy; 2024 SUKAD Event Management</p>
    </footer>

    <script>
        let currentAnnouncementIndex = 0; 
        let announcements = []; 
        fetchAnnouncements();

        document.addEventListener('DOMContentLoaded', function () {
            fetchAnnouncements();
            startAutoCycle();
        });

        function fetchAnnouncements() {
            fetch("{{ route('getAnnouncements') }}")
                .then(response => response.json())
                .then(data => {
                    announcements = data; 
                    displayAnnouncement(currentAnnouncementIndex);
                })
                .catch(error => console.error('Error fetching announcements:', error));
        }

        function displayAnnouncement(index) {
            const announcementSection = document.getElementById('announcementSection');
            const announcement = announcements[index];

            if (announcement) {
                const announcementDiv = document.createElement('div');
                announcementDiv.classList.add('announcement-box'); 

                if (announcement.image_path) {
                    const image = document.createElement('img');
                    image.src = announcement.image_path; 
                    image.alt = "Announcement Image";
                    announcementDiv.appendChild(image);
                }

                if (announcement.content) {
                    const text = document.createElement('p');
                    text.textContent = announcement.content;
                    text.style.color = announcement.color;
                    text.style.fontWeight = announcement.bold ? 'bold' : 'normal';
                    text.style.fontStyle = announcement.italic ? 'italic' : 'normal';
                    text.style.textDecoration = announcement.underline ? 'underline' : 'none';
                    announcementDiv.appendChild(text);
                }

                const buttonContainer = document.createElement('div');
                buttonContainer.classList.add('button-container');

                const prevButton = document.createElement('button');
                prevButton.id = 'prevButton';
                prevButton.textContent = '<'; 
                prevButton.addEventListener('click', prevAnnouncement);
                buttonContainer.appendChild(prevButton);

                const nextButton = document.createElement('button');
                nextButton.id = 'nextButton';
                nextButton.textContent = '>'; 
                nextButton.addEventListener('click', nextAnnouncement);
                buttonContainer.appendChild(nextButton);

                announcementDiv.appendChild(buttonContainer);

                announcementSection.innerHTML = ''; 
                announcementSection.appendChild(announcementDiv);
            } else {
                announcementSection.innerHTML = `
                    <div class="no-announcement-box">
                        No Announcement
                    </div>
                `;
            }
        }

        function nextAnnouncement() {
            currentAnnouncementIndex = (currentAnnouncementIndex + 1) % announcements.length;
            displayAnnouncement(currentAnnouncementIndex);
        }

        function prevAnnouncement() {
            currentAnnouncementIndex = (currentAnnouncementIndex - 1 + announcements.length) % announcements.length;
            displayAnnouncement(currentAnnouncementIndex);
        }

        function startAutoCycle() {
            setInterval(() => {
                nextAnnouncement();
            }, 3000); 
        }
    </script>
</body>
</html>

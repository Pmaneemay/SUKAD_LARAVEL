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
        <div id="announcementSection"></div> <!-- Announcement will be displayed here -->
    </section>

    <!-- ABOUT Section -->
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
</body>
</html>

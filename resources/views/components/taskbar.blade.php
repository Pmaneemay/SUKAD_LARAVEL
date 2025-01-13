<nav>
    <div class="nav-bar">
        <span class="logo"><a href="{{ route('HomePage') }}">SUKAD</a></span>
        <div class="menu">
            <ul class="nav-links" id="buttonGroup">
                @if(session('role') == 'TMNG')
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('bookings.index') }}">SPORT FACILITIES BOOKING</a></li>
                    <li><a href="{{ route('bookings.past') }}">VIEW BOOKED FACILITIES</a></li>
                    <li><a href="{{ route('TeamManagementPage') }}">TEAM MANAGEMENT</a></li>
                    <li><a href="{{ route('getMatchups') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('score.input') }}">LIVE SCORE</a></li>
                @elseif(session('role') == 'DSAD')
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('bookings.past') }}">VIEW BOOKED FACILITIES</a></li>
                    <li><a href="{{ route('TeamManagementPage') }}">TEAM MANAGEMENT</a></li>
                    <li><a href="{{ route('getMatchups') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('score.input') }}">LIVE SCORE</a></li>
                @elseif(session('role') == 'STUD')
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('bookings.past') }}">VIEW BOOKED FACILITIES</a></li>
                    <li><a href="{{ route('TeamManagementPage') }}">TEAM MANAGEMENT</a></li>
                    <li><a href="{{ route('getMatchups') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('score.input') }}">LIVE SCORE</a></li>
                @elseif(session('role') == 'EORG')
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('getMatchups') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('Announcement') }}">ANNOUNCEMENT CONTENT</a></li>
                    <li><a href="{{ route('score.input') }}">SCORING</a></li>
                    <li><a href="{{ route('bookings.past') }}">VIEW FACILITIES BOOKING</a></li>
                @else
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('getMatchups') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('score.view') }}">LIVE SCORE</a></li>
                @endif
            </ul>
        </div>
        <div class="user-info">
            @if(session('profile'))
                <span class="user-name" onclick="toggleDropdown()">{{ session('profile')->name }}</span>
                <div class="dropdown" id="dropdownMenu" style="display: none;">
                    <a href="{{ route('logout') }}">LOGOUT</a>
                </div>
            @else
                <a href="{{ route('LoginPage') }}">
                    <button>LOGIN</button>
                </a>
            @endif
        </div>
    </div>
</nav>
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('dropdownMenu');
        const isVisible = dropdown.style.display === 'block';
        dropdown.style.display = isVisible ? 'none' : 'block';
    }
</script>
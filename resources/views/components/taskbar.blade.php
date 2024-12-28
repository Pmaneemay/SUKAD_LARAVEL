
<nav>
    <div class="nav-bar">
        <span class="logo"><a href="{{ route('HomePage') }}">SUKAD</a></span>
        <div class="menu">
            <ul class="nav-links" id="buttonGroup">
                
                @if(session('role') ==  'TMNG')
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('HomePage') }}">SPORT FACILITIES BOOKING</a></li>
                    <li><a href="{{ route('HomePage') }}">VIEW BOOKED FACILITIES</a></li>
                    <li><a href="{{ route('TeamManagementPage') }}">TEAM MANAGEMENT</a></li>
                    <li><a href="{{ route('HomePage') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('HomePage') }}">LIVE SCORE</a></li>
                @elseif(session('role') == 'DSAD')
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('HomePage') }}">VIEW BOOKED FACILITIES</a></li>
                    <li><a href="{{ route('TeamManagementPage') }}">TEAM MANAGEMENT</a></li>
                    <li><a href="{{ route('HomePage') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('HomePage') }}">LIVE SCORE</a></li>
                @elseif(session('role') == 'STUD')
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('HomePage') }}">VIEW BOOKED FACILITIES</a></li>
                    <li><a href="{{ route('TeamManagementPage') }}">TEAM MANAGEMENT</a></li>
                    <li><a href="{{ route('HomePage') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('HomePage') }}">LIVE SCORE</a></li>
                @elseif(session('role') == 'EORG')
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('HomePage') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('HomePage') }}">ANNOUNCEMENT CONTENT</a></li>
                    <li><a href="{{ route('HomePage') }}">SCORING</a></li>
                    <li><a href="{{ route('HomePage') }}">VIEW FACILITIES BOOKING</a></li>
                @else
                    <li><a href="{{ route('HomePage') }}">HOME</a></li>
                    <li><a href="{{ route('HomePage') }}">MATCHUP SCHEDULE</a></li>
                    <li><a href="{{ route('HomePage') }}">LIVE SCORE</a></li>
                @endif
            </ul>
        </div>
        <div class="user-info" id="userInfo">
            @if(session('profile'))
                <div class="user-name" onclick="toggleDropdown()">{{ session('profile')->name }}</div>
                <div class="dropdown">
                    <a href="{{ route('logout') }}"><button onclick="logout()">LOGOUT</button>
                </div>
            @else
                <a href="{{ route('LoginPage') }}"><button onclick="login()">LOGIN</button></a>
            @endif
        </div>
    </div>
</nav>

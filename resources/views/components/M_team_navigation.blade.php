<div id="team_nav">
            <ul id="team_nav_list">
                @if(session('role') == 'DSAD')
                    <li><button class="team_navBtn active-btn" id ="AdminmanagerlistBtn">Managers</button></li>
                    <li><button class="team_navBtn" id ="AdminclublistBtn">Teams</button></li>
                @elseif(session('role') == 'TMNG')
                    <li><button class="team_navBtn" id ="TMNGSelectionEventBtn">Player selection</button></li>
                    <li><button class="team_navBtn active-btn" id ="ManagerclublistBtn">My Team</button></li>
                @else
                    <li><button class="team_navBtn" id ="AllSelectionEventlistBtn">Selection Events</button></li>
                    <li><button class="team_navBtn" id ="RegisteredEventsBtn">My Event Registrations</button></li>
                    <li><button class="team_navBtn active-btn" id ="StudentclublistBtn">My Team</button></li>
                @endif
            </ul>  
</div>  
<div class="sidebar">
    <div class="logo">
        <span class="logo-name">LalloCare</span>
    </div>
    <div class="sidebar-content">
        <ul class="lists">
            <li class="list"><a href="{{ route('staff.dashboard') }}" class="nav-link"><i class="bx bx-home-alt icon"></i><span class="link">Dashboard</span></a></li>
            <li class="list"><a href="{{ route('staff.patients') }}" class="nav-link"><i class="bx bx-user icon"></i><span class="link">Patients</span></a></li>
            <li class="list"><a href="{{ route('staff.appointments') }}" class="nav-link"><i class="bx bx-calendar icon"></i><span class="link">Appointments</span></a></li>
            <li class="list"><a href="{{ route('staff.medicine') }}" class="nav-link"><i class="bx bx-capsule icon"></i><span class="link">Medicine Pick-Up</span></a></li>
            <li class="list"><a href="#" class="nav-link"><i class="bx bx-time-five icon"></i><span class="link">Dosage Reminders</span></a></li>
            <li class="list"><a href="#" class="nav-link"><i class="bx bx-refresh icon"></i><span class="link">Follow-Up Checkups</span></a></li>
            <li class="list"><a href="#" class="nav-link"><i class="bx bx-heart icon"></i><span class="link">Health Monitoring</span></a></li>
            <li class="list"><a href="#" class="nav-link"><i class="bx bx-bell icon"></i><span class="link">Notifications</span></a></li>
            <li class="list"><a href="#" class="nav-link"><i class="bx bx-bar-chart-alt icon"></i><span class="link">Reports</span></a></li>
        </ul>
        <div class="bottom-content">
            <ul class="lists">
                <li class="list"><a href="#" class="nav-link"><i class="bx bx-cog icon"></i><span
                            class="link">Settings</span></a></li>
                <li class="list">
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="#" class="nav-link"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-log-out icon"></i><span class="link">Logout</span>
                        </a>
                    </form>
                </li>

            </ul>
        </div>
    </div>
</div>
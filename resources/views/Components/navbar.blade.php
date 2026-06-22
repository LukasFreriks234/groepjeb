<nav class="navbar" aria-label="Main navigation">
    <ul class="navbar-menu">
        <li>
            <a
                href="/grid"
                class="navbar-button"
                @if(request()->is('grid')) aria-current="page" @endif
            >
                Grid
            </a>
        </li>

        <li>
            <a
                href="{{ route('functions.index') }}"
                class="navbar-button"
                @if(request()->routeIs('functions.*', 'overview.groups', 'groups.*', 'events.*')) aria-current="page" @endif
            >
                Overview
            </a>
        </li>

        <li class="navbar-clock-item">
            <div
                class="navbar-clock"
                data-simulation-clock
                aria-live="polite"
                aria-atomic="true"
            >
                <span class="navbar-clock-label">Time</span>

                <div class="navbar-clock-content">
                    <div class="navbar-clock-ring" aria-hidden="true">
                        <svg class="navbar-clock-ring-svg" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
                            <circle class="navbar-clock-ring-track" cx="50" cy="50" r="40" pathLength="100"></circle>
                            <circle class="navbar-clock-ring-fill" data-simulation-clock-progress cx="50" cy="50" r="40" pathLength="100"></circle>
                        </svg>

                        <div class="navbar-clock-ring-core">
                            <span class="navbar-clock-time" data-simulation-clock-time>00:00</span>
                        </div>
                    </div>

                    <span class="navbar-clock-range">/ 24:00</span>
                </div>

                <div class="navbar-date-display" aria-live="polite" aria-atomic="true">
                    <span class="navbar-date-display-label">Date</span>
                    <span class="navbar-date-display-value" data-simulation-date>Jan 1, 2026</span>
                    <div class="navbar-date-popup" data-simulation-date-popup aria-hidden="true">
                        <div class="navbar-date-popup-nav">
                            <button class="navbar-date-popup-arrow" type="button" data-calendar-prev aria-label="Previous month">&lsaquo;</button>
                            <span class="navbar-date-popup-title" data-calendar-title></span>
                            <button class="navbar-date-popup-arrow" type="button" data-calendar-next aria-label="Next month">&rsaquo;</button>
                        </div>
                        <div class="navbar-date-popup-calendar" data-simulation-date-calendar></div>
                    </div>
                </div>

                <button
                    class="navbar-pause-button"
                    type="button"
                    data-simulation-pause
                    aria-label="Pause the simulation"
                >
                    ⏸
                </button>
            </div>
        </li>

        <li class="navbar-logout-item">
            <form method="POST" action="/logout" class="logout-form">
                @csrf

                <button
                    type="submit"
                    class="navbar-button"
                    aria-label="Log out of your account"
                >
                    Log out
                </button>
            </form>
        </li>
    </ul>
</nav>
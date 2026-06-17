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
                @if(request()->routeIs('functions.index')) aria-current="page" @endif
            >
                Functions
            </a>
        </li>

        <li>
            <a href="{{ route('groups.index') }}" class="navbar-button">
                Groups
            </a>
        </li>

        <li>
            <a
                href="{{ route('events.create') }}"
                class="navbar-button"
                @if(request()->routeIs('events.create')) aria-current="page" @endif
            >
                Create Event
            </a>
        </li>

        <li class="navbar-clock-item">
            <div
                class="navbar-clock"
                data-simulation-clock
                aria-live="polite"
                aria-atomic="true"
            >
                <div class="navbar-speed-control">
                    <label class="navbar-speed-label" for="simulation-speed">Speed:</label>
                    <div class="navbar-speed-row">
                        <input
                            id="simulation-speed"
                            class="navbar-speed-input"
                            type="number"
                            min="1"
                            max="1440"
                            value="24"
                            data-simulation-speed-input
                            aria-label="Simulation minutes per second"
                        />
                        <span class="navbar-speed-unit">min/s</span>
                        <button
                            class="navbar-speed-button"
                            type="button"
                            data-simulation-speed-confirm
                            aria-label="Apply speed"
                        >
                            Set
                        </button>
                    </div>
                </div>

                <div class="navbar-clock-divider" aria-hidden="true"></div>

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

                </div>

                <div class="navbar-day-counter" aria-live="polite" aria-atomic="true">
                    <span class="navbar-day-counter-label">Days</span>
                    <span class="navbar-day-counter-value" data-simulation-day-counter>0</span>
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

<script src="{{ asset('js/simulationTimer.js') }}?v={{ filemtime(public_path('js/simulationTimer.js')) }}" defer></script>

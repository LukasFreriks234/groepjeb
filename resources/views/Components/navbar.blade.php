<nav class="navbar" aria-label="Main Navigation">
    <ul class="navbar-menu">
        <li>
            <a href="/grid" class="navbar-button">
                Grid
            </a>
        </li>

        <li>
            <a href="{{ route('functions.index') }}" class="navbar-button">
                Overview
            </a>
        </li>

        <li>
            <form method="POST" action="/logout" class="logout-form">
                @csrf
                <button type="submit" class="navbar-button">
                    Log Out
                </button>
            </form>
        </li>
    </ul>
</nav>
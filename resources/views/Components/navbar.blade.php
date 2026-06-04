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
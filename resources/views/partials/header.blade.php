<header class="header">

    <div class="logo">
        <a href="{{ route('home') }}">D&D Campaign Manager</a>
    </div>

    <nav class="navigation">
        <a href="{{ route('characters.index') }}">Персонажи</a>
        <a href="#">Кампании</a>
    </nav>

    <div class="profile-menu">

        @auth
            <div class="profile-dropdown" id="profileDropdown">

                <button type="button" class="profile-button" id="profileButton">
                    {{ auth()->user()->name }} ▾
                </button>

                <div class="dropdown-content" id="dropdownContent">

                    <a href="{{ route('profile') }}">Профиль</a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">Выход</button>
                    </form>

                </div>

            </div>
        @else
            <a class="header-button" href="{{ route('login') }}">Войти</a>
        @endauth

    </div>

</header>

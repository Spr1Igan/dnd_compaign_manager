<header class="header">
    @php
        $locales = [
            'ru' => 'RU',
            'en' => 'EN',
        ];

        $currentLocale = app()->getLocale();
    @endphp

    <div class="logo">
        <a href="{{ route('home') }}">D&D Campaign Manager</a>
    </div>

    <nav class="navigation">
        <a href="{{ route('characters.index') }}">{{ __('ui.characters') }}</a>
        <a href="#">{{ __('ui.campaigns') }}</a>
        <a href="{{ route('data.index') }}">{{ __('ui.data') }}</a>
    </nav>

    <div class="header-actions">
        <form class="locale-switcher" action="{{ route('locale.update') }}" method="POST" aria-label="{{ __('ui.language') }}">
            @csrf

            @foreach ($locales as $locale => $label)
                <button
                    class="locale-button @if ($currentLocale === $locale) is-active @endif"
                    type="submit"
                    name="locale"
                    value="{{ $locale }}"
                    aria-pressed="{{ $currentLocale === $locale ? 'true' : 'false' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </form>

        <div class="profile-menu">

            @auth
                <div class="profile-dropdown" id="profileDropdown">

                    <button type="button" class="profile-button" id="profileButton">
                        {{ auth()->user()->name }} ▾
                    </button>

                    <div class="dropdown-content" id="dropdownContent">

                        <a href="{{ route('profile') }}">{{ __('ui.profile') }}</a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit">{{ __('ui.logout') }}</button>
                        </form>

                    </div>

                </div>
            @else
                <a class="header-button" href="{{ route('login') }}">{{ __('ui.login') }}</a>
            @endauth

        </div>
    </div>

</header>

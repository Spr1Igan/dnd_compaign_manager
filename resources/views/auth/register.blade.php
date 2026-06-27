<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('ui.auth.register_title') }}</title>
    @vite('resources/css/app.css')
</head>
<body>

<div class="auth-page">
    <form class="paper-form" method="POST" action="{{ route('register') }}">
        @csrf

        <h1>{{ __('ui.auth.register_title') }}</h1>

        @foreach ($errors->all() as $error)
            <p class="error">{{ $error }}</p>
        @endforeach

        <label for="name">{{ __('ui.auth.name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('ui.auth.enter_name') }}">

        <label for="login">{{ __('ui.auth.login') }}</label>
        <input id="login" type="text" name="login" value="{{ old('login') }}" placeholder="{{ __('ui.auth.enter_login') }}">

        <label for="password">{{ __('ui.auth.password') }}</label>
        <input id="password" type="password" name="password" placeholder="{{ __('ui.auth.enter_password') }}">

        <label for="password_confirmation">{{ __('ui.auth.password_confirmation') }}</label>
        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="{{ __('ui.auth.password_confirmation') }}">

        <button type="submit">{{ __('ui.register') }}</button>

        <p class="auth-link">
            {{ __('ui.auth.has_account') }}
            <a href="{{ route('login') }}">{{ __('ui.login') }}</a>
        </p>
    </form>
</div>

</body>
</html>

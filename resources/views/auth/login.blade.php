<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('ui.auth.login_title') }}</title>
    @vite('resources/css/app.css')
</head>
<body>

<div class="auth-page">
    <form class="paper-form" method="POST" action="{{ route('login') }}">
        @csrf

        <h1>{{ __('ui.auth.login_title') }}</h1>

        @foreach ($errors->all() as $error)
            <p class="error">{{ $error }}</p>
        @endforeach

        <label for="login">{{ __('ui.auth.login') }}</label>
        <input id="login" type="text" name="login" value="{{ old('login') }}" placeholder="{{ __('ui.auth.enter_login') }}">

        <label for="password">{{ __('ui.auth.password') }}</label>
        <input id="password" type="password" name="password" placeholder="{{ __('ui.auth.enter_password') }}">

        <label class="checkbox-row">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            {{ __('ui.auth.remember') }}
        </label>

        <button type="submit">{{ __('ui.login') }}</button>

        <p class="auth-link">
            {{ __('ui.auth.no_account') }}
            <a href="{{ route('register') }}">{{ __('ui.register') }}</a>
        </p>
    </form>
</div>

</body>
</html>

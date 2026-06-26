<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    @vite('resources/css/app.css')
</head>
<body>

<div class="auth-page">
    <form class="paper-form" method="POST" action="{{ route('login') }}">
        @csrf

        <h1>Вход</h1>

        @foreach ($errors->all() as $error)
            <p class="error">{{ $error }}</p>
        @endforeach

        <label for="login">Логин</label>
        <input id="login" type="text" name="login" value="{{ old('login') }}" placeholder="Введите логин">

        <label for="password">Пароль</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">

        <label class="checkbox-row">
            <input type="checkbox" name="remember" value="1">
            Запомнить меня
        </label>

        <button type="submit">Войти</button>

        <p class="auth-link">
            Нет аккаунта?
            <a href="{{ route('register') }}">Зарегистрироваться</a>
        </p>
    </form>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    @vite('resources/css/app.css')
</head>
<body>

<div class="auth-page">
    <form class="paper-form" method="POST" action="{{ route('register') }}">
        @csrf

        <h1>Регистрация</h1>

        @foreach ($errors->all() as $error)
            <p class="error">{{ $error }}</p>
        @endforeach

        <label for="name">Имя</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Введите имя">

        <label for="login">Логин</label>
        <input id="login" type="text" name="login" value="{{ old('login') }}" placeholder="Введите логин">

        <label for="password">Пароль</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">

        <label for="password_confirmation">Повторите пароль</label>
        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Повторите пароль">

        <button type="submit">Зарегистрироваться</button>

        <p class="auth-link">
            Уже есть аккаунт?
            <a href="{{ route('login') }}">Войти</a>
        </p>
    </form>
</div>

</body>
</html>

@extends('layouts.app')

@section('title', 'Профиль')

@section('content')

<section class="paper-panel">
    <h1>Профиль</h1>

    <p><b>ID:</b> {{ auth()->user()->id }}</p>
    <p><b>Имя:</b> {{ auth()->user()->name }}</p>
    <p><b>Логин:</b> {{ auth()->user()->login }}</p>

    <a class="paper-button" href="{{ route('home') }}">На главную</a>
</section>

@endsection

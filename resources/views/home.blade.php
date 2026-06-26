@extends('layouts.app')

@section('title', 'Главная')

@section('content')

<section class="home-hero">

    <h1>D&D Campaign Manager</h1>

    <p>
        Управляй персонажами, кампаниями и приключениями в одном месте.
    </p>

</section>

<section class="home-cards">

    <a href="{{ route('characters.index') }}" class="paper-card">
        <h2>Персонажи</h2>
        <p>Создавай героев, храни характеристики, снаряжение и историю.</p>
        <span>Открыть →</span>
    </a>

    <a href="#" class="paper-card">
        <h2>Кампании</h2>
        <p>Веди игровые сессии, заметки, NPC и сюжетные линии.</p>
        <span>Открыть →</span>
    </a>

</section>

@endsection

@extends('layouts.app')

@section('title', 'Персонажи')

@section('content')

<div class="page-heading">
    <div>
        <h1>Персонажи</h1>
        <p>Список твоих героев и NPC.</p>
    </div>

    <a class="paper-button" href="{{ route('characters.create') }}">
        + Создать персонажа
    </a>
</div>

@if (session('success'))
    <p class="success-message">{{ session('success') }}</p>
@endif

@if ($characters->isEmpty())

    <div class="paper-panel">
        <h2>Пока нет персонажей</h2>
        <p>Создай первого героя для своей кампании.</p>
    </div>

@else

    <div class="character-grid">

        @foreach ($characters as $character)

            <a class="character-card" href="{{ route('characters.show', $character) }}">
                <h2>{{ $character->name }}</h2>

                <p>
                    {{ $character->race?->name ?? 'Без расы' }}
                    /
                    {{ $character->characterClass?->name ?? 'Без класса' }}
                </p>

                <div class="character-stats">
                    <span>Ур. {{ $character->level }}</span>
                    <span>КД {{ $character->effectiveArmorClass() }}</span>
                    <span>ХП {{ $character->current_hp }}/{{ $character->max_hp }}</span>
                </div>
            </a>

        @endforeach

    </div>

@endif

@endsection

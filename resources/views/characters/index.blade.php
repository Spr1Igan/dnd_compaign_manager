@extends('layouts.app')

@section('title', __('ui.characters_page.title'))

@section('content')

<div class="page-heading">
    <div>
        <h1>{{ __('ui.characters_page.title') }}</h1>
        <p>{{ __('ui.characters_page.subtitle') }}</p>
    </div>

    <a class="paper-button" href="{{ route('characters.create') }}">
        + {{ __('ui.characters_page.create') }}
    </a>
</div>

@if (session('success'))
    <p class="success-message">{{ session('success') }}</p>
@endif

@if ($characters->isEmpty())

    <div class="paper-panel">
        <h2>{{ __('ui.characters_page.empty_title') }}</h2>
        <p>{{ __('ui.characters_page.empty_text') }}</p>
    </div>

@else

    <div class="character-grid">

        @foreach ($characters as $character)

            <a class="character-card" href="{{ route('characters.show', $character) }}">
                <h2>{{ $character->name }}</h2>

                @if ($isGameMaster && $character->user)
                    <span class="character-owner-badge">
                        Игрок: {{ $character->user->name }}
                    </span>
                @endif

                <p>
                    {{ $character->race ? \App\Models\Character::readableRuleLabel($character->race->slug) : __('ui.characters_page.no_race') }}
                    @if ($character->subrace)
                        / {{ \App\Models\Character::readableRuleLabel($character->subrace->slug) }}
                    @endif
                    /
                    {{ $character->characterClass ? \App\Models\Character::readableRuleLabel($character->characterClass->slug) : __('ui.characters_page.no_class') }}
                    @if ($character->characterSubclass)
                        / {{ \App\Models\Character::readableRuleLabel($character->characterSubclass->slug) }}
                    @endif
                </p>

                <div class="character-stats">
                    <span>{{ __('ui.characters_page.level_short') }} {{ $character->level }}</span>
                    <span>{{ __('ui.characters_page.armor_short') }} {{ $character->effectiveArmorClass() }}</span>
                    <span>{{ __('ui.characters_page.hp_short') }} {{ $character->current_hp }}/{{ $character->max_hp }}</span>
                </div>
            </a>

        @endforeach

    </div>

@endif

@endsection

@extends('layouts.app')

@section('title', 'Данные')

@section('content')

<div class="page-heading data-heading">
    <div>
        <p class="eyebrow">Справочник мастера</p>
        <h1>Данные</h1>
        <p>Проверенный набор D&D 5e из локальных JSON: правила, предметы, сокровища, заклинания и монстры без старого сырого экспорта.</p>
    </div>
</div>

@unless ($dataExists)
    <div class="paper-error">
        Папка <strong>some_data/dnd5e_equipment_ru</strong> не найдена. Справочник пока не может прочитать данные.
    </div>
@endunless

<form class="data-search" method="GET" action="{{ route('data.index') }}">
    <label for="data-search">Поиск по справочнику</label>
    <div>
        <input id="data-search" name="q" value="{{ $query }}" placeholder="Например: меч, яд, отдых, blinded, fireball">
        <button class="paper-button" type="submit">Найти</button>
    </div>
</form>

@if ($query !== '')
    <section class="data-section">
        <div class="data-section-title">
            <h2>Результаты поиска</h2>
            <span>{{ count($results) }}</span>
        </div>

        @if (count($results) > 0)
            <div class="data-result-list">
                @foreach ($results as $entry)
                    <a class="data-result-card" href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">
                        <span>{{ $entry['category_title'] }}</span>
                        <strong>{{ $entry['name'] }}</strong>
                        <p>{{ $entry['excerpt'] ?: 'Открой карточку, чтобы увидеть подробности.' }}</p>
                    </a>
                @endforeach
            </div>
        @else
            <div class="paper-panel">
                Ничего не найдено. Попробуй другое слово или открой категорию вручную.
            </div>
        @endif
    </section>
@endif

<div class="data-group-tabs" aria-label="Группы данных">
    @foreach ($groups as $group)
        <a href="#data-group-{{ $group['slug'] }}">
            <span>{{ $group['title'] }}</span>
            <strong>{{ $group['count'] }}</strong>
        </a>
    @endforeach
</div>

@foreach ($groups as $group)
    <section class="data-section data-group-section" id="data-group-{{ $group['slug'] }}">
        <div class="data-section-title">
            <div>
                <h2>{{ $group['title'] }}</h2>
                <p>{{ $group['description'] }}</p>
            </div>
            <span>{{ $group['count'] }}</span>
        </div>

        <div class="data-category-grid">
            @foreach ($group['categories'] as $category)
                <a class="data-category-card @if ($category['count'] === 0) is-empty @endif" href="{{ route('data.category', $category['slug']) }}">
                    <span>{{ $category['count'] > 0 ? $category['count'].' записей' : 'нет данных' }}</span>
                    <h2>{{ $category['title'] }}</h2>
                    <p>{{ $category['description'] }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endforeach

@if (! empty($metadata))
    <section class="data-source-note">
        Источник: {{ $metadata['title'] ?? 'dnd5e_equipment_ru' }},
        {{ $metadata['rules_version'] ?? $metadata['edition'] ?? 'D&D 5e' }}.
        {{ $metadata['completeness_note'] ?? '' }}
    </section>
@endif

@endsection

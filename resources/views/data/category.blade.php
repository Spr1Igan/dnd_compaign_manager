@extends('layouts.app')

@section('title', $category['title'])

@section('content')

<div class="page-heading data-heading">
    <div>
        <p class="eyebrow">Справочник мастера</p>
        <h1>{{ $category['title'] }}</h1>
        <p>{{ $category['description'] }}</p>
    </div>

    <a class="paper-button secondary" href="{{ route('data.index') }}">Назад к данным</a>
</div>

<form class="data-search compact" method="GET" action="{{ route('data.category', $category['slug']) }}">
    <label for="category-search">Поиск в категории</label>
    <div>
        <input id="category-search" name="q" value="{{ $query }}" placeholder="Название или слово из описания">
        <button class="paper-button" type="submit">Найти</button>
    </div>
</form>

<section class="data-section">
    <div class="data-section-title">
        <h2>{{ $query !== '' ? 'Найдено' : 'Все записи' }}</h2>
        <span>{{ count($entries) }}</span>
    </div>

    @if (count($entries) > 0)
        <div class="data-result-list">
            @foreach ($entries as $entry)
                <a class="data-result-card" href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">
                    <span>
                        {{ $entry['category_title'] }}
                        @if($entry['page_pdf'])
                            · стр. {{ $entry['page_pdf'] }}
                        @endif
                    </span>
                    <strong>{{ $entry['name'] }}</strong>
                    <p>{{ $entry['excerpt'] ?: 'Описание будет доступно в карточке.' }}</p>
                </a>
            @endforeach
        </div>
    @else
        <div class="paper-panel">
            Записей не найдено.
        </div>
    @endif
</section>

@endsection

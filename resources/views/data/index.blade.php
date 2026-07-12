@extends('layouts.app')

@section('title', 'Данные')

@section('content')

<div class="page-heading data-heading">
    <div>
        <p class="eyebrow">Справочник мастера</p>
        <h1>Данные</h1>
        <p>Быстрый доступ к сущностям, главам и таблицам из Руководства Мастера.</p>
    </div>
</div>

@unless ($dataExists)
    <div class="paper-error">
        Папка <strong>some_data/dmg_structured_export</strong> не найдена. Справочник пока не может прочитать данные.
    </div>
@endunless

<form class="data-search" method="GET" action="{{ route('data.index') }}">
    <label for="data-search">Поиск по справочнику</label>
    <div>
        <input id="data-search" name="q" value="{{ $query }}" placeholder="Например: зелье, ловушка, безумие, артефакт">
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
                        <span>{{ $entry['category_title'] }} @if($entry['page_pdf']) · стр. {{ $entry['page_pdf'] }} @endif</span>
                        <strong>{{ $entry['name'] }}</strong>
                        <p>{{ $entry['excerpt'] ?: 'Описание будет доступно в карточке.' }}</p>
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

<section class="data-section">
    <div class="data-section-title">
        <h2>Сущности</h2>
        <span>{{ collect($categories)->sum('count') }}</span>
    </div>

    <div class="data-category-grid">
        @foreach ($categories as $category)
            <a class="data-category-card" href="{{ route('data.category', $category['slug']) }}">
                <span>{{ $category['count'] }} записей</span>
                <h2>{{ $category['title'] }}</h2>
                <p>{{ $category['description'] }}</p>
            </a>
        @endforeach
    </div>
</section>

<section class="data-lower-grid">
    <a class="data-large-link" href="{{ route('data.chapters') }}">
        <span>Текст и оглавление</span>
        <h2>Главы книги</h2>
        <p>Открывай разделы Руководства Мастера по страницам, если нужно свериться с контекстом.</p>
    </a>

    <a class="data-large-link" href="{{ route('data.tables') }}">
        <span>Кандидаты таблиц</span>
        <h2>Таблицы</h2>
        <p>Собранные строки таблиц по главам. Часть сложных таблиц может требовать ручной проверки.</p>
    </a>
</section>

@if (! empty($metadata))
    <section class="data-source-note">
        Источник: {{ $metadata['title'] ?? 'Руководство Мастера' }},
        {{ $metadata['edition'] ?? '5e' }},
        страниц PDF: {{ $metadata['pdf_pages'] ?? '—' }}.
    </section>
@endif

@endsection

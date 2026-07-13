@extends('layouts.app')

@section('title', $entry['name'])

@section('content')

<div class="page-heading data-heading">
    <div>
        <p class="eyebrow">{{ $category['title'] }}</p>
        <h1>{{ $entry['name'] }}</h1>
        <p>
            Запись {{ $entry['index'] + 1 }} из {{ $total }}
            @if($entry['page_pdf'])
                · страница PDF {{ $entry['page_pdf'] }}
            @endif
        </p>
    </div>

    <a class="paper-button secondary" href="{{ route('data.category', $category['slug']) }}">К списку</a>
</div>

<article class="data-detail-card">
    <div class="data-detail-meta">
        <span>{{ $category['title'] }}</span>
        @if($entry['page_pdf'])
            <span>Страница PDF: {{ $entry['page_pdf'] }}</span>
        @endif
        @if($entry['is_manual'])
            <span>Проверено вручную</span>
        @endif
    </div>

    @if ($entry['type'] || $entry['price_gp'] || $entry['saving_throw'] || $entry['dc'] || $entry['attack_bonus'])
        <div class="data-stat-grid">
            @if ($entry['type'])
                <div>
                    <span>Тип</span>
                    <strong>{{ $entry['type'] }}</strong>
                </div>
            @endif

            @if ($entry['price_gp'])
                <div>
                    <span>Цена</span>
                    <strong>{{ $entry['price_gp'] }} зм</strong>
                </div>
            @endif

            @if ($entry['saving_throw'])
                <div>
                    <span>Спасбросок</span>
                    <strong>{{ $entry['saving_throw'] }}</strong>
                </div>
            @endif

            @if ($entry['dc'])
                <div>
                    <span>Сложность</span>
                    <strong>Сл {{ $entry['dc'] }}</strong>
                </div>
            @endif

            @if ($entry['attack_bonus'])
                <div>
                    <span>Бонус атаки</span>
                    <strong>+{{ $entry['attack_bonus'] }}</strong>
                </div>
            @endif
        </div>
    @endif

    @if ($entry['description'] !== '')
        <div class="data-detail-text">
            @foreach (preg_split('/(?<=[.!?])\s+/u', $entry['description']) as $paragraph)
                @if (trim($paragraph) !== '')
                    <p>{{ $paragraph }}</p>
                @endif
            @endforeach
        </div>
    @endif

    @if (! empty($entry['sections']))
        <div class="data-section-grid">
            @foreach ($entry['sections'] as $section)
                <section class="data-info-section">
                    <h2>{{ $section['title'] }}</h2>
                    @if (count($section['items']) === 1)
                        <p>{{ $section['items'][0] }}</p>
                    @else
                        <ul>
                            @foreach ($section['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            @endforeach
        </div>
    @endif

    @if (! empty($entry['effect']))
        <div class="data-effect-list">
            <h2>Эффект</h2>
            <ol>
                @foreach ($entry['effect'] as $effect)
                    <li>{{ $effect }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    @if ($entry['description'] === '' && empty($entry['sections']) && empty($entry['effect']))
        <div class="paper-panel">
            У этой записи нет отдельного описания в автоматическом экспорте. Проверь соседние записи или главу-источник.
        </div>
    @endif

    @if (! empty($entry['tags']))
        <div class="data-tag-list">
            @foreach ($entry['tags'] as $tag)
                <span>{{ $tag }}</span>
            @endforeach
        </div>
    @endif
</article>

<div class="data-detail-nav">
    @if ($previous)
        <a class="paper-button secondary" href="{{ route('data.entity', [$previous['category_slug'], $previous['index']]) }}">← {{ $previous['name'] }}</a>
    @else
        <span></span>
    @endif

    @if ($next)
        <a class="paper-button secondary" href="{{ route('data.entity', [$next['category_slug'], $next['index']]) }}">{{ $next['name'] }} →</a>
    @endif
</div>

@endsection

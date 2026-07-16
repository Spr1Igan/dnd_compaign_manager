@extends('layouts.app')

@section('title', $entry['name'])

@section('content')

<div class="page-heading data-heading">
    <div>
        <p class="eyebrow">{{ $category['title'] }}</p>
        <h1>{{ $entry['name'] }}</h1>
        <p>
            Запись {{ $entry['index'] + 1 }} из {{ $total }}
            @if($entry['english_name'])
                · {{ $entry['english_name'] }}
            @endif
        </p>
    </div>

    <a class="paper-button secondary" href="{{ route('data.category', $category['slug']) }}">К списку</a>
</div>

<article class="data-detail-card">
    <div class="data-detail-meta">
        <span>{{ $category['title'] }}</span>
        @if($entry['type'])<span>{{ $entry['type'] }}</span>@endif
        @if($entry['is_manual'])<span>Сохранено из ручной структуры</span>@endif
    </div>

    @if (! empty($entry['stats']))
        <div class="data-stat-grid">
            @foreach ($entry['stats'] as $stat)
                <div>
                    <span>{{ $stat['label'] }}</span>
                    <strong>{{ $stat['value'] }}</strong>
                </div>
            @endforeach
        </div>
    @endif

    @if (! empty($entry['properties']))
        <div class="item-property-list">
            <span>Свойства</span>
            @foreach ($entry['properties'] as $property)
                <strong>{{ $property }}</strong>
            @endforeach
        </div>
    @endif

    @if ($entry['category_view'] === 'monsters')
        <section class="monster-stat-block">
            @if (! empty($entry['abilities']))
                <div class="monster-ability-grid">
                    @foreach ($entry['abilities'] as $ability)
                        <div>
                            <span>{{ $ability['title'] }}</span>
                            <strong>{{ $ability['value'] }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="monster-info-grid">
                @if (! empty($entry['saves']))
                    <section>
                        <h2>Спасброски</h2>
                        <p>
                            @foreach ($entry['saves'] as $save)
                                {{ $save['name'] }} +{{ $save['value'] }}@if (! $loop->last), @endif
                            @endforeach
                        </p>
                    </section>
                @endif

                @if (! empty($entry['skills']))
                    <section>
                        <h2>Навыки</h2>
                        <p>
                            @foreach ($entry['skills'] as $skill)
                                {{ $skill['name'] }} +{{ $skill['value'] }}@if (! $loop->last), @endif
                            @endforeach
                        </p>
                    </section>
                @endif

                @if (! empty($entry['senses']))
                    <section>
                        <h2>Чувства</h2>
                        <p>
                            @foreach ($entry['senses'] as $sense)
                                {{ $sense['name'] }} {{ $sense['value'] }}@if (! $loop->last), @endif
                            @endforeach
                        </p>
                    </section>
                @endif

                <section>
                    <h2>Языки</h2>
                    <p>{{ ! empty($entry['languages']) ? implode(', ', $entry['languages']) : '—' }}</p>
                </section>
            </div>

            @if (! empty($entry['traits']))
                <section class="monster-text-section">
                    <h2>Особенности</h2>
                    @foreach ($entry['traits'] as $trait)
                        <article>
                            <strong>{{ $trait['name'] }}</strong>
                            <p>{{ $trait['description'] }}</p>
                        </article>
                    @endforeach
                </section>
            @endif

            @if (! empty($entry['actions']))
                <section class="monster-text-section">
                    <h2>Действия</h2>
                    @foreach ($entry['actions'] as $action)
                        <article>
                            <strong>{{ $action['name'] }}</strong>
                            <p>{{ $action['description'] }}</p>
                        </article>
                    @endforeach
                </section>
            @endif
        </section>
    @endif

    @if ($entry['description'] !== '' && $entry['category_view'] !== 'monsters')
        <div class="data-detail-text">
            @foreach (preg_split('/\R+/u', $entry['description']) as $paragraph)
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
                    @foreach (preg_split('/\R+/u', $section['text']) as $line)
                        @if (trim($line) !== '')
                            <p>{{ $line }}</p>
                        @endif
                    @endforeach
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

    @if ($entry['description'] === '' && empty($entry['sections']) && empty($entry['effect']) && $entry['category_view'] !== 'monsters')
        <div class="paper-panel">
            У этой записи пока нет отдельного описания, но основные данные показаны в карточках выше.
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

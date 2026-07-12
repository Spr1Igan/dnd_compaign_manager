@extends('layouts.app')

@section('title', $chapter['title'])

@section('content')

<div class="page-heading data-heading">
    <div>
        <p class="eyebrow">Глава книги</p>
        <h1>{{ $chapter['title'] }}</h1>
        <p>Страница {{ $pageIndex + 1 }} из {{ $pagesCount }} в этом разделе.</p>
    </div>

    <a class="paper-button secondary" href="{{ route('data.chapters') }}">К главам</a>
</div>

<div class="data-page-nav">
    @if ($pageIndex > 0)
        <a class="paper-button secondary" href="{{ route('data.chapter', [$chapter['id'], 'page' => $pageIndex - 1]) }}">← Предыдущая</a>
    @else
        <span></span>
    @endif

    <span>
        PDF {{ $page['page_pdf'] ?? '—' }}
        @if($page['printed_page'] ?? null)
            · печатная {{ $page['printed_page'] }}
        @endif
    </span>

    @if ($pageIndex + 1 < $pagesCount)
        <a class="paper-button secondary" href="{{ route('data.chapter', [$chapter['id'], 'page' => $pageIndex + 1]) }}">Следующая →</a>
    @endif
</div>

<article class="data-reader">
    @if ($page)
        @if (! empty($page['headings']))
            <div class="data-page-headings">
                @foreach ($page['headings'] as $heading)
                    <span>{{ $heading['text'] ?? '' }}</span>
                @endforeach
            </div>
        @endif

        @forelse (($page['paragraphs'] ?? []) as $paragraph)
            <p>{{ $paragraph }}</p>
        @empty
            <p>На этой странице нет распознанных абзацев.</p>
        @endforelse
    @else
        <p>Страница не найдена.</p>
    @endif
</article>

@endsection

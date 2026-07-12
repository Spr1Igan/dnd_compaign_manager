@extends('layouts.app')

@section('title', 'Главы книги')

@section('content')

<div class="page-heading data-heading">
    <div>
        <p class="eyebrow">Справочник мастера</p>
        <h1>Главы книги</h1>
        <p>Оглавление структурированного экспорта. Используй его для проверки контекста и страниц источника.</p>
    </div>

    <a class="paper-button secondary" href="{{ route('data.index') }}">Назад к данным</a>
</div>

<section class="data-chapter-list">
    @foreach ($chapters as $chapter)
        <a class="data-chapter-card" href="{{ route('data.chapter', $chapter['id']) }}">
            <span>
                PDF {{ $chapter['page_pdf_start'] ?? '—' }}
                @if($chapter['page_pdf_end'])
                    –{{ $chapter['page_pdf_end'] }}
                @endif
            </span>
            <strong>{{ $chapter['title'] }}</strong>
        </a>
    @endforeach
</section>

@endsection

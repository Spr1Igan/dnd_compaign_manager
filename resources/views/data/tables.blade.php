@extends('layouts.app')

@section('title', 'Таблицы')

@section('content')

<div class="page-heading data-heading">
    <div>
        <p class="eyebrow">Справочник мастера</p>
        <h1>Таблицы</h1>
        <p>Автоматически выделенные строки таблиц. Сложные двухколоночные места лучше сверять с главой-источником.</p>
    </div>

    <a class="paper-button secondary" href="{{ route('data.index') }}">Назад к данным</a>
</div>

<section class="data-table-list">
    @foreach ($tables as $table)
        <details class="data-table-card">
            <summary>
                <span>{{ $table['rows_count'] }} строк</span>
                <strong>{{ $table['section'] }}</strong>
                <small>
                    @if (count($table['pages']) > 0)
                        PDF: {{ implode(', ', array_slice($table['pages'], 0, 8)) }}{{ count($table['pages']) > 8 ? '…' : '' }}
                    @else
                        PDF: —
                    @endif
                </small>
            </summary>

            <div class="data-table-preview">
                @foreach ($table['sample_rows'] as $row)
                    <div class="data-table-row">
                        <span>стр. {{ $row['page_pdf'] ?? '—' }}</span>
                        @foreach (($row['cells'] ?? []) as $cell)
                            <p>{{ $cell }}</p>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </details>
    @endforeach
</section>

@endsection

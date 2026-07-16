@extends('layouts.app')

@section('title', __('ui.campaigns'))

@section('content')

<div class="page-heading campaign-heading">
    <div>
        <p class="eyebrow">Мастерская ведущего</p>
        <h1>Кампании</h1>
        <p>Генератор случайных наград по таблицам к100: индивидуальная добыча, сокровищница и быстрый подбор предметов.</p>
    </div>
</div>

<form class="treasure-generator-form" method="GET" action="{{ route('campaigns.index') }}" data-treasure-generator-form>
    <input type="hidden" name="generate" value="1">

    <label>
        <span>Режим</span>
        <select name="mode" data-treasure-mode>
            @foreach ($modes as $value => $label)
                <option value="{{ $value }}" @selected($options['mode'] === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label>
        <span>Опасность</span>
        <select name="danger">
            @foreach ($dangerTiers as $value => $label)
                <option value="{{ $value }}" @selected($options['danger'] === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label data-items-only-field>
        <span>Фокус</span>
        <select name="focus">
            @foreach ($focuses as $value => $label)
                <option value="{{ $value }}" @selected($options['focus'] === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </label>

    <label data-items-only-field>
        <span>Предметов</span>
        <input type="number" name="count" min="1" max="12" value="{{ $options['count'] }}">
    </label>

    <button class="paper-button" type="submit">Сгенерировать</button>

    <p class="treasure-generator-note" data-table-mode-note>
        Фокус и количество используются только в режиме быстрых случайных предметов. Сокровищница и индивидуальная добыча идут строго по таблицам к100.
    </p>
</form>

@if ($result)
    <section class="treasure-result">
        <div class="data-section-title">
            <div>
                <h2>Результат</h2>
                <p>
                    {{ $modes[$options['mode']] ?? 'Генерация' }} · {{ $dangerTiers[$options['danger']] }}
                    @if ($result['roll'])
                        · к100: <strong>{{ str_pad((string) $result['roll'], 2, '0', STR_PAD_LEFT) }}</strong>
                    @endif
                </p>
            </div>
            <span>{{ collect([...$result['items'], ...$result['valuables']])->sum(fn ($item) => $item['quantity'] ?? 1) }}</span>
        </div>

        @if ($result['note'])
            <div class="paper-panel">{{ $result['note'] }}</div>
        @endif

        <div class="treasure-layout">
            <section class="treasure-box">
                <h2>Монеты</h2>
                @forelse ($result['coins'] as $label => $value)
                    <div class="treasure-line">
                        <span>{{ $label }}</span>
                        <strong>{{ $value }}</strong>
                    </div>
                @empty
                    <p>Монеты не выпали.</p>
                @endforelse
            </section>

            @if (! empty($result['magic_requests']))
                <section class="treasure-box">
                    <h2>Броски магических таблиц</h2>
                    @foreach ($result['magic_requests'] as $request)
                        <div class="treasure-line">
                            <span>Таблица {{ $request['table'] }}</span>
                            <strong>{{ $request['count'] }}</strong>
                        </div>
                    @endforeach
                </section>
            @endif
        </div>

        @if (! empty($result['valuables']))
            <section class="treasure-list-section">
                <h2>Драгоценности и искусство</h2>
                <div class="treasure-card-grid">
                    @foreach ($result['valuables'] as $item)
                        @include('campaigns.partials.reward-card', ['item' => $item])
                    @endforeach
                </div>
            </section>
        @endif

        @if (! empty($result['items']))
            <section class="treasure-list-section">
                <h2>Предметы</h2>
                <div class="treasure-card-grid">
                    @foreach ($result['items'] as $item)
                        @include('campaigns.partials.reward-card', ['item' => $item])
                    @endforeach
                </div>
            </section>
        @endif
    </section>
@else
    <section class="paper-panel">
        Выбери режим и опасность, затем сгенерируй награду. Для “Сокровищницы” используется точный бросок к100 по таблицам сокровищ.
    </section>
@endif

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-treasure-generator-form]').forEach(function (form) {
            const mode = form.querySelector('[data-treasure-mode]');
            const itemOnlyFields = form.querySelectorAll('[data-items-only-field]');
            const note = form.querySelector('[data-table-mode-note]');

            const syncGeneratorFields = function () {
                const isItemsMode = mode?.value === 'items';

                itemOnlyFields.forEach(function (field) {
                    field.hidden = !isItemsMode;
                    field.querySelectorAll('select, input').forEach(function (input) {
                        input.disabled = !isItemsMode;
                    });
                });

                if (note) {
                    note.hidden = isItemsMode;
                }
            };

            mode?.addEventListener('change', syncGeneratorFields);
            syncGeneratorFields();
        });
    </script>
@endpush

@extends('layouts.app')

@section('title', $category['title'])

@section('content')

@php($view = $category['view'] ?? 'cards')

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
        <input id="category-search" name="q" value="{{ $query }}" placeholder="Название, свойство, эффект или слово из описания">
        <button class="paper-button" type="submit">Найти</button>
    </div>
</form>

<section class="data-section">
    <div class="data-section-title">
        <h2>{{ $query !== '' ? 'Найдено' : 'Все записи' }}</h2>
        <span>{{ count($entries) }}</span>
    </div>

    @if (count($entries) === 0)
        <div class="paper-panel">Записей не найдено.</div>
    @elseif ($view === 'weapons')
        <div class="data-table-wrap">
            <table class="data-index-table">
                <thead>
                    <tr>
                        <th>Оружие</th>
                        <th>Группа</th>
                        <th>Урон</th>
                        <th>Дальность</th>
                        <th>Свойства</th>
                        <th>Цена</th>
                        <th>Вес</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td>
                                <a href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">{{ $entry['name'] }}</a>
                                @if ($entry['english_name'])<small>{{ $entry['english_name'] }}</small>@endif
                            </td>
                            <td>{{ $entry['item_group'] ?: '—' }}</td>
                            <td><strong>{{ $entry['damage'] ?: '—' }}</strong>@if ($entry['damage_type'])<small>{{ $entry['damage_type'] }}</small>@endif</td>
                            <td>{{ $entry['range'] ?: '—' }}</td>
                            <td>
                                @forelse ($entry['properties'] as $property)
                                    <span class="data-mini-chip">{{ $property }}</span>
                                @empty
                                    —
                                @endforelse
                            </td>
                            <td>{{ $entry['cost'] ?: '—' }}</td>
                            <td>{{ $entry['weight'] ? $entry['weight'].' фнт.' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($view === 'armor')
        <div class="data-table-wrap">
            <table class="data-index-table">
                <thead>
                    <tr>
                        <th>Доспех</th>
                        <th>Группа</th>
                        <th>КД</th>
                        <th>Ловкость</th>
                        <th>Требование</th>
                        <th>Скрытность</th>
                        <th>Цена</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td>
                                <a href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">{{ $entry['name'] }}</a>
                                @if ($entry['english_name'])<small>{{ $entry['english_name'] }}</small>@endif
                            </td>
                            <td>{{ $entry['item_group'] ?: '—' }}</td>
                            <td><strong>{{ $entry['armor_class_formula'] ?: '—' }}</strong></td>
                            <td>{{ $entry['dexterity_bonus'] ?: '—' }}</td>
                            <td>{{ $entry['requirement'] ?: '—' }}</td>
                            <td>{{ is_null($entry['stealth_disadvantage']) ? '—' : ($entry['stealth_disadvantage'] ? 'Помеха' : 'Без помехи') }}</td>
                            <td>{{ $entry['cost'] ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif (in_array($view, ['equipment', 'mounts', 'services'], true))
        <div class="data-table-wrap">
            <table class="data-index-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Группа</th>
                        <th>Цена</th>
                        @if ($view === 'mounts')
                            <th>Скорость</th>
                            <th>Груз</th>
                        @else
                            <th>Единица</th>
                            <th>Вес</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td>
                                <a href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">{{ $entry['name'] }}</a>
                                @if ($entry['english_name'])<small>{{ $entry['english_name'] }}</small>@endif
                            </td>
                            <td>{{ $entry['item_group'] ?: '—' }}</td>
                            <td>{{ $entry['cost'] ?: ($entry['value_gp'] ? $entry['value_gp'].' зм' : '—') }}</td>
                            @if ($view === 'mounts')
                                <td>{{ $entry['speed_ft'] ? $entry['speed_ft'].' фт.' : '—' }}</td>
                                <td>{{ $entry['carrying_capacity_lb'] ? $entry['carrying_capacity_lb'].' фнт.' : '—' }}</td>
                            @else
                                <td>{{ $entry['unit'] ?: '—' }}</td>
                                <td>{{ $entry['weight'] ? $entry['weight'].' фнт.' : '—' }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($view === 'books')
        <div class="data-table-wrap">
            <table class="data-index-table">
                <thead>
                    <tr>
                        <th>Книга</th>
                        <th>Редкость</th>
                        <th>Изучение</th>
                        <th>Что даёт</th>
                        <th>Настройка</th>
                        <th>Цена</th>
                        <th>Вес</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td>
                                <a href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">{{ $entry['name'] }}</a>
                            </td>
                            <td>{{ $entry['rarity'] ?: '—' }}</td>
                            <td>{{ $entry['activation'] ?: '—' }}</td>
                            <td>{{ $entry['excerpt'] ?: '—' }}</td>
                            <td>{{ is_null($entry['attunement']) ? '—' : ($entry['attunement'] ? 'Требуется' : 'Не требуется') }}</td>
                            <td><strong>{{ $entry['cost'] ?: '—' }}</strong></td>
                            <td>{{ $entry['weight'] ? $entry['weight'].' фнт.' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($view === 'scrolls')
        <div class="data-table-wrap">
            <table class="data-index-table">
                <thead>
                    <tr>
                        <th>Свиток</th>
                        <th>Редкость</th>
                        <th>Использование</th>
                        <th>Дистанция / область</th>
                        <th>Спасбросок</th>
                        <th>Урон</th>
                        <th>Длительность</th>
                        <th>Цена</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td>
                                <a href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">{{ $entry['name'] }}</a>
                                @if ($entry['usable_by'])<small>{{ $entry['usable_by'] }}</small>@endif
                            </td>
                            <td>{{ $entry['rarity'] ?: '—' }}</td>
                            <td>{{ $entry['activation'] ?: '—' }}</td>
                            <td>
                                {{ $entry['range'] ?: '—' }}
                                @if ($entry['area'])<small>{{ $entry['area'] }}</small>@endif
                            </td>
                            <td>{{ $entry['saving_throw'] ?: '—' }}</td>
                            <td>
                                <strong>{{ $entry['damage'] ?: '—' }}</strong>
                                @if ($entry['damage_type'])<small>{{ $entry['damage_type'] }}</small>@endif
                            </td>
                            <td>{{ $entry['duration'] ?: '—' }}</td>
                            <td><strong>{{ $entry['cost'] ?: '—' }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($view === 'treasure_table')
        <div class="data-table-wrap">
            <table class="data-index-table data-treasure-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Вид</th>
                        <th>Цена</th>
                        <th>Единица / материал</th>
                        <th>Заметка</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td>
                                <a href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">{{ $entry['name'] }}</a>
                            </td>
                            <td>{{ $entry['item_group'] ?: $entry['type'] ?: '—' }}</td>
                            <td><strong>{{ $entry['value_gp'] ? $entry['value_gp'].' зм' : ($entry['cost'] ?: '—') }}</strong></td>
                            <td>
                                {{ $entry['unit'] ?: '—' }}
                                @foreach ($entry['sections'] as $section)
                                    @if (in_array($section['key'], ['material', 'condition', 'subject'], true))
                                        <small>{{ $section['title'] }}: {{ $section['text'] }}</small>
                                    @endif
                                @endforeach
                            </td>
                            <td>{{ $entry['excerpt'] ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($view === 'spells')
        <div class="data-table-wrap">
            <table class="data-index-table">
                <thead>
                    <tr>
                        <th>Заклинание</th>
                        <th>Уровень</th>
                        <th>Школа</th>
                        <th>Дистанция</th>
                        <th>Время</th>
                        <th>Классы</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td><a href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">{{ $entry['name'] }}</a></td>
                            <td>{{ collect($entry['stats'])->firstWhere('label', 'Уровень')['value'] ?? '—' }}</td>
                            <td>{{ collect($entry['stats'])->firstWhere('label', 'Школа')['value'] ?? '—' }}</td>
                            <td>{{ $entry['range'] ?: '—' }}</td>
                            <td>{{ collect($entry['stats'])->firstWhere('label', 'Время накладывания')['value'] ?? '—' }}</td>
                            <td>{{ Str::limit(collect($entry['sections'])->firstWhere('key', 'classes')['text'] ?? '—', 80) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif (in_array($view, ['cr', 'xp', 'skills'], true))
        <div class="data-table-wrap">
            <table class="data-index-table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Данные</th>
                        <th>Описание</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        <tr>
                            <td><a href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">{{ $entry['name'] }}</a></td>
                            <td>
                                @foreach ($entry['stats'] as $stat)
                                    <span class="data-mini-chip">{{ $stat['label'] }}: {{ $stat['value'] }}</span>
                                @endforeach
                            </td>
                            <td>{{ $entry['excerpt'] ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif ($view === 'monsters')
        <div class="data-card-grid monster-index-grid">
            @foreach ($entries as $entry)
                <a class="data-monster-card" href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">
                    <span>{{ trim($entry['size'].' '.$entry['type']) ?: 'Существо' }}</span>
                    <strong>{{ $entry['name'] }}</strong>
                    <div>
                        <b>КД {{ $entry['armor_class'] ?: '—' }}</b>
                        <b>ХП {{ $entry['hit_points'] ?: '—' }}</b>
                        <b>ПО {{ $entry['challenge_rating'] ?: '—' }}</b>
                    </div>
                    <p>{{ $entry['speed'] ?: 'Скорость не указана' }}</p>
                </a>
            @endforeach
        </div>
    @else
        <div class="data-card-grid">
            @foreach ($entries as $entry)
                <a class="{{ in_array($view, ['effects', 'treasure'], true) ? 'data-effect-card' : 'data-rule-card' }}" href="{{ route('data.entity', [$entry['category_slug'], $entry['index']]) }}">
                    <span>{{ $entry['type'] ?: $entry['category_title'] }}</span>
                    <strong>{{ $entry['name'] }}</strong>
                    @if ($entry['english_name'])<small>{{ $entry['english_name'] }}</small>@endif
                    <div>
                        @foreach (array_slice($entry['stats'], 0, 4) as $stat)
                            <b>{{ $stat['label'] }}: {{ $stat['value'] }}</b>
                        @endforeach
                    </div>
                    <p>{{ $entry['excerpt'] ?: 'Открой карточку, чтобы увидеть подробности.' }}</p>
                    @if ($entry['suggested_price'])<em>{{ $entry['suggested_price'] }}</em>@endif
                </a>
            @endforeach
        </div>
    @endif
</section>

@endsection

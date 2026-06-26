@extends('layouts.app')

@section('title', 'Создать персонажа')

@section('content')

<div class="page-heading character-heading">
    <div>
        <p class="eyebrow">Новый лист персонажа</p>
        <h1>Создание персонажа</h1>
        <p>Заполни героя как настоящий настольный лист: характеристики, боевые параметры, навыки и историю.</p>
    </div>
</div>

@include('characters.partials.form', [
    'action' => route('characters.store'),
    'method' => 'POST',
    'character' => null,
])

@endsection

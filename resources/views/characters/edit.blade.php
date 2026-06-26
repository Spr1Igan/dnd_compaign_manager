@extends('layouts.app')

@section('title', 'Редактировать персонажа')

@section('content')

<div class="page-heading character-heading">
    <div>
        <p class="eyebrow">Правка листа</p>
        <h1>Редактирование персонажа</h1>
        <p>{{ $character->name }}</p>
    </div>
</div>

@include('characters.partials.form', [
    'action' => route('characters.update', $character),
    'method' => 'PUT',
    'character' => $character,
])

@endsection

@extends('layouts.app')

@section('title', __('ui.edit_page.title'))

@section('content')

<div class="page-heading character-heading">
    <div>
        <p class="eyebrow">{{ __('ui.edit_page.eyebrow') }}</p>
        <h1>{{ __('ui.edit_page.heading') }}</h1>
        <p>{{ $character->name }}</p>
    </div>
</div>

@include('characters.partials.form', [
    'action' => route('characters.update', $character),
    'method' => 'PUT',
    'character' => $character,
])

@endsection

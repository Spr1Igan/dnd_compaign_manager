@extends('layouts.app')

@section('title', __('ui.create_page.title'))

@section('content')

<div class="page-heading character-heading">
    <div>
        <p class="eyebrow">{{ __('ui.create_page.eyebrow') }}</p>
        <h1>{{ __('ui.create_page.heading') }}</h1>
        <p>{{ __('ui.create_page.lead') }}</p>
    </div>
</div>

@include('characters.partials.form', [
    'action' => route('characters.store'),
    'method' => 'POST',
    'character' => null,
])

@endsection

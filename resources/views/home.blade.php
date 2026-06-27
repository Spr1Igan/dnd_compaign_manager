@extends('layouts.app')

@section('title', __('ui.home.title'))

@section('content')

<section class="home-hero">

    <h1>D&D Campaign Manager</h1>

    <p>
        {{ __('ui.home.lead') }}
    </p>

</section>

<section class="home-cards">

    <a href="{{ route('characters.index') }}" class="paper-card">
        <h2>{{ __('ui.characters') }}</h2>
        <p>{{ __('ui.home.characters_text') }}</p>
        <span>{{ __('ui.open') }} →</span>
    </a>

    <a href="#" class="paper-card">
        <h2>{{ __('ui.campaigns') }}</h2>
        <p>{{ __('ui.home.campaigns_text') }}</p>
        <span>{{ __('ui.open') }} →</span>
    </a>

</section>

@endsection

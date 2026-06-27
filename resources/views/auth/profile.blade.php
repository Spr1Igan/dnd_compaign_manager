@extends('layouts.app')

@section('title', __('ui.profile'))

@section('content')

<section class="paper-panel">
    <h1>{{ __('ui.profile') }}</h1>

    <p><b>ID:</b> {{ auth()->user()->id }}</p>
    <p><b>{{ __('ui.profile_page.name') }}:</b> {{ auth()->user()->name }}</p>
    <p><b>{{ __('ui.profile_page.login') }}:</b> {{ auth()->user()->login }}</p>

    <a class="paper-button" href="{{ route('home') }}">{{ __('ui.profile_page.home') }}</a>
</section>

@endsection

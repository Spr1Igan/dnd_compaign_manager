<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::post('/locale', function (Request $request) {
    $data = $request->validate([
        'locale' => ['required', 'in:ru,en'],
    ]);

    session(['locale' => $data['locale']]);

    return back();
})->name('locale.update');

require __DIR__ . '/auth.php';
require __DIR__ . '/profile.php';
require __DIR__ . '/characters.php';
require __DIR__ . '/campaigns.php';
require __DIR__ . '/data.php';

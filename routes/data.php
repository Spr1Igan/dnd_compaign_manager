<?php

use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('data')->name('data.')->group(function () {
    Route::get('/', [DataController::class, 'index'])->name('index');
    Route::get('/chapters', [DataController::class, 'chapters'])->name('chapters');
    Route::get('/chapters/{chapter}', [DataController::class, 'chapter'])->name('chapter');
    Route::get('/tables', [DataController::class, 'tables'])->name('tables');
    Route::get('/entities/{category}', [DataController::class, 'category'])->name('category');
    Route::get('/entities/{category}/{entry}', [DataController::class, 'entity'])->whereNumber('entry')->name('entity');
});

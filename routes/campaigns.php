<?php

use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('campaigns')->name('campaigns.')->group(function (): void {
    Route::get('/', [CampaignController::class, 'index'])->name('index');
});

<?php

use App\Http\Controllers\CompetitionController;
use Illuminate\Support\Facades\Route;

// Route::get('/validate-competitions', [CompetitionController::class, 'validateCompetitions']);
Route::get('/', function () {
    return view('landing.landing');
});
// Route::post('/cari-lomba', [LombaController::class, 'cariLomba']);
// Route::get('/', [App\Http\Controllers\LandingPageController::class, 'index'])->name('landing');

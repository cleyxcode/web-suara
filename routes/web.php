<?php

use App\Http\Controllers\VotingController;

// // Route untuk voting (tanpa auth)
// Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');
// Route::post('/voting', [VotingController::class, 'store'])->name('voting.store');


// Route voting (sudah ada)
Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');
Route::post('/voting', [VotingController::class, 'store'])
    ->name('voting.store')
    ->middleware('throttle:10,1');
    // Route statistik publik (NEW)
Route::get('/hasil', [PublicStatisticsController::class, 'index'])->name('public-statistics.index');




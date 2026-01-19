<?php

use App\Http\Controllers\VotingController;

// Route untuk voting (tanpa auth)
Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');
Route::post('/voting', [VotingController::class, 'store'])->name('voting.store');

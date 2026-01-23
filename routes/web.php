<?php

use App\Http\Controllers\PublicStatisticsController;
use App\Http\Controllers\VotingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route voting dengan Livewire (tanpa auth)
Route::get('/voting', [VotingController::class, 'index'])->name('voting.index');

// Route statistik publik
Route::get('/hasil', [PublicStatisticsController::class, 'index'])->name('public-statistics.index');

// Route legacy POST untuk voting (optional - bisa dihapus jika full Livewire)
// Route::post('/voting', [VotingController::class, 'store'])
//     ->name('voting.store')
//     ->middleware('throttle:10,1');
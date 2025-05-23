<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeaderboardController;

 
Route::get('/', [LeaderboardController::class, 'index'])->name('leaderboard.index');
Route::post('/leaderboard/recalculate', [LeaderboardController::class, 'recalculate'])->name('leaderboard.recalculate');
Route::post('/activity/dummy', [ActivityController::class, 'insertDummyData'])->name('activity.dummy');

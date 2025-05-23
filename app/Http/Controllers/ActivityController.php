<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    // insert activities in 5 random user  
    public function insertDummyData(Request $request)
    {

        $users = User::all(); 
        foreach ($users->random(5) as $user) {
            Activity::create([
                'user_id' => $user->id,
                'performed_at' => now()->subDays(rand(0, 30))->setTime(rand(6, 20), rand(0, 59)), // Random past 30 days
                'points' => 20,
            ]);
        }
        app(LeaderboardController::class)->recalculate(new Request(['filter' => 'day']));
        app(LeaderboardController::class)->recalculate(new Request(['filter' => 'month']));
        app(LeaderboardController::class)->recalculate(new Request(['filter' => 'year']));
 
        return redirect()->route('leaderboard.index', ['filter' => 'year'])
            ->with('success', 'Dummy activity data inserted and leaderboard recalculated!');
    }
}

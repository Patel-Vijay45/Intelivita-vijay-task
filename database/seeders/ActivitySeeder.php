<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Each user will have 5–15 activities randomly
            $activitiesCount = rand(5, 15);

            for ($i = 0; $i < $activitiesCount; $i++) {
                Activity::create([
                    'user_id' => $user->id,
                    'performed_at' => now()->subDays(rand(0, 30))->setTime(rand(6, 20), rand(0, 59)), // Random past 30 days
                    'points' => 20,
                ]);
            }
        }
        // ✅ Call the leaderboard recalculation command after seeding
        // Artisan::call('recalculate:leaderboard');
    }
}

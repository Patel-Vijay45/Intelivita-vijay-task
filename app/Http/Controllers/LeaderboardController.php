<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\Leaderboard; 
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'day');
        $searchUserId = $request->input('search');

        $referenceDate = match ($filter) {
            'day' => today()->toDateString(),
            'month' => now()->format('Y-m'),
            'year' => now()->format('Y'),
            default => today()->toDateString(),
        };

        $query = Leaderboard::with('user')
            ->where('filter_type', $filter)
            ->where('reference_date', $referenceDate);
        // dd($referenceDate);
        if ($searchUserId) {
            $query->orderByRaw('user_id = ? DESC', [$searchUserId]);
        }

        $query->orderBy('rank');

        $leaderboard = $query->get();

        return view('leaderboard.index', compact('leaderboard', 'filter', 'searchUserId'));
    }

    public function recalculate(Request $request)
    {
        $filter = $request->input('filter', 'day');

        switch ($filter) {
            case 'day':
                $referenceDate = today()->toDateString();
                $startDate = today()->startOfDay();
                $endDate = today()->endOfDay();
                break;
            case 'month':
                $referenceDate = now()->format('Y-m');
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;
            case 'year':
                $referenceDate = now()->year;
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            default:
                $referenceDate = today()->toDateString();
                $startDate = today()->startOfDay();
                $endDate = today()->endOfDay();
        }

        // Delete existing leaderboard by  filter and referenceDate
        Leaderboard::where('filter_type', $filter)
            ->where('reference_date', $referenceDate)
            ->delete();

        // Fetch total points per user for the filter
        $query = Activity::select('user_id', DB::raw('SUM(points) as total_points'))
            ->whereBetween('performed_at', [$startDate, $endDate])
            ->groupBy('user_id')
            ->orderByDesc('total_points');
        // dd($query, $startDate, $endDate, $referenceDate);
        $usersPoints = $query->get();

        $leaderboardData = [];
        $rank = 0;
        $prevPoints = null;
     
        foreach ($usersPoints as $index => $user) {
            if ($prevPoints === null || $user->total_points < $prevPoints) {
                $rank++;
            }
            $prevPoints = $user->total_points;

            $leaderboardData[] = [
                'user_id' => $user->user_id,
                'filter_type' => $filter,
                'reference_date' => $referenceDate,
                'total_points' => $user->total_points,
                'rank' => $rank,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert new leaderboard data in bulk
        if (!empty($leaderboardData)) {
            Leaderboard::insert($leaderboardData);
        }

        return redirect()->route('leaderboard.index', ['filter' => $filter])
            ->with('success', 'Leaderboard recalculated successfully!');
    }
}

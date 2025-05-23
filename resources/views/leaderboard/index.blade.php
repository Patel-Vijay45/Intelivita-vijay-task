@extends('layouts.app')

@section('content')
<div class="container">


    <h2>Leaderboard - Filter: {{ ucfirst($filter) }}</h2>
    <div style="display: flex;flex-wrap: wrap;">
        <div style="margin-right: 20px;">
            <form method="GET" action="{{ route('leaderboard.index') }}" class="mb-3 d-flex gap-2 align-items-center">
                <select name="filter" onchange="this.form.submit()" class="form-select w-auto">
                    <option value="day" @if($filter=='day' ) selected @endif>Day</option>
                    <option value="month" @if($filter=='month' ) selected @endif>Month</option>
                    <option value="year" @if($filter=='year' ) selected @endif>Year</option>
                </select>

                <input type="text" name="search" value="{{ $searchUserId }}" placeholder="Search by User ID" class="form-control w-auto" />

                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            <!-- @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif -->

        </div>
        <div>

            <form action="{{ route('activity.dummy') }}" method="POST" style="display:inline-block;">
                @csrf
                <button type="submit" class="btn btn-primary">Add Dummy Activity</button>
            </form>
        </div>
    </div>
    <form method="POST" action="{{ route('leaderboard.recalculate') }}">
        @csrf
        <input type="hidden" name="filter" value="{{ $filter }}" />
        <button type="submit" class="btn btn-success mb-3">Recalculate</button>
    </form>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Rank</th>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Total Points</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaderboard as $entry)
            <tr @if($entry->user_id == $searchUserId) style="background-color: #ffffcc;" @endif>
                <td>#{{ $entry->rank }}</td>
                <td>{{ $entry->user_id }}</td>
                <td>{{ $entry->user->full_name }}</td>
                <td>{{ $entry->total_points }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No leaderboard data found. Please recalculate.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
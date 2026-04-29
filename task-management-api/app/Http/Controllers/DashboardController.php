<?php

namespace App\Http\Controllers;

use App\Models\TeamJoinRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
   public function latestJoinRequests(Request $request)
    {
        $user = $request->user();

        // IDs الفرق اللي هو Admin فيها
        $adminTeamIds = $user->adminTeams()->pluck('teams.id');

        // آخر 3 طلبات انضمام
        $joinRequests = TeamJoinRequest::with(['user:id,name,avatar', 'team:id,name'])
            ->whereIn('team_id', $adminTeamIds)
            ->where('status', 'pending')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($request) {
                $request->created_at_human = $request->created_at->diffForHumans();
                return $request;
            });

        return response()->json([
            'data' => $joinRequests
        ]
        );
    }
}

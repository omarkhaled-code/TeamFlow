<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class PlanController extends Controller
{


    public function index(Request $request)
    {
        $plans = Plan::all();
        $currentPlan = $request->user()->plan;

        return response()->json([
            'plans' => $plans,
            'current_plan' => $currentPlan,
        ]);
    }

    public function checkCreateTeam(Request $request)
    {
        $user = $request->user();
        $allowed = $user->adminTeams()->count() < $user->plan->max_created_teams;
        // $allowed = $user->canCreateTeam();
        return response()->json([
            'allowed' => $allowed
        ]);
    }

    public function checkJoinTeam(Request $request)
    {
        $user = $request->user();
        $allowed = $user->joinedTeams()->count() < $user->plan->max_joined_teams;
        // $allowed = $user->canJoinTeam();
        
        return response()->json([
            'allowed' => $allowed
        ]);
    }


    public function upgradePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        $user = $request->user();

        $plan = Plan::findOrFail($request->plan_id);

        // تحديث الخطة
        $user->update([
            'plan_id' => $plan->id,
        ]);

        return response()->json([
            'message' => 'تم تحديث الخطة بنجاح',
            'plan' => $user->fresh()->plan
        ]);
    }
    public function checkTeamChat(Request $request){
        $validated = $request->validate([
            'team_id' => 'required',
        ]);
        $team = Team::findOrFail($validated['team_id']);
        $user = User::findOrFail($team->owner_id);
        $allowed = ($user->plan_id === 2);

         return response()->json([
            'allowed' => $allowed
        ]);
        
    }

}

<?php

namespace App\Http\Controllers\User;

use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\StoreTeam;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TeamController extends BaseController
{
    private $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    public function index(Request $request){
        $orgId = session()->get('current_organization');

        if($request->expectsJson()){
            $rows = DB::table('users')
                ->join('teams', 'users.id', '=', 'teams.user_id')
                ->where('teams.organization_id', '=', $orgId)
                ->select('users.*')
                ->get();

            return response()->json([
                'rows' => $rows
            ]);
        } else {
            $query = Team::with('user')
                ->where('organization_id', $orgId);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($request->filled('role') && $request->role !== 'all') {
                $query->where('role', $request->role);
            }

            $rows = TeamResource::collection(
                $query->latest()->paginate(10)->withQueryString()
            );

            $counts = [
                'total' => Team::where('organization_id', $orgId)->count(),
                'owner' => Team::where('organization_id', $orgId)->where('role', 'owner')->count(),
                'manager' => Team::where('organization_id', $orgId)->where('role', 'manager')->count(),
                'agent' => Team::where('organization_id', $orgId)->where('role', 'agent')->count(),
            ];

            return Inertia::render('User/Team/Index', [
                'title' => __('Team Management'),
                'filters' => $request->all(),
                'rows' => $rows,
                'counts' => $counts,
            ]);
        }
    }

    public function invite(StoreTeam $request){
        $this->teamService->invite($request);

        //response()->json(['success' => true, 'message'=> __('User invited successfully!'), 'data' => $invite])

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('User invited successfully!')
            ]
        );
    }

    public function update(Request $request, $uuid){
        $this->teamService->update($request, $uuid);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('User account updated successfully!')
            ]
        );
    }

    public function delete($uuid)
    {
        $this->teamService->destroy($uuid);
    }
}
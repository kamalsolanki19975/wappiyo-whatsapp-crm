<?php

namespace App\Http\Controllers\User;

use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Models\Organization;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrganizationController extends BaseController
{
    public function index()
    {
        $data['organizations'] = Team::with('organization')->where('user_id', auth()->user()->id)->get();

        return Inertia::render('User/OrganizationSelect', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string',
        ]);

        $organization = Organization::where('uuid', $request->uuid)->first();

        if ($organization) {
            $user = auth()->user();
            $isMember = Team::where('user_id', $user->id)
                ->where('organization_id', $organization->id)
                ->exists();

            if (!$isMember && $user->role !== 'admin') {
                abort(403, 'Unauthorized access to organization.');
            }

            session()->put('current_organization', $organization->id);
        }

        return to_route('dashboard');
    }
}
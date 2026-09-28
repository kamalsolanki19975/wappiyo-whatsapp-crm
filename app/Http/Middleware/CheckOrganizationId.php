<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use App\Helpers\SubscriptionHelper;
use Illuminate\Support\Facades\Auth;

class CheckOrganizationId
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('current_organization')) {
            return redirect()->route('user.organization.index');
        }

        $orgId = session()->get('current_organization');
        $user = Auth::user();
        if ($user && $user->role !== 'admin') {
            $isMember = \App\Models\Team::where('user_id', $user->id)
                ->where('organization_id', $orgId)
                ->exists();
            if (!$isMember) {
                session()->forget('current_organization');
                return redirect()->route('user.organization.index');
            }
        }

        return $next($request);
    }
}

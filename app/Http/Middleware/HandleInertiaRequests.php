<?php

namespace App\Http\Middleware;

use App\Helpers\DateTimeHelper;
use App\Models\Chat;
use App\Models\Language;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Middleware;


class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $organization = array();
        $organizations = array();
        $user = $request->user();
        $language = session('locale') ?? 'en';
        $unreadMessages = 0;

        if ($user) {
            $organizationId = session('current_organization');

            // Auto-fallback to user's first active organization if session key is not set
            if (!$organizationId && $user->role === 'user') {
                $firstTeam = Team::where('user_id', $user->id)->whereHas('organization')->first();
                if ($firstTeam) {
                    $organizationId = $firstTeam->organization_id;
                    session()->put('current_organization', $organizationId);
                }
            }

            if ($organizationId) {
                $user->load(['teams' => function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                }]);

                $organization = Organization::where('id', $organizationId)->first();
                $unreadMessages = Chat::where('organization_id', $organizationId)
                    ->where('type', 'inbound')
                    ->where('deleted_at', NULL)
                    ->where('is_read', 0)
                    ->count();
            }

            if ($user->role === 'user') {
                // Ensure only teams with active (non-deleted) organizations are passed
                $organizations = Team::with('organization')->where('user_id', $user->id)->whereHas('organization')->get();
            } else {
                // If admin is impersonating/testing, provide active organizations list
                $organizations = Organization::latest()->take(20)->get()->map(function ($org) {
                    return (object) [
                        'role' => 'admin',
                        'organization' => $org,
                    ];
                });
            }
        }

        if($this->isInstalled()){
            $keys = ['favicon', 'logo', 'company_name', 'address', 'currency' , 'email', 'phone', 'socials', 'trial_period', 'recaptcha_site_key', 'recaptcha_active', 'google_analytics_tracking_id', 'google_maps_api_key','pusher_app_key','pusher_app_cluster'];
            $config = Setting::whereIn('key', $keys)->get();
            $languages = Language::where('deleted_at', null)->where('status', 'active')->get();
        } else {
            $config = array();
            $languages = array();
        }

        return array_merge(parent::share($request), [
            'csrf_token' => csrf_token(),
            'applicationVersion' => fn () => Config::get('version.version'),
            'applicationReleaseDate' => fn () => Config::get('version.release_date'),
            'config' => $config,
            'auth' => [
                'user' => $user ?: null,
            ],
            'organization' => $organization,
            'organizations' => $organizations,
            'flash' => [
                'status'=> session('status')
            ],
            'response_data' => fn () => $request->session()->get('response_data'),
            'languages' => $languages,
            'unreadMessages' => $unreadMessages,
            'currentLanguage' => $language,
            'timezone' => fn () => DateTimeHelper::getOrganizationTimezone($organization ?: null),
            'timezone_display' => fn () => DateTimeHelper::getTimezoneDisplay(DateTimeHelper::getOrganizationTimezone($organization ?: null)),
        ]);
    }

    /**
     * Checks if the application has been installed.
     *
     * @return bool
     */
    public function isInstalled(): bool
    {
        return file_exists(storage_path('installed'));
    }
}

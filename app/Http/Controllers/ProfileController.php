<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\StoreProfile;
use App\Http\Requests\StoreProfilePassword;
use App\Http\Requests\StoreProfileAddress;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Hash;
use Redirect;

class ProfileController extends BaseController
{
    public function update(StoreProfile $request)
    {
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;

        $response = User::where('id', auth()->user()->id)->update([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
        ]);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Profile updated successfully!')
            ]
        );
    }

    public function updatePassword(StoreProfilePassword $request)
    {
        $old_password = $request->old_password;
        $password = Hash::make($request->password);

        $response = User::where('id', auth()->user()->id)->update([
            'password' => $password,
        ]);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Profile updated successfully!')
            ]
        );
    }

    public function updateOrganization(StoreProfileAddress $request)
    {
        $organizationId = session('current_organization');
        if (! $organizationId) {
            abort(403, 'No active organization session.');
        }

        $user = auth()->user();
        if ($user->role !== 'admin') {
            $teamMember = \App\Models\Team::where('user_id', $user->id)
                ->where('organization_id', $organizationId)
                ->first();

            if (! $teamMember || ! in_array($teamMember->role, ['owner', 'manager'])) {
                abort(403, 'Unauthorized. Only organization owners and managers can update organization settings.');
            }
        }

        $organizationConfig = Organization::where('id', $organizationId)->firstOrFail();
        $metadataArray = $organizationConfig->metadata ? json_decode($organizationConfig->metadata, true) : [];

        $metadataArray['notifications']['enable_sound'] = $request->input('enable_sound_notification');
        $metadataArray['notifications']['tone'] = $request->input('tone');
        $timezoneInput = $request->input('timezone');
        $validTz = \App\Helpers\DateTimeHelper::isValidTimezone($timezoneInput)
            ? $timezoneInput
            : ($organizationConfig->timezone ?: \App\Helpers\DateTimeHelper::DEFAULT_TIMEZONE);

        $metadataArray['timezone'] = $validTz;
        $organizationConfig->timezone = $validTz;

        $addressArray['street'] = $request->input('address');
        $addressArray['city'] = $request->input('city');
        $addressArray['state'] = $request->input('state');
        $addressArray['zip'] = $request->input('zip');
        $addressArray['country'] = $request->input('country');

        $organizationConfig->name = $request->input('organization_name');
        $organizationConfig->address = json_encode($addressArray);
        $organizationConfig->metadata = json_encode($metadataArray);

        if($organizationConfig->save()){
            \App\Helpers\DateTimeHelper::clearCache();
            return Redirect::back()->with(
                'status', [
                    'type' => 'success', 
                    'message' => __('Organization updated successfully!')
                ]
            );
        } else {
            return Redirect::back()->with(
                'status', [
                    'type' => 'error', 
                    'message' => __('Something went wrong. Refresh the page and try again')
                ]
            );
        }
    }
}
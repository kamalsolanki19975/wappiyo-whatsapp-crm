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
        $user = auth()->user();
        $data = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
        ];

        if ($request->has('phone')) {
            $data['phone'] = $request->input('phone');
        }

        // Email is strictly IMMUTABLE through normal profile settings (Requirement 19)
        // If an email change is needed in future, it must be a separate verified workflow.
        // We explicitly do NOT include 'email' in the update payload.

        // Handle avatar upload if included in the request
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            // Remove old avatar if exists
            if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Profile updated successfully!')
            ]
        );
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = auth()->user();

        // Remove old avatar if exists
        if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success' => true,
            'message' => __('Profile picture updated successfully!'),
            'avatar_url' => asset('storage/' . $path),
        ]);
    }

    public function deleteAvatar(Request $request)
    {
        $user = auth()->user();

        if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return response()->json([
            'success' => true,
            'message' => __('Profile picture removed successfully!'),
        ]);
    }

    public function sendResetPasswordLink(Request $request)
    {
        $user = auth()->user();

        try {
            (new \App\Services\PasswordResetService)->generateResetLink($user->email);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => __('We will send a password reset confirmation to your registered email address.')
                ]);
            }

            return Redirect::back()->with(
                'status', [
                    'type' => 'success',
                    'message' => __('We will send a password reset confirmation to your registered email address.')
                ]
            );
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Failed to send password reset email. Please try again.')
                ], 500);
            }

            return Redirect::back()->with(
                'status', [
                    'type' => 'error',
                    'message' => __('Failed to send password reset email. Please try again.')
                ]
            );
        }
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
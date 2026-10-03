<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileSecurityTest extends TestCase
{
    protected function createWorkspaceForUser(User $user): Organization
    {
        $org = Organization::create([
            'identifier' => 'prof-org-' . Str::random(6),
            'name' => 'Profile Workspace',
            'created_by' => $user->id,
        ]);

        Team::create([
            'user_id' => $user->id,
            'organization_id' => $org->id,
            'role' => 'owner',
            'created_by' => $user->id,
        ]);

        return $org;
    }

    public function test_email_is_strictly_immutable_on_profile_update(): void
    {
        $originalEmail = 'immutable.' . Str::random(8) . '@wappiyo.com';
        $user = User::create([
            'first_name' => 'Original',
            'last_name' => 'Name',
            'email' => $originalEmail,
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $org = $this->createWorkspaceForUser($user);
        $this->actingAs($user, 'user');
        session(['current_organization' => $org->id]);

        // Attempt to change email via profile update payload
        $response = $this->put('/profile', [
            'first_name' => 'UpdatedFirst',
            'last_name' => 'UpdatedLast',
            'email' => 'attacker.hijack@wappiyo.com',
            'phone' => '+1234567890',
        ]);

        $user->refresh();

        // Name updated
        $this->assertEquals('UpdatedFirst', $user->first_name);
        $this->assertEquals('UpdatedLast', $user->last_name);

        // Crucial security check: Email was NOT changed
        $this->assertEquals($originalEmail, $user->email);
        $this->assertNotEquals('attacker.hijack@wappiyo.com', $user->email);
    }

    public function test_profile_avatar_upload_and_removal(): void
    {
        Storage::fake('public');

        $user = User::create([
            'first_name' => 'Avatar',
            'last_name' => 'Tester',
            'email' => 'avatar.' . Str::random(8) . '@wappiyo.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $org = $this->createWorkspaceForUser($user);
        $this->actingAs($user, 'user');
        session(['current_organization' => $org->id]);

        // 1. Upload valid image
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);
        $response = $this->postJson('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $user->refresh();
        $this->assertNotNull($user->avatar);
        Storage::disk('public')->assertExists($user->avatar);

        // 2. Reject non-image upload
        $badFile = UploadedFile::fake()->create('malicious.php', 10, 'application/x-php');
        $badResponse = $this->postJson('/profile/avatar', [
            'avatar' => $badFile,
        ]);
        $badResponse->assertStatus(422);

        // 3. Remove avatar
        $deleteResponse = $this->deleteJson('/profile/avatar');
        $deleteResponse->assertStatus(200);

        $user->refresh();
        $this->assertNull($user->avatar);
    }

    public function test_password_reset_dispatch_from_profile_settings(): void
    {
        $user = User::create([
            'first_name' => 'Reset',
            'last_name' => 'User',
            'email' => 'reset.' . Str::random(8) . '@wappiyo.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $org = $this->createWorkspaceForUser($user);
        $this->actingAs($user, 'user');
        session(['current_organization' => $org->id]);

        $response = $this->postJson('/profile/reset-password');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }
}

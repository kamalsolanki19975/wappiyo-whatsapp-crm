<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class SignupOtpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_duplicate_email_validation_case_insensitive(): void
    {
        $existingEmail = 'unique.test.' . Str::random(8) . '@wappiyo.com';

        User::create([
            'first_name' => 'Existing',
            'last_name' => 'User',
            'email' => strtolower($existingEmail),
            'password' => Hash::make('Password@1234'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Attempt signup with uppercase variations and leading/trailing whitespace
        $variations = [
            strtoupper($existingEmail),
            ucfirst($existingEmail),
            ' ' . $existingEmail . ' ',
        ];

        foreach ($variations as $emailAttempt) {
            $response = $this->postJson('/send-otp', [
                'first_name' => 'Test',
                'last_name' => 'Duplicate',
                'organization_name' => 'Duplicate Org',
                'email' => $emailAttempt,
                'password' => 'Password@1234',
                'password_confirmation' => 'Password@1234',
            ]);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['email']);
            $errors = $response->json('errors.email');
            $this->assertStringContainsString('An account with this email already exists', $errors[0]);
        }
    }

    public function test_valid_signup_creates_unverified_user_and_stores_otp(): void
    {
        $newEmail = 'newuser.' . Str::random(8) . '@wappiyo.com';

        $response = $this->postJson('/send-otp', [
            'first_name' => 'New',
            'last_name' => 'Member',
            'organization_name' => 'Member Workspace',
            'email' => $newEmail,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'step' => 'verify_otp',
        ]);

        // User should not be created in DB until OTP verification is successful
        $user = User::where('email', strtolower($newEmail))->first();
        $this->assertNull($user);

        // Check OTP was generated for this email
        $otpRecord = Otp::where('email', strtolower($newEmail))->latest()->first();
        $this->assertNotNull($otpRecord);
        $this->assertGreaterThanOrEqual(6, strlen($otpRecord->otp));
        $this->assertFalse($otpRecord->isExpired());
    }

    public function test_wrong_otp_is_rejected(): void
    {
        $email = 'wrongotp.' . Str::random(8) . '@wappiyo.com';

        $this->postJson('/send-otp', [
            'first_name' => 'Test',
            'last_name' => 'WrongOtp',
            'organization_name' => 'Wrong OTP Org',
            'email' => $email,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response = $this->postJson('/verify-otp', [
            'email' => $email,
            'otp' => '000000',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    public function test_valid_otp_verifies_email_and_creates_login_notification(): void
    {
        $email = 'verify.' . Str::random(8) . '@wappiyo.com';

        $this->postJson('/send-otp', [
            'first_name' => 'Verified',
            'last_name' => 'User',
            'organization_name' => 'Verified Org',
            'email' => $email,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $otpRecord = Otp::where('email', strtolower($email))->latest()->first();
        $this->assertNotNull($otpRecord);

        // Set known OTP hash to simulate email delivery
        $otpRecord->update(['otp' => Hash::make('654321')]);

        $response = $this->postJson('/verify-otp', [
            'email' => $email,
            'otp' => '654321',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verify database state
        $user = User::where('email', strtolower($email))->first();
        $this->assertNotNull($user->email_verified_at);

        // Verify login notification was created (Requirement 25)
        $notification = Notification::where('user_id', $user->id)
            ->where('title', 'like', '%Login%')
            ->first();
        $this->assertNotNull($notification);
    }

    public function test_otp_resend_cooldown(): void
    {
        $email = 'cooldown.' . Str::random(8) . '@wappiyo.com';

        $this->postJson('/send-otp', [
            'first_name' => 'Cooldown',
            'last_name' => 'Test',
            'organization_name' => 'Cooldown Org',
            'email' => $email,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        // Immediate resend should be rate-limited by 60s cooldown
        $response = $this->postJson('/resend-otp', [
            'email' => $email,
        ]);

        $response->assertStatus(429);
        $response->assertJson([
            'success' => false,
        ]);
    }
}

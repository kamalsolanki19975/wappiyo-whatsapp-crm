<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminRenewalNotificationTest extends TestCase
{
    protected User $adminUser;
    protected User $regularUser;
    protected Organization $org;
    protected SubscriptionPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Super',
            'email' => 'admin.test.' . Str::random(8) . '@wappiyo.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->regularUser = User::create([
            'first_name' => 'Client',
            'last_name' => 'Member',
            'email' => 'client.test.' . Str::random(8) . '@wappiyo.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->org = Organization::create([
            'identifier' => 'test-client-' . Str::random(6),
            'name' => 'Renewal Test Org',
            'created_by' => $this->regularUser->id,
        ]);

        Team::create([
            'user_id' => $this->regularUser->id,
            'organization_id' => $this->org->id,
            'role' => 'owner',
            'created_by' => $this->regularUser->id,
        ]);

        $this->plan = SubscriptionPlan::first() ?? SubscriptionPlan::create([
            'name' => 'Pro Plan',
            'price' => 49.00,
            'billing_period' => 'monthly',
        ]);
    }

    public function test_admin_can_send_notification_to_all_users(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->postJson('/admin/notifications/send', [
                'audience' => 'all',
                'title' => 'Scheduled Maintenance Notice',
                'message' => 'The system will undergo brief maintenance tonight at 2 AM IST.',
                'type' => 'warning',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify recipient received notification
        $received = Notification::where('user_id', $this->regularUser->id)
            ->where('title', 'Scheduled Maintenance Notice')
            ->first();
        $this->assertNotNull($received);
    }

    public function test_admin_can_send_notification_to_specific_user(): void
    {
        $response = $this->actingAs($this->adminUser, 'admin')
            ->postJson('/admin/notifications/send', [
                'audience' => 'specific',
                'user_id' => $this->regularUser->id,
                'title' => 'Account Upgrade Available',
                'message' => 'Special promo on enterprise plan.',
                'type' => 'info',
            ]);

        $response->assertStatus(200);

        $received = Notification::where('user_id', $this->regularUser->id)
            ->where('title', 'Account Upgrade Available')
            ->first();
        $this->assertNotNull($received);
    }

    public function test_non_admin_cannot_send_admin_notifications(): void
    {
        // A regular user without admin guard authentication cannot post to admin notifications
        $response = $this->actingAs($this->regularUser, 'user')
            ->postJson('/admin/notifications/send', [
                'audience' => 'all',
                'title' => 'Malicious Broadcast',
                'message' => 'Should fail',
                'type' => 'info',
            ]);

        // Guard rejects non-admin users with 401 or 302/403
        $this->assertTrue(in_array($response->getStatusCode(), [302, 401, 403]));
    }

    public function test_user_notification_center_api_and_read_state(): void
    {
        $notif = Notification::create([
            'user_id' => $this->regularUser->id,
            'title' => 'Welcome to Wappiyo',
            'comment' => 'Get started by creating your first contact list.',
            'is_read' => false,
        ]);

        // Fetch notifications
        $response = $this->actingAs($this->regularUser, 'user')
            ->getJson('/notifications');
        $response->assertStatus(200);
        $response->assertJsonStructure(['notifications', 'unread_count']);
        $this->assertGreaterThanOrEqual(1, $response->json('unread_count'));

        // Mark as read
        $markResponse = $this->actingAs($this->regularUser, 'user')
            ->postJson("/notifications/{$notif->id}/read");
        $markResponse->assertStatus(200);

        $notif->refresh();
        $this->assertTrue((bool)$notif->is_read);
    }

    public function test_subscription_renewal_reminder_command_and_deduplication(): void
    {
        // Create subscription renewing in 7 days
        $renewalDate = now()->addDays(7)->startOfDay()->format('Y-m-d H:i:s');

        $sub = Subscription::create([
            'organization_id' => $this->org->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'valid_until' => $renewalDate,
        ]);

        // Run artisan command
        $exitCode = Artisan::call('wappiyo:send-renewal-reminders');
        $this->assertEquals(0, $exitCode);

        // Verify in-app notification created
        $renewalNotif = Notification::where('user_id', $this->regularUser->id)
            ->where('title', 'like', '%Subscription Renewal%')
            ->first();
        $this->assertNotNull($renewalNotif);
        $this->assertStringContainsString('7 days', $renewalNotif->message);

        // Run again immediately — deduplication must prevent double notification on same milestone
        $countBefore = Notification::where('user_id', $this->regularUser->id)
            ->where('title', 'like', '%Subscription Renewal%')
            ->count();

        Artisan::call('wappiyo:send-renewal-reminders');

        $countAfter = Notification::where('user_id', $this->regularUser->id)
            ->where('title', 'like', '%Subscription Renewal%')
            ->count();

        $this->assertEquals($countBefore, $countAfter);
    }

    public function test_admin_renewal_due_screen_and_manual_reminder(): void
    {
        $renewalDate = now()->addDays(3)->format('Y-m-d H:i:s');
        $sub = Subscription::create([
            'organization_id' => $this->org->id,
            'plan_id' => $this->plan->id,
            'status' => 'active',
            'valid_until' => $renewalDate,
        ]);

        $response = $this->actingAs($this->adminUser, 'admin')
            ->get('/admin/subscriptions/renewal-due');
        $response->assertStatus(200);

        // Manual trigger
        $manualResponse = $this->actingAs($this->adminUser, 'admin')
            ->postJson("/admin/subscriptions/{$sub->id}/send-reminder");
        $manualResponse->assertStatus(200);
        $manualResponse->assertJson(['success' => true]);
    }
}

<?php

namespace Tests\Feature;

use App\Helpers\WebhookHelper;
use App\Models\Contact;
use App\Models\OrganizationApiKey;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiLimitsAndWebhookTest extends TestCase
{
    protected $user;
    protected $org;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'api_tester@wappiyo.com'],
            [
                'first_name' => 'API',
                'last_name' => 'Tester',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->org = Organization::firstOrCreate(
            ['identifier' => 'api-test-org'],
            [
                'name' => 'API Test Org',
                'created_by' => $this->user->id,
            ]
        );

        // Plan with contacts_limit = 5
        $plan = SubscriptionPlan::firstOrCreate(
            ['name' => 'Limited Test Plan'],
            [
                'price' => 10.00,
                'period' => 'monthly',
                'status' => 'active',
                'metadata' => json_encode([
                    'contacts_limit' => 5,
                    'message_limit' => 100,
                    'campaign_limit' => 10,
                    'canned_replies_limit' => 5,
                ]),
            ]
        );

        Subscription::updateOrCreate(
            ['organization_id' => $this->org->id],
            [
                'plan_id' => $plan->id,
                'status' => 'active',
                'start_date' => now(),
                'valid_until' => now()->addDays(30),
            ]
        );

        $this->token = OrganizationApiKey::firstOrCreate(
            ['organization_id' => $this->org->id],
            [
                'token' => 'wappiyo_test_secret_token_123',
            ]
        );
    }

    public function test_api_requires_bearer_token(): void
    {
        $response = $this->postJson('/api/contacts', [
            'first_name' => 'John',
            'phone' => '+15551234567',
        ]);

        $response->assertStatus(401);
    }

    public function test_api_allows_contact_creation_within_limits(): void
    {
        // Ensure contacts count is under limit
        Contact::where('organization_id', $this->org->id)->delete();

        $response = $this->withToken($this->token->token)->postJson('/api/contacts', [
            'first_name' => 'API Contact One',
            'last_name' => 'Doe',
            'email' => 'apicontact1@example.com',
            'phone' => '+12025550123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('contacts', [
            'organization_id' => $this->org->id,
            'first_name' => 'API Contact One',
        ]);
    }

    public function test_webhook_helper_handles_null_organization_gracefully(): void
    {
        session(['current_organization' => $this->org->id]);

        // Should not throw any null pointer or assignment error
        $result = WebhookHelper::triggerWebhookEvent('contact.created', ['test' => true]);

        $this->assertNotNull($result);
    }
}

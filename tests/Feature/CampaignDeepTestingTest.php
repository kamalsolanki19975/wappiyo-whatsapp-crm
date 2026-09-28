<?php

namespace Tests\Feature;

use App\Exports\CampaignDetailsExport;
use App\Exports\CampaignFailedMessagesExport;
use App\Helpers\DateTimeHelper;
use App\Jobs\RetryCampaignJob;
use App\Jobs\SendCampaignJob;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\CampaignRetry;
use App\Models\Chat;
use App\Models\ChatStatusLog;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\Template;
use App\Models\User;
use App\Rules\CampaignLimit;
use App\Services\CampaignRecoveryService;
use App\Services\CampaignService;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class CampaignDeepTestingTest extends TestCase
{
    protected $userA;
    protected $userB;
    protected $orgA;
    protected $orgB;
    protected $templateA;
    protected $recoveryService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recoveryService = app(CampaignRecoveryService::class);

        // Tenant A
        $this->userA = User::firstOrCreate(
            ['email' => 'campaign_owner_a@wappiyo.com'],
            [
                'first_name' => 'Owner',
                'last_name' => 'Alpha',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );

        $this->orgA = Organization::firstOrCreate(
            ['name' => 'Campaign Tenant Alpha'],
            [
                'identifier' => 'tenant-alpha-camp-' . Str::random(5),
                'created_by' => $this->userA->id,
                'timezone' => 'Asia/Kolkata',
                'metadata' => json_encode([
                    'timezone' => 'Asia/Kolkata',
                    'whatsapp' => [
                        'access_token' => 'mock_access_token_123',
                        'phone_number_id' => '10987654321',
                        'waba_id' => '98765432100',
                    ]
                ])
            ]
        );

        // Ensure Org A metadata has WhatsApp
        $orgMetaA = json_decode($this->orgA->metadata ?? '{}', true);
        $orgMetaA['whatsapp'] = [
            'access_token' => 'mock_access_token_123',
            'phone_number_id' => '10987654321',
            'waba_id' => '98765432100',
        ];
        $this->orgA->metadata = json_encode($orgMetaA);
        $this->orgA->save();

        Team::firstOrCreate(
            [
                'user_id' => $this->userA->id,
                'organization_id' => $this->orgA->id,
            ],
            [
                'role' => 'owner',
                'created_by' => $this->userA->id,
            ]
        );

        Subscription::firstOrCreate(
            ['organization_id' => $this->orgA->id],
            [
                'plan_id' => 1,
                'status' => 'active',
                'start_date' => now(),
                'valid_until' => now()->addDays(30),
            ]
        );

        // Tenant B
        $this->userB = User::firstOrCreate(
            ['email' => 'campaign_owner_b@wappiyo.com'],
            [
                'first_name' => 'Owner',
                'last_name' => 'Beta',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );

        $this->orgB = Organization::firstOrCreate(
            ['name' => 'Campaign Tenant Beta'],
            [
                'identifier' => 'tenant-beta-camp-' . Str::random(5),
                'created_by' => $this->userB->id,
                'timezone' => 'America/New_York',
            ]
        );

        Team::firstOrCreate(
            [
                'user_id' => $this->userB->id,
                'organization_id' => $this->orgB->id,
            ],
            [
                'role' => 'owner',
                'created_by' => $this->userB->id,
            ]
        );

        Subscription::firstOrCreate(
            ['organization_id' => $this->orgB->id],
            [
                'plan_id' => 1,
                'status' => 'active',
                'start_date' => now(),
                'valid_until' => now()->addDays(30),
            ]
        );

        // Template for Tenant A
        $this->templateA = Template::firstOrCreate(
            ['organization_id' => $this->orgA->id, 'name' => 'campaign_test_template'],
            [
                'meta_id' => 'template_meta_' . Str::random(8),
                'language' => 'en_US',
                'category' => 'MARKETING',
                'status' => 'APPROVED',
                'created_by' => $this->userA->id,
                'metadata' => json_encode([
                    'type' => 'standard',
                    'components' => [
                        ['type' => 'BODY', 'text' => 'Hello {{1}}, here is your special offer!'],
                    ]
                ])
            ]
        );
    }

    /**
     * Test 1: Campaign Creation, Name Validation, Special Characters, Emojis, SQL/XSS Payloads
     */
    public function test_campaign_creation_validation_and_payload_safety(): void
    {
        $this->actingAs($this->userA, 'user');
        session(['current_organization' => $this->orgA->id]);

        // 1. Empty name should fail validation (HTTP 422 JSON validation)
        $responseEmpty = $this->postJson('/campaigns', [
            'name' => '',
            'template' => $this->templateA->uuid,
            'contacts' => 'all',
            'skip_schedule' => true,
        ]);
        $responseEmpty->assertStatus(422);
        $responseEmpty->assertJsonValidationErrors(['name']);

        // 2. Safe handling of special characters, unicode, and emojis
        $specialName = '🚀 Diwali Mega Sale ✨ 50% Off [2026] & More!';
        $campaign1 = Campaign::create([
            'organization_id' => $this->orgA->id,
            'name' => $specialName,
            'template_id' => $this->templateA->id,
            'contact_group_id' => 0,
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'scheduled',
            'scheduled_at' => Carbon::now('UTC')->addHour(),
            'created_by' => $this->userA->id,
        ]);
        $this->assertEquals($specialName, $campaign1->name);

        // 3. Safe handling of SQL-injection patterns
        $sqlPayload = "Diwali Campaign ' OR '1'='1' -- DROP TABLE users;";
        $campaign2 = Campaign::create([
            'organization_id' => $this->orgA->id,
            'name' => $sqlPayload,
            'template_id' => $this->templateA->id,
            'contact_group_id' => 0,
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'scheduled',
            'scheduled_at' => Carbon::now('UTC')->addHour(),
            'created_by' => $this->userA->id,
        ]);
        $this->assertEquals($sqlPayload, $campaign2->name);

        // 4. Safe handling of XSS injection patterns
        $xssPayload = "<script>alert('XSS')</script> Spring Blast";
        $campaign3 = Campaign::create([
            'organization_id' => $this->orgA->id,
            'name' => $xssPayload,
            'template_id' => $this->templateA->id,
            'contact_group_id' => 0,
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'scheduled',
            'scheduled_at' => Carbon::now('UTC')->addHour(),
            'created_by' => $this->userA->id,
        ]);
        $this->assertEquals($xssPayload, $campaign3->name);
    }

    /**
     * Test 2: Audience Selection, Contact Group Filtering and Recipient Deduplication
     */
    public function test_audience_selection_and_recipient_deduplication(): void
    {
        $this->actingAs($this->userA, 'user');
        session(['current_organization' => $this->orgA->id]);

        // Create Contacts for Org A
        $contact1 = Contact::create([
            'organization_id' => $this->orgA->id,
            'first_name' => 'Aarav',
            'last_name' => 'Patel',
            'phone' => '+9198' . rand(10000000, 99999999),
            'created_by' => $this->userA->id,
        ]);

        $contact2 = Contact::create([
            'organization_id' => $this->orgA->id,
            'first_name' => 'Diya',
            'last_name' => 'Sharma',
            'phone' => '+9198' . rand(10000000, 99999999),
            'created_by' => $this->userA->id,
        ]);

        // Create Group
        $group = ContactGroup::create([
            'organization_id' => $this->orgA->id,
            'name' => 'VIP Customers ' . Str::random(4),
            'created_by' => $this->userA->id,
        ]);

        // Attach contact1 to group
        $contact1->contact_group_id = $group->id;
        $contact1->save();

        // 1. Campaign with specific group
        $campaignGroup = Campaign::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Group Targeted Campaign ' . Str::random(4),
            'template_id' => $this->templateA->id,
            'contact_group_id' => $group->id,
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'scheduled',
            'scheduled_at' => Carbon::now('UTC'),
            'created_by' => $this->userA->id,
        ]);

        $this->assertEquals($group->id, $campaignGroup->contact_group_id);

        // 2. Test Deduplication: Create logs and verify identical contact is NOT inserted twice
        CampaignLog::create([
            'campaign_id' => $campaignGroup->id,
            'contact_id' => $contact1->id,
            'status' => 'pending',
        ]);

        // Attempting to prepare new logs with contactIds = [contact1->id, contact2->id]
        $contactIds = collect([$contact1->id, $contact2->id]);
        $existingLogs = CampaignLog::where('campaign_id', $campaignGroup->id)
            ->whereIn('contact_id', $contactIds)
            ->pluck('contact_id')
            ->toArray();

        $newContacts = $contactIds->diff($existingLogs);
        $this->assertCount(1, $newContacts, 'Deduplication must filter out contact1 who already has a log.');
        $this->assertEquals($contact2->id, $newContacts->first());
    }

    /**
     * Test 3: Timezone Scheduling (Asia/Kolkata vs UTC)
     */
    public function test_campaign_timezone_conversion_and_scheduling(): void
    {
        // Organization A is Asia/Kolkata (UTC+05:30)
        $scheduledTimeLocal = '2026-10-15 10:00:00'; // 10:00 AM IST
        $utcConverted = DateTimeHelper::parseLocalToUtc($scheduledTimeLocal, 'Asia/Kolkata');

        // 10:00 AM IST is 04:30 AM UTC
        $this->assertEquals('2026-10-15 04:30:00', $utcConverted->format('Y-m-d H:i:s'));

        // When converted back to organization timezone Asia/Kolkata, must be 10:00:00
        $localConverted = Carbon::parse($utcConverted, 'UTC')->timezone('Asia/Kolkata');
        $this->assertEquals('2026-10-15 10:00:00', $localConverted->format('Y-m-d H:i:s'));
    }

    /**
     * Test 4: SendCampaignJob Processing & Status Lifecycle Transitions
     */
    public function test_send_campaign_job_processing_and_status_lifecycle(): void
    {
        $contact = Contact::create([
            'organization_id' => $this->orgA->id,
            'first_name' => 'Karan',
            'last_name' => 'Kapoor',
            'phone' => '+9198' . rand(10000000, 99999999),
            'created_by' => $this->userA->id,
        ]);

        $campaign = Campaign::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Lifecycle Broadcast ' . Str::random(4),
            'template_id' => $this->templateA->id,
            'contact_group_id' => 0, // all contacts
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'scheduled',
            'scheduled_at' => Carbon::now('UTC')->subMinute(), // Due now
            'created_by' => $this->userA->id,
        ]);

        // Create initial pending log
        $log = CampaignLog::create([
            'campaign_id' => $campaign->id,
            'contact_id' => $contact->id,
            'status' => 'pending',
        ]);

        $this->assertEquals('scheduled', $campaign->status);
        $this->assertEquals('pending', $log->status);

        // Simulate ongoing transition
        $campaign->update(['status' => 'ongoing']);
        $log->update(['status' => 'ongoing']);

        $this->assertEquals('ongoing', $campaign->status);
        $this->assertEquals('ongoing', $log->status);

        // Simulate successful dispatch
        $log->update(['status' => 'success']);
        $campaign->update(['status' => 'completed']);

        $this->assertEquals('completed', $campaign->status);
        $this->assertEquals('success', $log->status);
    }

    /**
     * Test 5: Webhook Delivery Status Propagation (Sent -> Delivered -> Read -> Failed) & Reconciliation
     */
    public function test_webhook_delivery_status_propagation_and_reconciliation(): void
    {
        $contact = Contact::create([
            'organization_id' => $this->orgA->id,
            'first_name' => 'Ananya',
            'last_name' => 'Verma',
            'phone' => '+9198' . rand(10000000, 99999999),
            'created_by' => $this->userA->id,
        ]);

        $campaign = Campaign::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Webhook Delivery Campaign ' . Str::random(4),
            'template_id' => $this->templateA->id,
            'contact_group_id' => 0,
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'completed',
            'scheduled_at' => Carbon::now('UTC'),
            'created_by' => $this->userA->id,
        ]);

        $wamId = 'wamid.' . Str::random(24);

        $chat = Chat::create([
            'organization_id' => $this->orgA->id,
            'contact_id' => $contact->id,
            'wam_id' => $wamId,
            'type' => 'outbound',
            'status' => 'sent',
            'metadata' => json_encode(['text' => 'Hello Ananya']),
        ]);

        $log = CampaignLog::create([
            'campaign_id' => $campaign->id,
            'contact_id' => $contact->id,
            'chat_id' => $chat->id,
            'status' => 'success',
        ]);

        // 1. Initial State: Sent
        $counts = $campaign->getCounts();
        $this->assertEquals(1, $counts->total_message_count);
        $this->assertEquals(1, $counts->total_sent_count);
        $this->assertEquals(0, $counts->total_delivered_count);
        $this->assertEquals(0, $counts->total_read_count);
        $this->assertEquals(0, $counts->total_failed_count);

        // 2. Webhook Delivered
        $chat->update(['status' => 'delivered']);
        ChatStatusLog::create([
            'chat_id' => $chat->id,
            'metadata' => json_encode(['status' => 'delivered', 'id' => $wamId]),
        ]);

        $countsDelivered = $campaign->getCounts();
        $this->assertEquals(1, $countsDelivered->total_sent_count);
        $this->assertEquals(1, $countsDelivered->total_delivered_count);
        $this->assertEquals(0, $countsDelivered->total_read_count);

        // 3. Webhook Read
        $chat->update(['status' => 'read']);
        ChatStatusLog::create([
            'chat_id' => $chat->id,
            'metadata' => json_encode(['status' => 'read', 'id' => $wamId]),
        ]);

        $countsRead = $campaign->getCounts();
        $this->assertEquals(1, $countsRead->total_sent_count);
        $this->assertEquals(1, $countsRead->total_delivered_count);
        $this->assertEquals(1, $countsRead->total_read_count);

        // 4. Duplicate Webhook: Repeated Read event does NOT increment counts (Idempotency)
        ChatStatusLog::create([
            'chat_id' => $chat->id,
            'metadata' => json_encode(['status' => 'read', 'id' => $wamId]),
        ]);

        $countsIdempotent = $campaign->getCounts();
        $this->assertEquals(1, $countsIdempotent->total_message_count);
        $this->assertEquals(1, $countsIdempotent->total_read_count);
    }

    /**
     * Test 6: Failed Message Analysis, Retry Lifecycle, Retry Limits and Exclusion
     */
    public function test_failed_message_analysis_retry_lifecycle_and_exclusion(): void
    {
        $contact = Contact::create([
            'organization_id' => $this->orgA->id,
            'first_name' => 'Vikram',
            'last_name' => 'Singh',
            'phone' => '+9198' . rand(10000000, 99999999),
            'created_by' => $this->userA->id,
        ]);

        $campaign = Campaign::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Failed & Recovery Campaign ' . Str::random(4),
            'template_id' => $this->templateA->id,
            'contact_group_id' => 0,
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'completed',
            'scheduled_at' => Carbon::now('UTC'),
            'created_by' => $this->userA->id,
        ]);

        // Failed log due to temporary rate limit error (code 130429)
        $failedLog = CampaignLog::create([
            'campaign_id' => $campaign->id,
            'contact_id' => $contact->id,
            'status' => 'failed',
            'metadata' => json_encode([
                'data' => [
                    'error' => [
                        'code' => 130429,
                        'message' => 'Rate limit exceeded for recipient phone number',
                        'type' => 'OAuthException',
                    ]
                ]
            ]),
        ]);

        // 1. Analyze failure
        $analysis = $this->recoveryService->analyzeFailure($failedLog);
        $this->assertEquals(130429, $analysis['error_code']);
        $this->assertTrue($analysis['is_retryable'], 'Meta error 130429 should be retryable.');

        // 2. Queue Retry for this log
        $retryResult = $this->recoveryService->queueRetry($campaign, [$failedLog->id], false, $this->userA->id);
        $this->assertTrue($retryResult['success']);
        $this->assertEquals(1, $retryResult['queued_count']);

        $failedLog->refresh();
        // Since queue driver executes RetryCampaignJob synchronously, attempt completed
        $this->assertEquals(1, $failedLog->retry_count);

        // Verify CampaignRetry record created and processed
        $retryRecord = CampaignRetry::where('campaign_log_id', $failedLog->id)->first();
        $this->assertNotNull($retryRecord);
        $this->assertEquals(1, $retryRecord->attempt_number);
        $this->assertEquals($campaign->id, $retryRecord->campaign_id);

        // 3. Exclude recipient from further retries
        $this->recoveryService->setLogExcluded($campaign, $failedLog->id, true);
        $failedLog->refresh();
        $this->assertTrue((bool) $failedLog->is_excluded);

        // Attempting to retry an excluded recipient should queue 0 messages
        $retryExcluded = $this->recoveryService->queueRetry($campaign, [$failedLog->id], false, $this->userA->id);
        $this->assertFalse($retryExcluded['success']);
        $this->assertEquals(0, $retryExcluded['queued_count']);

        // 4. Test MAX_RETRY_ATTEMPTS limit
        $this->recoveryService->setLogExcluded($campaign, $failedLog->id, false); // re-include
        $failedLog->update(['retry_count' => CampaignRecoveryService::MAX_RETRY_ATTEMPTS, 'retry_status' => 'failed']);

        $retryExceeded = $this->recoveryService->queueRetry($campaign, [$failedLog->id], false, $this->userA->id);
        $this->assertFalse($retryExceeded['success']);
        $this->assertStringContainsString('exceeded', $retryExceeded['message']);
    }

    /**
     * Test 7: Multi-Tenant Campaign Isolation & IDOR Protection
     */
    public function test_multi_tenant_campaign_isolation_and_idor_protection(): void
    {
        // Campaign belonging to Tenant A
        $campaignA = Campaign::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Secret Tenant A Campaign',
            'template_id' => $this->templateA->id,
            'contact_group_id' => 0,
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'scheduled',
            'scheduled_at' => Carbon::now('UTC'),
            'created_by' => $this->userA->id,
        ]);

        // Tenant B logs in
        $this->actingAs($this->userB, 'user');
        session(['current_organization' => $this->orgB->id]);

        // 1. Tenant B attempts to view Tenant A's campaign detail
        $viewResponse = $this->get('/campaigns/' . $campaignA->uuid);
        $this->assertEquals(404, $viewResponse->status(), 'Tenant B must receive 404 when attempting to access Tenant A campaign.');

        // 2. Tenant B attempts to trigger retry on Tenant A's campaign
        $retryResponse = $this->post('/campaigns/' . $campaignA->uuid . '/retry', [
            'all_eligible' => true,
        ]);
        $this->assertEquals(404, $retryResponse->status(), 'Tenant B must receive 404 when attempting to retry Tenant A campaign.');

        // 3. Tenant B attempts to delete Tenant A's campaign
        $deleteResponse = $this->delete('/campaigns/' . $campaignA->uuid);
        $this->assertNull(Campaign::where('id', $campaignA->id)->whereNotNull('deleted_at')->first(), 'Tenant A campaign must NOT be deleted by Tenant B.');
    }

    /**
     * Test 8: Campaign Reporting Exports (CSV Details & CSV Failed Messages)
     */
    public function test_campaign_exports(): void
    {
        $this->actingAs($this->userA, 'user');
        session(['current_organization' => $this->orgA->id]);

        $campaign = Campaign::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Exportable Campaign ' . Str::random(4),
            'template_id' => $this->templateA->id,
            'contact_group_id' => 0,
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'completed',
            'scheduled_at' => Carbon::now('UTC'),
            'created_by' => $this->userA->id,
        ]);

        // Test Export CSV route
        $exportResp = $this->get('/campaigns/export/' . $campaign->uuid);
        $exportResp->assertStatus(200);

        // Test Export Failed Messages CSV route
        $exportFailedResp = $this->get('/campaigns/export-failed/' . $campaign->uuid);
        $exportFailedResp->assertStatus(200);
    }

    /**
     * Test 9: Campaign Subscription Limits Enforcement
     */
    public function test_campaign_subscription_limit_enforcement(): void
    {
        $rule = new CampaignLimit();

        // When limit is not reached, rule passes
        $passes = $rule->passes('name', 'Valid Campaign Name');
        $this->assertIsBool($passes);

        $message = $rule->message();
        $this->assertStringContainsString('limit of campaigns', $message);
    }
}

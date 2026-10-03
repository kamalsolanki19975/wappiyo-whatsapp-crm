<?php

namespace Tests\Feature;

use App\Models\Call;
use App\Models\ChatLog;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\ProcessedWebhookEvent;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use App\Services\Calling\CallEventProcessor;
use App\Services\Calling\CallingService;
use App\Services\Calling\CallStatusProcessor;
use App\Services\Calling\CallingProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class WhatsAppCallingDeepValidationTest extends TestCase
{
    protected $org;
    protected $ownerUser;
    protected $agentUser1;
    protected $agentUser2;
    protected $contact;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ownerUser = User::firstOrCreate(
            ['email' => 'deep_val_owner@wappiyo.com'],
            [
                'first_name' => 'Owner',
                'last_name' => 'Boss',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->agentUser1 = User::firstOrCreate(
            ['email' => 'deep_val_agent1@wappiyo.com'],
            [
                'first_name' => 'Agent',
                'last_name' => 'One',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->agentUser2 = User::firstOrCreate(
            ['email' => 'deep_val_agent2@wappiyo.com'],
            [
                'first_name' => 'Agent',
                'last_name' => 'Two',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->org = Organization::firstOrCreate(
            ['identifier' => 'deep-val-org'],
            [
                'name' => 'Deep Validation Org',
                'created_by' => $this->ownerUser->id,
                'metadata' => json_encode([
                    'whatsapp' => [
                        'phone_number_id' => '1122334455',
                        'waba_id' => '5544332211',
                        'access_token' => 'EAAGdeepvaltoken',
                        'app_secret' => 'test_webhook_app_secret_123',
                    ],
                    'whatsapp_calling' => [
                        'enabled' => true,
                    ],
                ]),
            ]
        );

        Team::updateOrCreate(
            ['organization_id' => $this->org->id, 'user_id' => $this->ownerUser->id],
            ['role' => 'owner', 'status' => 'active', 'created_by' => $this->ownerUser->id]
        );

        Team::updateOrCreate(
            ['organization_id' => $this->org->id, 'user_id' => $this->agentUser1->id],
            ['role' => 'agent', 'status' => 'active', 'created_by' => $this->ownerUser->id]
        );

        Team::updateOrCreate(
            ['organization_id' => $this->org->id, 'user_id' => $this->agentUser2->id],
            ['role' => 'agent', 'status' => 'active', 'created_by' => $this->ownerUser->id]
        );

        Subscription::updateOrCreate(
            ['organization_id' => $this->org->id],
            [
                'plan_id' => 1,
                'status' => 'active',
                'start_date' => now(),
                'valid_until' => now()->addDays(30),
            ]
        );

        $this->contact = Contact::firstOrCreate(
            ['organization_id' => $this->org->id, 'phone' => '+919988776655'],
            [
                'first_name' => 'Vikram',
                'last_name' => 'Patel',
                'created_by' => $this->ownerUser->id,
            ]
        );

        // Ensure clean slate for each test method
        Call::where('organization_id', $this->org->id)->forceDelete();
        ProcessedWebhookEvent::where('organization_id', $this->org->id)->delete();
    }

    /**
     * Test 1: Double Call Protection - Rapid second call by same agent returns existing active call.
     */
    public function test_double_call_rapid_retry_returns_existing_active_call(): void
    {
        $mockProvider = $this->createMock(CallingProviderInterface::class);
        $mockProvider->method('initiateCall')->willReturn([
            'success' => true,
            'provider_call_id' => 'call_double_123',
            'status' => 'ringing',
            'raw' => [],
        ]);

        $service = new CallingService($this->org->id, $mockProvider);

        // First click
        $call1 = $service->initiateCall($this->agentUser1, $this->contact);
        $this->assertNotNull($call1);
        $this->assertEquals('call_double_123', $call1->provider_call_id);

        // Second click within 15 seconds by same agent -> should return identical call without duplicate record
        $call2 = $service->initiateCall($this->agentUser1, $this->contact);
        $this->assertEquals($call1->id, $call2->id);

        // Count calls for this contact in DB - should be exactly 1
        $count = Call::where('organization_id', $this->org->id)
            ->where('contact_id', $this->contact->id)
            ->where('provider_call_id', 'call_double_123')
            ->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test 2: Double Call Protection - Second agent prevented from calling contact with active call.
     */
    public function test_second_agent_prevented_from_calling_contact_with_active_call(): void
    {
        $mockProvider = $this->createMock(CallingProviderInterface::class);
        $mockProvider->method('initiateCall')->willReturn([
            'success' => true,
            'provider_call_id' => 'call_active_agent1',
            'status' => 'ringing',
            'raw' => [],
        ]);

        $service = new CallingService($this->org->id, $mockProvider);
        $service->initiateCall($this->agentUser1, $this->contact);

        // Agent 2 tries to call same contact
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('There is already an active call in progress for this contact.');

        $service->initiateCall($this->agentUser2, $this->contact);
    }

    /**
     * Test 3: Agent RBAC - Agent cannot view other agents' call details via API.
     */
    public function test_agent_cannot_view_other_agents_call_details(): void
    {
        $call = Call::create([
            'organization_id' => $this->org->id,
            'contact_id' => $this->contact->id,
            'user_id' => $this->agentUser1->id,
            'customer_phone' => '+919988776655',
            'direction' => 'outbound',
            'status' => 'completed',
            'duration' => 60,
        ]);

        // Agent 2 attempts to view Agent 1's call
        $response = $this->actingAs($this->agentUser2, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->getJson("/calls/{$call->uuid}");

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized to view this call record.',
        ]);

        // Agent 1 views own call -> 200 OK
        $responseAgent1 = $this->actingAs($this->agentUser1, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->getJson("/calls/{$call->uuid}");

        $responseAgent1->assertStatus(200);
        $responseAgent1->assertJson(['success' => true]);
    }

    /**
     * Test 4: Agent RBAC - Agent cannot modify notes or terminate other agents' call.
     */
    public function test_agent_cannot_modify_or_terminate_other_agents_call(): void
    {
        $call = Call::create([
            'organization_id' => $this->org->id,
            'contact_id' => $this->contact->id,
            'user_id' => $this->agentUser1->id,
            'customer_phone' => '+919988776655',
            'direction' => 'outbound',
            'status' => 'connected',
            'started_at' => now()->subMinute(),
        ]);

        $service = new CallingService($this->org->id);

        // Agent 2 attempts to update notes on Agent 1's call
        try {
            $service->updateNotes($call->uuid, 'Malicious note', $this->agentUser2);
            $this->fail('Expected authorization exception was not thrown.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Agents may only manage their own calls', $e->getMessage());
        }

        // Agent 2 attempts to terminate Agent 1's call
        try {
            $service->endCall($call->uuid, $this->agentUser2);
            $this->fail('Expected authorization exception was not thrown.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Agents may only manage their own calls', $e->getMessage());
        }
    }

    /**
     * Test 5: Agent RBAC - Agent cannot export CSV (403), Owner can export.
     */
    public function test_agent_cannot_export_csv_but_owner_can(): void
    {
        // Agent export attempt -> 403
        $responseAgent = $this->actingAs($this->agentUser1, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->get('/calls/export');

        $responseAgent->assertStatus(403);

        // Owner export attempt -> 200 Streamed CSV
        $responseOwner = $this->actingAs($this->ownerUser, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->get('/calls/export');

        $responseOwner->assertStatus(200);
        $this->assertStringContainsString('text/csv', $responseOwner->headers->get('Content-Type'));
    }

    /**
     * Test 6: Webhook Security - Rejects missing signature when app_secret is configured.
     */
    public function test_webhook_rejects_missing_signature_when_app_secret_configured(): void
    {
        $payload = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'field' => 'calls',
                            'value' => [
                                'call_id' => 'wa_call_unsigned_001',
                                'event' => 'connected',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // Send POST request without X-Hub-Signature-256 header
        $response = $this->postJson("/webhook/whatsapp/{$this->org->identifier}", $payload);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Invalid payload signature',
        ]);
    }

    /**
     * Test 7: Webhook Security - Rejects invalid HMAC signature, accepts valid HMAC.
     */
    public function test_webhook_verifies_hmac_signature_correctly(): void
    {
        $payload = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'field' => 'calls',
                            'value' => [
                                'call_id' => 'wa_call_sig_002',
                                'event' => 'connected',
                                'direction' => 'outbound',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $rawPayload = json_encode($payload);

        // Invalid signature
        $badResponse = $this->call(
            'POST',
            "/webhook/whatsapp/{$this->org->identifier}",
            [],
            [],
            [],
            [
                'HTTP_X-Hub-Signature-256' => 'sha256=invalidhash99999999999999999999999999999999999999999999999999999999',
                'CONTENT_TYPE' => 'application/json',
            ],
            $rawPayload
        );
        $badResponse->assertStatus(400);

        // Valid signature
        $secret = 'test_webhook_app_secret_123';
        $validHash = 'sha256=' . hash_hmac('sha256', $rawPayload, $secret);

        $goodResponse = $this->call(
            'POST',
            "/webhook/whatsapp/{$this->org->identifier}",
            [],
            [],
            [],
            [
                'HTTP_X-Hub-Signature-256' => $validHash,
                'CONTENT_TYPE' => 'application/json',
            ],
            $rawPayload
        );
        $goodResponse->assertStatus(200);
    }

    /**
     * Test 8: Webhook Resilience - Handles malformed or empty payloads gracefully without 500 error.
     */
    public function test_webhook_handles_malformed_payload_gracefully(): void
    {
        $emptyPayload = [];
        $rawPayload = json_encode($emptyPayload);
        $secret = 'test_webhook_app_secret_123';
        $validHash = 'sha256=' . hash_hmac('sha256', $rawPayload, $secret);

        $response = $this->call(
            'POST',
            "/webhook/whatsapp/{$this->org->identifier}",
            [],
            [],
            [],
            [
                'HTTP_X-Hub-Signature-256' => $validHash,
                'CONTENT_TYPE' => 'application/json',
            ],
            $rawPayload
        );

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ignored',
            'message' => 'Malformed or empty webhook payload',
        ]);
    }

    /**
     * Test 9: Webhook Idempotency & Terminal State Protection - Completed call cannot be reverted by out-of-order webhook.
     */
    public function test_webhook_cannot_revert_completed_call_to_ringing(): void
    {
        $call = Call::create([
            'organization_id' => $this->org->id,
            'contact_id' => $this->contact->id,
            'provider_call_id' => 'call_ooo_terminal',
            'customer_phone' => '+919988776655',
            'direction' => 'outbound',
            'status' => 'completed',
            'duration' => 120,
            'started_at' => now()->subMinutes(2),
            'ended_at' => now(),
        ]);

        $oooWebhookChange = [
            'field' => 'calls',
            'value' => [
                'id' => 'evt_ooo_ringing_999',
                'call_id' => 'call_ooo_terminal',
                'event' => 'ringing',
                'direction' => 'outbound',
                'to' => '+919988776655',
            ],
        ];

        $result = CallEventProcessor::process($oooWebhookChange, $this->org);
        $this->assertTrue($result['success']);

        // Call status must remain completed!
        $call->refresh();
        $this->assertEquals('completed', $call->status);
        $this->assertEquals(120, $call->duration);
    }

    /**
     * Test 10: Inbound Deduplication - Matches existing contact stored without leading '+'.
     */
    public function test_inbound_call_matches_existing_contact_without_plus_sign(): void
    {
        // Clean up any existing contact for this test phone number
        Contact::where('organization_id', $this->org->id)
            ->where(function ($q) {
                $q->where('phone', '+918877665544')->orWhere('phone', '918877665544');
            })->forceDelete();

        // Create contact without '+'
        $noPlusContact = Contact::create([
            'organization_id' => $this->org->id,
            'first_name' => 'Aditi',
            'last_name' => 'Roy',
            'phone' => '918877665544',
            'created_by' => $this->ownerUser->id,
        ]);

        // Incoming call webhook sends E164 format with '+'
        $inboundChange = [
            'field' => 'calls',
            'value' => [
                'id' => 'evt_inbound_dedup_01',
                'call_id' => 'call_inbound_dedup_01',
                'event' => 'connected',
                'direction' => 'inbound',
                'from' => '+918877665544',
            ],
        ];

        $result = CallEventProcessor::process($inboundChange, $this->org);
        $this->assertTrue($result['success']);

        $call = Call::where('provider_call_id', 'call_inbound_dedup_01')->first();
        $this->assertNotNull($call);
        $this->assertEquals($noPlusContact->id, $call->contact_id);

        // Verify no duplicate contact was created
        $matchingContacts = Contact::where('organization_id', $this->org->id)
            ->where(function ($q) {
                $q->where('phone', '+918877665544')
                  ->orWhere('phone', '918877665544');
            })->count();
        $this->assertEquals(1, $matchingContacts);
    }

    /**
     * Test 11: Accurate Duration Calculation using UTC timestamps.
     */
    public function test_duration_calculated_accurately_without_timezone_skew(): void
    {
        $call = Call::create([
            'organization_id' => $this->org->id,
            'contact_id' => $this->contact->id,
            'user_id' => $this->ownerUser->id,
            'customer_phone' => '+919988776655',
            'direction' => 'outbound',
            'status' => 'connected',
            'connected_at' => now()->subSeconds(75),
        ]);

        $mockProvider = $this->createMock(CallingProviderInterface::class);
        $mockProvider->method('terminateCall')->willReturn(['success' => true]);

        $service = new CallingService($this->org->id, $mockProvider);
        $endedCall = $service->endCall($call->uuid, $this->ownerUser);

        // Duration should be approximately 75 seconds (between 74 and 76)
        $this->assertEquals('completed', $endedCall->status);
        $this->assertGreaterThanOrEqual(74, $endedCall->duration);
        $this->assertLessThanOrEqual(76, $endedCall->duration);
    }

    /**
     * Test 12: CSV Formula Injection Protection.
     */
    public function test_csv_export_sanitizes_formula_injection_characters(): void
    {
        Call::create([
            'organization_id' => $this->org->id,
            'contact_id' => $this->contact->id,
            'user_id' => $this->ownerUser->id,
            'customer_phone' => '+919988776655',
            'direction' => 'outbound',
            'status' => 'completed',
            'duration' => 30,
            'notes' => '=cmd|"/C calc"!A0',
            'disposition' => '+3312345678',
        ]);

        $response = $this->actingAs($this->ownerUser, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->get('/calls/export');

        $response->assertStatus(200);
        $content = $response->streamedContent();

        // The dangerous formula '=' and '+' characters must be prefixed with a single quote "'"
        $this->assertStringContainsString("'=cmd", $content);
        $this->assertStringContainsString("'+3312345678", $content);
    }
}

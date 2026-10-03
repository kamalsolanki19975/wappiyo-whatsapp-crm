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
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class WhatsAppCallingTest extends TestCase
{
    protected $orgA;
    protected $orgB;
    protected $userA;
    protected $userB;
    protected $contactA;

    protected function setUp(): void
    {
        parent::setUp();

        // User A & Org A
        $this->userA = User::firstOrCreate(
            ['email' => 'calling_test_a@wappiyo.com'],
            [
                'first_name' => 'Agent',
                'last_name' => 'Alpha',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
        $this->userA->email_verified_at = now();
        $this->userA->save();

        $this->orgA = Organization::firstOrCreate(
            ['identifier' => 'calling-org-alpha'],
            [
                'name' => 'Calling Alpha Corp',
                'created_by' => $this->userA->id,
                'metadata' => json_encode([
                    'whatsapp' => [
                        'phone_number_id' => '1234567890',
                        'waba_id' => '9876543210',
                        'access_token' => 'EAAGmocktoken',
                    ],
                    'whatsapp_calling' => [
                        'enabled' => true,
                    ],
                ]),
            ]
        );

        Team::firstOrCreate(
            ['organization_id' => $this->orgA->id, 'user_id' => $this->userA->id],
            ['role' => 'owner', 'status' => 'active', 'created_by' => $this->userA->id]
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

        $this->contactA = Contact::firstOrCreate(
            ['organization_id' => $this->orgA->id, 'phone' => '919876543210'],
            [
                'first_name' => 'Rahul',
                'last_name' => 'Sharma',
                'created_by' => $this->userA->id,
            ]
        );

        // User B & Org B for multi-tenant / IDOR tests
        $this->userB = User::firstOrCreate(
            ['email' => 'calling_test_b@wappiyo.com'],
            [
                'first_name' => 'Agent',
                'last_name' => 'Beta',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
        $this->userB->email_verified_at = now();
        $this->userB->save();

        $this->orgB = Organization::firstOrCreate(
            ['identifier' => 'calling-org-beta'],
            [
                'name' => 'Calling Beta Corp',
                'created_by' => $this->userB->id,
            ]
        );

        Team::firstOrCreate(
            ['organization_id' => $this->orgB->id, 'user_id' => $this->userB->id],
            ['role' => 'owner', 'status' => 'active', 'created_by' => $this->userB->id]
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
    }

    /**
     * 1. Test Call Model creation, UUID generation, duration formatting, and relationships.
     */
    public function test_call_model_creation_and_attributes()
    {
        $call = Call::create([
            'organization_id' => $this->orgA->id,
            'contact_id' => $this->contactA->id,
            'user_id' => $this->userA->id,
            'phone_number_id' => '1234567890',
            'customer_phone' => '+91 98765 43210',
            'direction' => 'outbound',
            'provider' => 'meta',
            'provider_call_id' => 'meta_call_' . Str::random(10),
            'status' => 'connected',
            'duration' => 145, // 2 minutes 25 seconds
            'started_at' => now()->subSeconds(145),
            'connected_at' => now()->subSeconds(140),
            'ended_at' => now(),
            'disposition' => 'Interested',
            'notes' => 'Customer inquired about enterprise pricing.',
        ]);

        $this->assertNotEmpty($call->uuid);
        $this->assertEquals('02:25', $call->formatted_duration);
        $this->assertEquals($this->contactA->id, $call->contact->id);
        $this->assertEquals($this->userA->id, $call->user->id);
        $this->assertEquals($this->orgA->id, $call->organization->id);
    }

    /**
     * 2. Test status normalization and transition guards (e.g. out-of-order webhook delivery).
     */
    public function test_status_normalization_and_transition_guard()
    {
        // Meta event mapping
        $this->assertEquals('ringing', CallStatusProcessor::mapMetaEventToStatus('ringing'));
        $this->assertEquals('connected', CallStatusProcessor::mapMetaEventToStatus('accepted'));
        $this->assertEquals('completed', CallStatusProcessor::mapMetaEventToStatus('terminated'));
        $this->assertEquals('failed', CallStatusProcessor::mapMetaEventToStatus('failed'));

        // Valid transition
        $this->assertTrue(CallStatusProcessor::isValidTransition('initiating', 'ringing'));
        $this->assertTrue(CallStatusProcessor::isValidTransition('ringing', 'connected'));
        $this->assertTrue(CallStatusProcessor::isValidTransition('connected', 'completed'));

        // Invalid backward transitions (out-of-order webhook safety)
        $this->assertFalse(CallStatusProcessor::isValidTransition('completed', 'ringing'));
        $this->assertFalse(CallStatusProcessor::isValidTransition('completed', 'connected'));
        $this->assertFalse(CallStatusProcessor::isValidTransition('failed', 'connected'));
    }

    /**
     * 3. Test Call History listing & Tenant Isolation.
     */
    public function test_call_history_listing_and_tenant_isolation()
    {
        // Create call for Org A
        $callA = Call::create([
            'organization_id' => $this->orgA->id,
            'contact_id' => $this->contactA->id,
            'user_id' => $this->userA->id,
            'customer_phone' => '919876543210',
            'direction' => 'outbound',
            'status' => 'completed',
            'duration' => 60,
        ]);

        // Create call for Org B
        $callB = Call::create([
            'organization_id' => $this->orgB->id,
            'user_id' => $this->userB->id,
            'customer_phone' => '919123456789',
            'direction' => 'inbound',
            'status' => 'completed',
            'duration' => 90,
        ]);

        $this->actingAs($this->userA, 'user');
        session(['current_organization' => $this->orgA->id]);

        // User A requests call history
        $responseA = $this->getJson('/calls');

        $responseA->assertStatus(200);
        $dataA = $responseA->json('data');
        $uuidsA = collect($dataA)->pluck('uuid')->toArray();

        $this->assertContains($callA->uuid, $uuidsA);
        $this->assertNotContains($callB->uuid, $uuidsA);

        // IDOR Test: User A attempts to view Org B's call directly
        $idorResponse = $this->getJson('/calls/' . $callB->uuid);
        $idorResponse->assertStatus(404);
    }

    /**
     * 4. Test updating Call Disposition, Notes, and Follow-up.
     */
    public function test_call_notes_disposition_and_follow_up()
    {
        $call = Call::create([
            'organization_id' => $this->orgA->id,
            'contact_id' => $this->contactA->id,
            'user_id' => $this->userA->id,
            'customer_phone' => '919876543210',
            'direction' => 'outbound',
            'status' => 'completed',
        ]);

        $this->actingAs($this->userA, 'user');
        session(['current_organization' => $this->orgA->id]);

        // Update Disposition
        $respDisp = $this->postJson("/calls/{$call->uuid}/disposition", [
            'disposition' => 'Converted',
        ]);

        $respDisp->assertStatus(200);
        $respDisp->assertJson(['success' => true]);
        $this->assertEquals('Converted', $call->fresh()->disposition);

        // Update Notes
        $respNotes = $this->postJson("/calls/{$call->uuid}/notes", [
            'notes' => 'Contract signed. Follow up next month.',
        ]);

        $respNotes->assertStatus(200);
        $this->assertEquals('Contract signed. Follow up next month.', $call->fresh()->notes);

        // Schedule Follow-up
        $followUpDate = now()->addDays(3)->format('Y-m-d H:i:s');
        $respFollow = $this->postJson("/calls/{$call->uuid}/follow-up", [
            'follow_up_at' => $followUpDate,
            'reminder_notes' => 'Check onboarding status',
        ]);

        $respFollow->assertStatus(200);
        $this->assertNotNull($call->fresh()->follow_up_at);

        // IDOR Test: User B cannot modify User A's call notes
        $this->actingAs($this->userB, 'user');
        session(['current_organization' => $this->orgB->id]);

        $respIdor = $this->postJson("/calls/{$call->uuid}/notes", [
            'notes' => 'Malicious overwrite attempt',
        ]);

        $respIdor->assertStatus(404);
        $this->assertNotEquals('Malicious overwrite attempt', $call->fresh()->notes);
    }

    /**
     * 5. Test Webhook idempotency and event processing for Meta calling events.
     */
    public function test_webhook_idempotency_and_event_processing()
    {
        Event::fake();

        $eventId = 'evt_' . Str::random(12);
        $providerCallId = 'meta_call_' . Str::random(12);

        $change = [
            'field' => 'calls',
            'value' => [
                'id' => $eventId,
                'call_id' => $providerCallId,
                'phone_number_id' => '1234567890',
                'from' => '919876543210',
                'to' => '1234567890',
                'event' => 'accepted',
                'timestamp' => time(),
            ],
        ];

        // First delivery: processes and creates call
        $result1 = CallEventProcessor::process($change, $this->orgA);
        $this->assertTrue($result1['success']);

        $call = Call::where('provider_call_id', $providerCallId)->first();
        $this->assertNotNull($call);
        $this->assertEquals('connected', $call->status);

        // Verify idempotency record created
        $this->assertTrue(ProcessedWebhookEvent::hasBeenProcessed($this->orgA->id, $eventId));

        // Duplicate delivery with identical event ID
        $result2 = CallEventProcessor::process($change, $this->orgA);
        $this->assertTrue($result2['success']);
        $this->assertEquals('duplicate_ignored', $result2['action']);

        // Ensure call count for this provider call ID did not duplicate
        $this->assertEquals(1, Call::where('provider_call_id', $providerCallId)->count());

        // Process call termination webhook event
        $terminateEventId = 'evt_' . Str::random(12);
        $terminateChange = [
            'field' => 'calls',
            'value' => [
                'id' => $terminateEventId,
                'call_id' => $providerCallId,
                'phone_number_id' => '1234567890',
                'from' => '919876543210',
                'to' => '1234567890',
                'event' => 'terminated',
                'duration' => 120,
                'timestamp' => time(),
            ],
        ];

        CallEventProcessor::process($terminateChange, $this->orgA);
        $call->refresh();
        $this->assertEquals('completed', $call->status);
        $this->assertEquals(120, $call->duration);

        // Verify CRM ChatLog activity timeline entry created
        $chatLog = ChatLog::where('contact_id', $call->contact_id)
            ->where('entity_type', 'call')
            ->where('entity_id', $call->id)
            ->first();

        $this->assertNotNull($chatLog);
    }

    /**
     * 6. Test Organization Settings Calling toggle.
     */
    public function test_calling_configuration_toggle()
    {
        $this->actingAs($this->userA, 'user');
        session(['current_organization' => $this->orgA->id]);

        // Toggle OFF
        $responseOff = $this->post('/settings/whatsapp/calling-toggle', [
            'enabled' => false,
        ]);

        $responseOff->assertStatus(302);
        $orgMetadata = json_decode($this->orgA->fresh()->metadata, true);
        $this->assertFalse($orgMetadata['whatsapp_calling']['enabled']);

        // Toggle ON
        $responseOn = $this->post('/settings/whatsapp/calling-toggle', [
            'enabled' => true,
        ]);

        $responseOn->assertStatus(302);
        $orgMetadata = json_decode($this->orgA->fresh()->metadata, true);
        $this->assertTrue($orgMetadata['whatsapp_calling']['enabled']);
    }

    /**
     * 7. Test Call Analytics metrics calculation.
     */
    public function test_call_analytics_and_kpis()
    {
        // Insert sample calls for analytics testing
        Call::create([
            'organization_id' => $this->orgA->id,
            'customer_phone' => '919876543210',
            'direction' => 'outbound',
            'status' => 'completed',
            'duration' => 120,
            'started_at' => now(),
        ]);

        Call::create([
            'organization_id' => $this->orgA->id,
            'customer_phone' => '919876543211',
            'direction' => 'inbound',
            'status' => 'completed',
            'duration' => 180,
            'started_at' => now(),
        ]);

        Call::create([
            'organization_id' => $this->orgA->id,
            'customer_phone' => '919876543212',
            'direction' => 'inbound',
            'status' => 'missed',
            'duration' => 0,
            'started_at' => now(),
        ]);

        $service = new CallingService($this->orgA->id);
        $analytics = $service->getAnalytics();

        $this->assertGreaterThanOrEqual(3, $analytics['total_calls']);
        $this->assertGreaterThanOrEqual(2, $analytics['connected_calls']);
        $this->assertGreaterThanOrEqual(1, $analytics['missed_calls']);
        $this->assertGreaterThanOrEqual(300, $analytics['total_talk_time']);
    }

    /**
     * 8. Test CSV Export of Call Records.
     */
    public function test_export_csv()
    {
        $this->actingAs($this->userA, 'user');
        session(['current_organization' => $this->orgA->id]);

        $response = $this->get('/calls/export');

        $response->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('calls_export_', $response->headers->get('Content-Disposition'));
    }
}

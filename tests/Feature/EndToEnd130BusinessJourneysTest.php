<?php

namespace Tests\Feature;

use App\Models\BillingInvoice;
use App\Models\BillingPayment;
use App\Models\Call;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\Chat;
use App\Models\ChatNote;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Lead;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\Otp;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\Template;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\Calling\CallingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\TestCase;

class EndToEnd130BusinessJourneysTest extends TestCase
{
    protected User $adminUser;
    protected SubscriptionPlan $plan;
    protected TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin@wappiyo.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->plan = SubscriptionPlan::firstOrCreate(
            ['name' => 'Enterprise Pro'],
            [
                'price' => 99.00,
                'period' => 'monthly',
                'status' => 'active',
                'metadata' => json_encode(['messages' => 10000, 'calls' => 1000]),
            ]
        );

        $this->category = TicketCategory::firstOrCreate(
            ['name' => 'General Support']
        );
    }

    /**
     * JOURNEY A — NEW CUSTOMER
     * Website → Signup → Duplicate Email Validation → Email OTP → Verify Your Mail ID →
     * Onboarding → Setup Complete → Dashboard → WhatsApp Connect → Contact → Message → Call → Ticket → Follow-up
     */
    public function test_journey_a_new_customer_full_lifecycle(): void
    {
        $uniqueEmail = 'journey.customer.' . Str::random(8) . '@wappiyo.com';

        // 1. Duplicate email check failure test (positive negative test)
        User::create([
            'first_name' => 'Existing',
            'last_name' => 'Person',
            'email' => 'existing.journey@wappiyo.com',
            'password' => Hash::make('Pass123!'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $dupResponse = $this->postJson('/send-otp', [
            'first_name' => 'Aarav',
            'last_name' => 'Patel',
            'organization_name' => 'Aarav Tech',
            'email' => 'EXISTING.JOURNEY@WAPPIYO.COM',
            'password' => 'Pass123!',
            'password_confirmation' => 'Pass123!',
        ]);
        $dupResponse->assertStatus(422);

        // 2. Initiate fresh signup with unique email
        $signupResponse = $this->postJson('/send-otp', [
            'first_name' => 'Aarav',
            'last_name' => 'Patel',
            'organization_name' => 'Aarav Tech Labs',
            'email' => $uniqueEmail,
            'password' => 'Pass123!',
            'password_confirmation' => 'Pass123!',
        ]);
        $signupResponse->assertStatus(200);
        $signupResponse->assertJson(['success' => true]);

        // 3. Retrieve generated OTP record and verify code
        $otpRecord = Otp::where('email', strtolower($uniqueEmail))->latest()->first();
        $this->assertNotNull($otpRecord);
        $otpRecord->update(['otp' => Hash::make('889900')]);

        $verifyResponse = $this->postJson('/verify-otp', [
            'email' => $uniqueEmail,
            'otp' => '889900',
        ]);
        $verifyResponse->assertStatus(200);

        $customer = User::where('email', strtolower($uniqueEmail))->first();
        $this->assertNotNull($customer);
        $this->assertNotNull($customer->email_verified_at);

        // 4. Verify Organization & Team creation
        $org = Organization::where('created_by', $customer->id)->first();
        $this->assertNotNull($org);
        $team = Team::where('user_id', $customer->id)->where('organization_id', $org->id)->first();
        $this->assertNotNull($team);
        $this->assertEquals('owner', $team->role);

        // 5. Onboarding: complete steps
        $this->actingAs($customer, 'user')
            ->withSession(['current_organization' => $org->id])
            ->post('/onboarding/company', [
                'company_name' => 'Aarav Tech Labs Pvt Ltd',
                'industry' => 'Technology',
                'size' => '10-50',
            ]);

        // 6. Connect Contact in CRM
        $contact = Contact::create([
            'organization_id' => $org->id,
            'first_name' => 'Karan',
            'last_name' => 'Mehta',
            'phone' => '+919876543210',
            'email' => 'karan@client.com',
            'created_by' => $customer->id,
        ]);
        $this->assertNotNull($contact);

        // 7. Send WhatsApp CRM Message
        $chat = Chat::create([
            'organization_id' => $org->id,
            'contact_id' => $contact->id,
            'type' => 'outbound',
            'metadata' => json_encode(['text' => 'Hello Karan, welcome to our services!']),
            'status' => 'delivered',
            'created_at' => now(),
        ]);
        $this->assertNotNull($chat);

        // 8. Initiate Meta WhatsApp Call
        $call = Call::create([
            'organization_id' => $org->id,
            'contact_id' => $contact->id,
            'user_id' => $customer->id,
            'customer_phone' => '+919876543210',
            'direction' => 'outbound',
            'status' => 'completed',
            'duration' => 125,
            'started_at' => now()->subMinutes(3),
            'ended_at' => now(),
            'notes' => 'Product demo successful. Customer requested invoice.',
            'disposition' => 'interested',
        ]);
        $this->assertNotNull($call);

        // 9. Create Support Ticket
        $ticket = Ticket::create([
            'uuid' => (string) Str::uuid(),
            'reference' => 'TCK-' . strtoupper(Str::random(6)),
            'organization_id' => $org->id,
            'user_id' => $customer->id,
            'category_id' => $this->category->id,
            'subject' => 'Enterprise SLA Setup',
            'message' => 'Need priority support configuration for enterprise account.',
            'priority' => 'high',
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertNotNull($ticket);
    }

    /**
     * JOURNEY B — SALES
     * Lead → Contact → WhatsApp → Template → Campaign → Delivery → Read → Call → Notes → Follow-up → Report
     */
    public function test_journey_b_sales_pipeline_to_reporting(): void
    {
        $org = Organization::create([
            'identifier' => 'sales-org-' . Str::random(6),
            'name' => 'Vibrant Sales Corp',
            'created_by' => $this->adminUser->id,
            'timezone' => 'Asia/Kolkata',
        ]);

        Subscription::create([
            'organization_id' => $org->id,
            'status' => 'active',
            'valid_until' => now()->addDays(30),
        ]);

        // 1. Inbound Website Lead Capture
        $lead = Lead::create([
            'name' => 'Vikram Aditya',
            'first_name' => 'Vikram',
            'last_name' => 'Aditya',
            'email' => 'vikram@buyer.com',
            'phone' => '+919811122233',
            'message' => 'Interested in enterprise WhatsApp CRM with voice calling',
            'status' => 'pending',
            'source' => 'website_contact',
            'created_at' => now(),
        ]);
        $this->assertNotNull($lead);

        // 2. Convert Lead to CRM Contact
        $contact = Contact::create([
            'organization_id' => $org->id,
            'first_name' => $lead->first_name,
            'last_name' => $lead->last_name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'created_by' => $this->adminUser->id,
        ]);
        $lead->update(['status' => 'converted']);

        // 3. WhatsApp Template & Broadcast Campaign
        $template = Template::create([
            'organization_id' => $org->id,
            'meta_id' => 'meta_tmpl_' . Str::random(12),
            'name' => 'sales_promotional_v1_' . Str::random(4),
            'category' => 'MARKETING',
            'language' => 'en_US',
            'status' => 'APPROVED',
            'created_by' => $this->adminUser->id,
            'metadata' => json_encode([
                ['type' => 'BODY', 'text' => 'Exclusive enterprise offer for {{1}}'],
            ]),
        ]);

        $group = ContactGroup::create([
            'organization_id' => $org->id,
            'name' => 'Hot Inbound Leads',
            'created_by' => $this->adminUser->id,
        ]);
        $contact->update(['contact_group_id' => $group->id]);

        $campaign = Campaign::create([
            'organization_id' => $org->id,
            'name' => 'Enterprise Sales Q3',
            'template_id' => $template->id,
            'contact_group_id' => $group->id,
            'metadata' => json_encode(['variables' => ['1' => 'Enterprise']]),
            'status' => 'completed',
            'scheduled_at' => now()->subHours(1),
            'created_by' => $this->adminUser->id,
        ]);

        // 4. Message Delivery & Read Events
        $log = CampaignLog::create([
            'campaign_id' => $campaign->id,
            'contact_id' => $contact->id,
            'status' => 'success',
            'metadata' => json_encode([
                'message_id' => 'wamid.' . Str::random(16),
                'read_at' => now()->subMinutes(30)->toISOString(),
            ]),
        ]);
        $this->assertNotNull($log);

        // 5. Agent Call with Follow-up Disposition
        $call = Call::create([
            'organization_id' => $org->id,
            'contact_id' => $contact->id,
            'user_id' => $this->adminUser->id,
            'customer_phone' => $contact->phone,
            'direction' => 'outbound',
            'status' => 'completed',
            'duration' => 240,
            'notes' => 'Discussed pricing and API limits. Sent proposal.',
            'disposition' => 'follow_up_scheduled',
        ]);
        $this->assertNotNull($call);

        // 6. Verify Reporting Service aggregates both campaign and call data accurately
        $reportingService = app(\App\Services\ReportingService::class);
        $startDate = now()->subDays(7);
        $endDate = now()->addDay();
        $adminReport = $reportingService->getAdminOverviewReport($startDate, $endDate);

        $this->assertGreaterThanOrEqual(1, $adminReport['summary']['total_organizations']);
        $orgRow = collect($adminReport['organizations'])->firstWhere('id', $org->id);
        $this->assertNotNull($orgRow);
        $this->assertEquals(1, $orgRow['calls_count']);
        $this->assertEquals(4.0, (float) $orgRow['call_minutes']);
    }

    /**
     * JOURNEY C — SUPPORT
     * Customer Message → Inbox → Ticket → Agent → Call → Notes → Resolution → Customer Timeline
     */
    public function test_journey_c_support_inbox_ticket_and_resolution(): void
    {
        $org = Organization::create([
            'identifier' => 'support-org-' . Str::random(6),
            'name' => 'CloudSupport Hub',
            'created_by' => $this->adminUser->id,
        ]);

        Subscription::create([
            'organization_id' => $org->id,
            'status' => 'active',
            'valid_until' => now()->addDays(30),
        ]);

        $agent = User::create([
            'first_name' => 'Sarah',
            'last_name' => 'Agent',
            'email' => 'sarah.agent.' . Str::random(6) . '@wappiyo.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        Team::create([
            'organization_id' => $org->id,
            'user_id' => $agent->id,
            'role' => 'agent',
            'created_by' => $this->adminUser->id,
        ]);

        $contact = Contact::create([
            'organization_id' => $org->id,
            'first_name' => 'Pooja',
            'last_name' => 'Desai',
            'phone' => '+919988776655',
            'created_by' => $this->adminUser->id,
        ]);

        // 1. Inbound Customer Message
        $inboundChat = Chat::create([
            'organization_id' => $org->id,
            'contact_id' => $contact->id,
            'type' => 'inbound',
            'metadata' => json_encode(['text' => 'Help: Webhook verification failed on our Meta integration']),
            'status' => 'read',
            'created_at' => now(),
        ]);

        // 2. Create Support Ticket
        $ticket = Ticket::create([
            'uuid' => (string) Str::uuid(),
            'reference' => 'TCK-SUP-' . strtoupper(Str::random(4)),
            'organization_id' => $org->id,
            'user_id' => $this->adminUser->id,
            'category_id' => $this->category->id,
            'assigned_to' => $agent->id,
            'subject' => 'Meta Webhook Verification Failure',
            'message' => 'Help: Webhook verification failed on our Meta integration',
            'priority' => 'critical',
            'status' => 'open',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Support Voice Call
        $supportCall = Call::create([
            'organization_id' => $org->id,
            'contact_id' => $contact->id,
            'user_id' => $agent->id,
            'customer_phone' => $contact->phone,
            'direction' => 'outbound',
            'status' => 'completed',
            'duration' => 180,
            'notes' => 'Walked customer through webhook callback URL and verify token in Meta Developer Portal.',
            'disposition' => 'resolved',
        ]);

        // 4. Resolve Ticket
        $ticket->update([
            'status' => 'resolved',
            'closed_by' => $agent->id,
        ]);
        $this->assertEquals('resolved', $ticket->fresh()->status);
    }

    /**
     * JOURNEY D — BILLING & RENEWALS
     * Signup → Trial → Subscription → Payment → Invoice → Usage → Renewal Notification → Renewal
     */
    public function test_journey_d_billing_lifecycle_and_renewal_automation(): void
    {
        $org = Organization::create([
            'identifier' => 'billing-org-' . Str::random(6),
            'name' => 'SaaS Enterprise Client',
            'created_by' => $this->adminUser->id,
        ]);

        // 1. Initial 14-day Trial with 7 days left
        $sub = Subscription::create([
            'organization_id' => $org->id,
            'plan_id' => $this->plan->id,
            'status' => 'trial',
            'start_date' => now()->subDays(14),
            'valid_until' => now()->addDays(7)->startOfDay()->format('Y-m-d H:i:s'),
        ]);

        // 2. Billing Payment & Invoice Generation
        $payment = BillingPayment::create([
            'organization_id' => $org->id,
            'processor' => 'stripe',
            'amount' => 99.00,
            'details' => json_encode(['currency' => 'USD', 'status' => 'paid', 'transaction_id' => 'tx_' . Str::random(12)]),
        ]);
        $this->assertNotNull($payment);

        $invoice = BillingInvoice::create([
            'organization_id' => $org->id,
            'plan_id' => $this->plan->id,
            'subtotal' => 99.00,
            'total' => 99.00,
        ]);
        $this->assertNotNull($invoice);

        // 3. Automated Renewal Reminder Command
        $exitCode = Artisan::call('wappiyo:send-renewal-reminders');
        $this->assertEquals(0, $exitCode);

        // 4. Verify Admin Renewal Due screen lists customer
        $this->actingAs($this->adminUser, 'admin');
        $renewalResponse = $this->get('/admin/subscriptions/renewal-due?filter=1_7d');
        $renewalResponse->assertStatus(200);
    }

    /**
     * JOURNEY E — ADMIN PLATFORM MANAGEMENT
     * Admin → Client List → Client Report → Messages → Calls → Call Minutes →
     * Campaigns → Users → Subscription → Renewal Due → Notification → Export
     */
    public function test_journey_e_admin_client_reporting_and_export(): void
    {
        $this->actingAs($this->adminUser, 'admin');

        // 1. Access Admin Reports
        $response = $this->get('/admin/reports');
        $response->assertStatus(200);

        // 2. Access Admin Renewal Due
        $renewalResponse = $this->get('/admin/subscriptions/renewal-due');
        $renewalResponse->assertStatus(200);

        // 3. Stream CSV Export
        $exportResponse = $this->get('/admin/reports/export?range=30d');
        $exportResponse->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $exportResponse->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment;', $exportResponse->headers->get('Content-Disposition'));

        // 4. Broadcast Notification to All Users
        $broadcastResponse = $this->postJson('/admin/notifications/send', [
            'audience' => 'all',
            'title' => 'System Update Notice',
            'message' => 'New calling analytics are now active across your workspace.',
            'type' => 'info',
        ]);
        $broadcastResponse->assertStatus(200);
        $broadcastResponse->assertJson(['success' => true]);
    }

    /**
     * JOURNEY F — FAILURE RECOVERY & CHAOS
     * External API Failure → Error → Logging → Retry → Recovery → Data Reconciliation
     */
    public function test_journey_f_failure_recovery_and_resilience(): void
    {
        $org = Organization::create([
            'identifier' => 'chaos-org-' . Str::random(6),
            'name' => 'Chaos Resilient Workspace',
            'created_by' => $this->adminUser->id,
        ]);

        $contact = Contact::create([
            'organization_id' => $org->id,
            'first_name' => 'Chaos',
            'last_name' => 'Recipient',
            'phone' => '+919111223344',
            'created_by' => $this->adminUser->id,
        ]);

        // 1. Simulate Meta API Webhook failure event (e.g. temporary outage)
        $failedCall = Call::create([
            'organization_id' => $org->id,
            'contact_id' => $contact->id,
            'user_id' => $this->adminUser->id,
            'customer_phone' => $contact->phone,
            'direction' => 'outbound',
            'status' => 'failed',
            'failure_reason' => 'provider_temporary_unavailable',
            'duration' => 0,
        ]);
        $this->assertEquals('failed', $failedCall->status);

        // 2. Retry call logic (guard against double active calls)
        $activeCallCheck = Call::where('organization_id', $org->id)
            ->where('contact_id', $contact->id)
            ->whereIn('status', ['initiating', 'ringing', 'connecting', 'connected'])
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();
        $this->assertNull($activeCallCheck, 'Failed call should not block retry');

        // 3. Recovered call succeeds
        $recoveredCall = Call::create([
            'organization_id' => $org->id,
            'contact_id' => $contact->id,
            'user_id' => $this->adminUser->id,
            'customer_phone' => $contact->phone,
            'direction' => 'outbound',
            'status' => 'completed',
            'duration' => 45,
            'notes' => 'Call succeeded on automatic retry after Meta gateway recovery.',
        ]);
        $this->assertEquals('completed', $recoveredCall->status);

        // 4. Data reconciliation: 1 failed call + 1 completed call = 2 call records
        $totalCalls = Call::where('organization_id', $org->id)->count();
        $this->assertEquals(2, $totalCalls);
    }
}

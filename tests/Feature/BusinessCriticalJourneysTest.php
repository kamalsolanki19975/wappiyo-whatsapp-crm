<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\Template;
use App\Models\User;
use App\Models\ChatNote;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class BusinessCriticalJourneysTest extends TestCase
{
    protected $admin;
    protected $customer;
    protected $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@wappiyo.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $this->customer = User::firstOrCreate(
            ['email' => 'demo@wappiyo.com'],
            [
                'first_name' => 'Demo',
                'last_name' => 'Customer',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );

        $this->organization = Organization::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Acme Technologies Inc.',
                'identifier' => 'acme-tech-' . Str::random(5),
                'created_by' => $this->customer->id,
                'timezone' => 'Asia/Kolkata',
            ]
        );

        // Ensure team membership
        Team::firstOrCreate(
            [
                'user_id' => $this->customer->id,
                'organization_id' => $this->organization->id,
            ],
            [
                'role' => 'owner',
            ]
        );
    }

    /**
     * Journey A — New Customer Registration, Onboarding & Timezone Setup
     */
    public function test_journey_a_new_customer_registration_and_onboarding(): void
    {
        $uniqueEmail = 'new_customer_' . Str::random(6) . '@example.com';
        $orgName = 'Aarav Global Enterprises ' . Str::random(4);

        // 1. Registration with Organization Name
        $registerResponse = $this->post('/register', [
            'first_name' => 'Aarav',
            'last_name' => 'Sharma',
            'email' => $uniqueEmail,
            'organization_name' => $orgName,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'terms' => true,
        ]);
        
        $registerResponse->assertStatus(302);
        
        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user, 'New customer must be registered in the database.');
        $this->assertEquals('user', $user->role);

        // Verify organization created via registration flow
        $newOrg = Organization::where('created_by', $user->id)->first();
        $this->assertNotNull($newOrg, 'New organization must be created during registration.');
        $this->assertEquals('Asia/Kolkata', $newOrg->timezone, 'Default organization timezone must be Asia/Kolkata.');

        // Verify Team Ownership
        $team = Team::where('user_id', $user->id)->where('organization_id', $newOrg->id)->first();
        $this->assertNotNull($team);
        $this->assertEquals('owner', $team->role);

        // Verify Customer Dashboard Access
        $this->actingAs($user, 'user');
        session(['current_organization' => $newOrg->id]);
        $dashResponse = $this->get('/dashboard');
        $dashResponse->assertStatus(200);

        // Verify Onboarding Wizard Access
        $onboardingResponse = $this->get('/onboarding');
        $onboardingResponse->assertStatus(200);
    }

    /**
     * Journey B — Website Lead Capture, Admin Management & CRM Conversion
     */
    public function test_journey_b_website_lead_capture_and_crm_conversion(): void
    {
        $leadEmail = 'lead_' . Str::random(6) . '@enterprise.com';
        $leadPhone = '+91 98' . rand(10000000, 99999999);

        // 1. Visitor submits contact form
        $leadPostResponse = $this->post('/contact', [
            'name' => 'Rajesh Mehta',
            'email' => $leadEmail,
            'phone' => $leadPhone,
            'company' => 'Mehta Logistics Ltd',
            'subject' => 'Enterprise WhatsApp API Trial',
            'message' => 'We are interested in high-volume WhatsApp campaign automations.',
            '_hp_company_url' => '', // Honeypot clean
        ]);
        $leadPostResponse->assertStatus(302);

        // 2. Verify Lead exists in DB with status new
        $lead = Lead::where('email', $leadEmail)->first();
        $this->assertNotNull($lead, 'Lead record should be saved in DB.');
        $this->assertEquals('new', $lead->status);
        $this->assertEquals('Mehta Logistics Ltd', $lead->company);
        $this->assertNotNull($lead->uuid);

        // 3. Admin logs in and views lead list & lead details
        $this->actingAs($this->admin, 'admin');
        $adminLeadsResponse = $this->get('/admin/leads');
        $adminLeadsResponse->assertStatus(200);

        // 4. Admin updates lead status to 'contacted'
        $updateStatusResponse = $this->post('/admin/leads/' . $lead->uuid . '/status', [
            'status' => 'contacted',
        ]);
        $updateStatusResponse->assertStatus(302);

        // Admin adds internal note
        $addNoteResponse = $this->post('/admin/leads/' . $lead->uuid . '/notes', [
            'note' => 'Called Rajesh Mehta. Demo scheduled for tomorrow at 3 PM IST.',
        ]);
        $addNoteResponse->assertStatus(302);

        $lead->refresh();
        $this->assertEquals('contacted', $lead->status);
        $this->assertIsArray($lead->notes);
        $notesContent = json_encode($lead->notes);
        $this->assertStringContainsString('Demo scheduled', $notesContent);

        // 5. Admin converts lead to CRM Contact for target organization
        $convertResponse = $this->post('/admin/leads/' . $lead->uuid . '/convert', [
            'organization_id' => $this->organization->id,
        ]);
        $convertResponse->assertStatus(302);

        $lead->refresh();
        $this->assertEquals('converted', $lead->status);
        $this->assertNotNull($lead->converted_at);

        // Verify Contact was created under the target organization
        $contact = Contact::where('organization_id', $this->organization->id)
            ->where('email', $leadEmail)
            ->first();
        $this->assertNotNull($contact, 'Lead must be converted to an organization CRM contact.');
        $this->assertStringContainsString('Rajesh', $contact->first_name);
    }

    /**
     * Journey C — Customer CRM, Contact & Inbox Conversation Threading
     */
    public function test_journey_c_whatsapp_customer_crm_and_inbox(): void
    {
        $this->actingAs($this->customer, 'user');
        session(['current_organization' => $this->organization->id]);

        // 1. Create a contact
        $contact = Contact::create([
            'organization_id' => $this->organization->id,
            'first_name' => 'Priya',
            'last_name' => 'Nair',
            'phone' => '+919988776655',
            'email' => 'priya.nair@test.com',
            'created_by' => $this->customer->id,
        ]);
        $this->assertNotNull($contact->id);

        // 2. Access Contacts index
        $contactsPage = $this->get('/contacts');
        $contactsPage->assertStatus(200);

        // 3. Access Inbox / Chats index
        $chatsPage = $this->get('/chats');
        $chatsPage->assertStatus(200);

        // 4. Create a ChatNote
        $chatNote = ChatNote::create([
            'contact_id' => $contact->id,
            'created_by' => $this->customer->id,
            'content' => 'Client expressed interest in recurring broadcast messages.',
        ]);
        $this->assertNotNull($chatNote->id);
        $this->assertEquals('Client expressed interest in recurring broadcast messages.', $chatNote->content);
    }

    /**
     * Journey D — Campaign Scheduling with Asia/Kolkata Timezone & Log Tracking
     */
    public function test_journey_d_campaign_scheduling_and_lifecycle(): void
    {
        $this->actingAs($this->customer, 'user');
        session(['current_organization' => $this->organization->id]);

        // 1. Access campaigns index
        $campaignsPage = $this->get('/campaigns');
        $campaignsPage->assertStatus(200);

        // 2. Create a contact group with created_by
        $group = ContactGroup::create([
            'organization_id' => $this->organization->id,
            'name' => 'Festival VIP Segment ' . Str::random(4),
            'created_by' => $this->customer->id,
        ]);
        $this->assertNotNull($group->id);

        // 3. Get existing template or create one
        $template = Template::where('organization_id', $this->organization->id)->first();
        if (!$template) {
            $template = Template::create([
                'organization_id' => $this->organization->id,
                'name' => 'welcome_template_' . Str::random(4),
                'language' => 'en_US',
                'category' => 'UTILITY',
                'status' => 'APPROVED',
                'created_by' => $this->customer->id,
            ]);
        }

        // 4. Create a campaign scheduled in India Standard Time
        $scheduledTimeIst = Carbon::now('Asia/Kolkata')->addHours(2)->format('Y-m-d H:i:s');
        
        $campaign = Campaign::create([
            'organization_id' => $this->organization->id,
            'name' => 'Diwali Special Offer Broadcast ' . Str::random(4),
            'template_id' => $template->id,
            'contact_group_id' => $group->id,
            'metadata' => json_encode(['type' => 'broadcast']),
            'status' => 'scheduled',
            'scheduled_at' => $scheduledTimeIst,
            'created_by' => $this->customer->id,
        ]);
        $this->assertNotNull($campaign->id);
        $this->assertEquals('scheduled', $campaign->status);

        // 5. Create Contact and Campaign Log record
        $contact = Contact::firstOrCreate(
            ['organization_id' => $this->organization->id, 'phone' => '+919999988888'],
            ['first_name' => 'Campaign', 'last_name' => 'Recipient', 'created_by' => $this->customer->id]
        );

        $log = CampaignLog::create([
            'campaign_id' => $campaign->id,
            'contact_id' => $contact->id,
            'status' => 'pending',
        ]);
        $this->assertNotNull($log->id);

        // 6. Test campaign detail view
        $campaignDetail = $this->get('/campaigns/' . $campaign->uuid);
        $this->assertTrue(in_array($campaignDetail->status(), [200, 302]));
    }

    /**
     * Journey E — Automation & Workflow Engine
     */
    public function test_journey_e_automation_workflows(): void
    {
        $this->actingAs($this->customer, 'user');
        session(['current_organization' => $this->organization->id]);

        // Basic automation page
        $autoPage = $this->get('/automation/basic');
        $autoPage->assertStatus(200);

        // Settings automation
        $autoSettings = $this->get('/settings/automation');
        $autoSettings->assertStatus(200);
    }

    /**
     * Journey F — Billing, Subscription Plans, Tax & Invoices
     */
    public function test_journey_f_billing_plans_pricing_and_invoices(): void
    {
        $this->actingAs($this->customer, 'user');
        session(['current_organization' => $this->organization->id]);

        // 1. Billing Overview Page
        $billingPage = $this->get('/billing');
        $billingPage->assertStatus(200);

        // 2. Verify subscription plans exist in DB
        $plans = SubscriptionPlan::all();
        $this->assertGreaterThan(0, $plans->count(), 'Subscription plans must be configured.');

        // 3. Verify Active or Trial Subscription on Acme Demo Org
        $subscription = Subscription::where('organization_id', $this->organization->id)->first();
        if (!$subscription) {
            $plan = SubscriptionPlan::first();
            $subscription = Subscription::create([
                'organization_id' => $this->organization->id,
                'subscription_plan_id' => $plan ? $plan->id : 1,
                'status' => 'active',
                'start_at' => Carbon::now('Asia/Kolkata'),
                'end_at' => Carbon::now('Asia/Kolkata')->addDays(30),
            ]);
        }
        $this->assertNotNull($subscription);
        $this->assertNotNull($subscription->organization);
        $this->assertEquals($this->organization->id, $subscription->organization->id);
    }

    /**
     * Journey G — Super Admin Full Management Suite
     */
    public function test_journey_g_admin_panel_management_suite(): void
    {
        $this->actingAs($this->admin, 'admin');

        $adminRoutes = [
            '/admin/dashboard',
            '/admin/organizations',
            '/admin/users',
            '/admin/leads',
            '/admin/plans',
            '/admin/billing',
            '/admin/addons',
            '/admin/tax-rates',
            '/admin/coupons',
            '/admin/faqs',
            '/admin/settings',
            '/admin/reports',
        ];

        foreach ($adminRoutes as $uri) {
            $resp = $this->get($uri);
            $this->assertEquals(200, $resp->status(), "Admin route {$uri} failed to load.");
        }
    }
}

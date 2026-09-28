<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebsiteLeadTest extends TestCase
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
                'first_name' => 'Super',
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
                'role' => 'customer',
            ]
        );

        $this->organization = Organization::firstOrCreate(
            ['name' => 'Acme Demo Organization'],
            [
                'identifier' => 'acme-demo-' . \Illuminate\Support\Str::random(6),
                'created_by' => $this->customer->id,
            ]
        );
    }

    public function test_guest_cannot_access_admin_leads(): void
    {
        $response = $this->get('/admin/leads');
        $response->assertRedirect('/login');
    }

    public function test_customer_cannot_access_admin_leads(): void
    {
        $this->actingAs($this->customer, 'user');

        $response = $this->get('/admin/leads');
        $response->assertRedirect('/login');
    }

    public function test_website_visitor_can_submit_contact_form_and_create_lead(): void
    {
        $uniqueEmail = 'lead.' . \Illuminate\Support\Str::random(6) . '@example.in';
        $payload = [
            'name' => 'Rajesh Sharma',
            'email' => $uniqueEmail,
            'phone' => '+919876543210',
            'company' => 'Sharma Logistics Pvt Ltd',
            'subject' => 'Enterprise Demo Request',
            'message' => 'We want to connect 20 WhatsApp agents and automate triage.',
            'form_type' => 'demo',
            'page_url' => 'http://localhost:8000/contact?type=demo',
            'utm_source' => 'google_ads',
            'utm_medium' => 'cpc',
            'utm_campaign' => 'q4_enterprise',
            'website_hp' => '', // Honeypot must be empty
        ];

        $response = $this->post('/contact', $payload);

        $response->assertStatus(302);
        $response->assertSessionHas('status', [
            'type' => 'success',
            'message' => 'Thank you! Your request has been received. Our team will contact you shortly.',
        ]);

        $this->assertDatabaseHas('leads', [
            'name' => 'Rajesh Sharma',
            'email' => $uniqueEmail,
            'company' => 'Sharma Logistics Pvt Ltd',
            'form_type' => 'demo',
            'utm_source' => 'google_ads',
            'status' => 'new',
        ]);
    }

    public function test_honeypot_rejects_spam_bot_submission(): void
    {
        $payload = [
            'name' => 'Bot Spammer',
            'email' => 'spam@bot.com',
            'message' => 'Buy cheap links now',
            'website_hp' => 'I am a spam bot', // honeypot filled
        ];

        $response = $this->post('/contact', $payload);

        $response->assertStatus(302);

        $this->assertDatabaseMissing('leads', [
            'email' => 'spam@bot.com',
        ]);
    }

    public function test_duplicate_submission_within_60_seconds_is_prevented(): void
    {
        $ip = '192.168.10.55';
        $payload = [
            'name' => 'Duplicate Tester',
            'email' => 'duplicate@tester.com',
            'subject' => 'Duplicate Test Subject',
            'message' => 'Hello team',
            'website_hp' => '',
        ];

        // First submission succeeds
        $this->serverVariables = ['REMOTE_ADDR' => $ip];
        $response1 = $this->post('/contact', $payload);
        $response1->assertSessionHas('status');

        // Immediate second submission triggers duplicate warning
        $response2 = $this->post('/contact', $payload);
        $response2->assertSessionHas('status', [
            'type' => 'warning',
            'message' => 'We already received your request a moment ago. Our team is already reviewing it!',
        ]);
    }

    public function test_admin_can_view_lead_list_and_details(): void
    {
        $lead = Lead::create([
            'name' => 'Vikram Patel',
            'email' => 'vikram@patelenterprises.com',
            'phone' => '+919876543211',
            'company' => 'Patel Enterprises',
            'message' => 'Interested in broadcast marketing.',
            'status' => 'new',
            'form_type' => 'contact',
            'lead_source' => 'Website Contact Form',
        ]);

        $this->actingAs($this->admin, 'admin');

        $listResponse = $this->get('/admin/leads');
        $listResponse->assertStatus(200);

        $showResponse = $this->get("/admin/leads/{$lead->uuid}");
        $showResponse->assertStatus(200);
    }

    public function test_admin_can_update_status_and_add_notes(): void
    {
        $lead = Lead::create([
            'name' => 'Sneha Roy',
            'email' => 'sneha@royconsulting.com',
            'message' => 'Consulting inquiry',
            'status' => 'new',
            'lead_source' => 'Website Contact Form',
        ]);

        $this->actingAs($this->admin, 'admin');

        // Update status
        $statusResponse = $this->put("/admin/leads/{$lead->uuid}/status", [
            'status' => 'contacted',
        ]);
        $statusResponse->assertStatus(302);
        $this->assertEquals('contacted', $lead->fresh()->status);

        // Add note
        $noteResponse = $this->post("/admin/leads/{$lead->uuid}/notes", [
            'note' => 'Spoke with Sneha on phone. Scheduled product demo for Friday 3 PM.',
        ]);
        $noteResponse->assertStatus(302);

        $freshLead = $lead->fresh();
        $this->assertNotEmpty($freshLead->notes);
        $notesText = collect($freshLead->notes)->pluck('text')->join(' ');
        $this->assertStringContainsString('Spoke with Sneha', $notesText);
    }

    public function test_admin_can_convert_lead_to_crm_contact(): void
    {
        $lead = Lead::create([
            'name' => 'Aarav Mehta',
            'email' => 'aarav.mehta@techcorp.in',
            'phone' => '+919988776655',
            'company' => 'TechCorp India',
            'message' => 'Ready to onboard.',
            'status' => 'qualified',
            'lead_source' => 'Website Demo Request',
        ]);

        $this->actingAs($this->admin, 'admin');

        $convertResponse = $this->post("/admin/leads/{$lead->uuid}/convert", [
            'organization_id' => $this->organization->id,
        ]);

        $convertResponse->assertStatus(302);
        $convertResponse->assertSessionHas('status', [
            'type' => 'success',
            'message' => 'Lead converted to CRM contact successfully!',
        ]);

        $freshLead = $lead->fresh();
        $this->assertEquals('converted', $freshLead->status);
        $this->assertEquals($this->organization->id, $freshLead->converted_to_organization_id);
        $this->assertNotNull($freshLead->converted_at);

        // Contact created in CRM
        $this->assertDatabaseHas('contacts', [
            'organization_id' => $this->organization->id,
            'email' => 'aarav.mehta@techcorp.in',
            'phone' => '+919988776655',
        ]);
    }
}

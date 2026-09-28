<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Tests\TestCase;

class FullUserJourneyTest extends TestCase
{
    protected $user;
    protected $admin;
    protected $org;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::where('email', 'demo@wappiyo.com')->first();
        $this->admin = User::where('email', 'admin@wappiyo.com')->first();
        $this->org = Organization::find(1);
    }

    public function test_customer_full_journey(): void
    {
        $this->actingAs($this->user, 'user');
        session(['current_organization' => $this->org->id]);

        $routes = [
            '/dashboard',
            '/contacts',
            '/contact-groups',
            '/chats',
            '/campaigns',
            '/templates',
            '/automation/basic',
            '/analytics',
            '/reports',
            '/team',
            '/settings',
            '/settings/whatsapp',
            '/settings/contacts',
            '/settings/automation',
            '/billing',
        ];

        foreach ($routes as $uri) {
            $response = $this->get($uri);
            $response->assertStatus(200);
        }
    }

    public function test_admin_full_journey(): void
    {
        $this->actingAs($this->admin, 'admin');

        $routes = [
            '/admin/dashboard',
            '/admin/organizations',
            '/admin/users',
            '/admin/plans',
            '/admin/billing',
            '/admin/addons',
            '/admin/tax-rates',
            '/admin/coupons',
            '/admin/faqs',
            '/admin/settings',
            '/admin/reports',
        ];

        foreach ($routes as $uri) {
            $response = $this->get($uri);
            $response->assertStatus(200);
        }
    }
}

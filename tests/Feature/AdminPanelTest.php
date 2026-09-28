<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    protected $admin;

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
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_billing_index_without_crash(): void
    {
        $this->actingAs($this->admin, 'admin');

        $response = $this->get('/admin/billing');
        $response->assertStatus(200);
    }

    public function test_admin_can_delete_subscription_plan_and_receives_redirect(): void
    {
        $plan = SubscriptionPlan::create([
            'name' => 'Test Disposable Plan',
            'price' => 29.00,
            'period' => 'monthly',
            'status' => 'active',
            'metadata' => json_encode(['description' => 'Test plan']),
        ]);

        $this->actingAs($this->admin, 'admin');

        $response = $this->delete("/admin/plans/{$plan->uuid}");

        // Must return redirect with status flash message
        $response->assertStatus(302);
        $response->assertSessionHas('status');

        $plan->refresh();
        $this->assertNotNull($plan->deleted_at);
    }
}

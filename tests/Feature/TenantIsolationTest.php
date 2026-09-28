<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\ContactField;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    protected $orgA;
    protected $orgB;
    protected $userA;
    protected $userB;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Org A and User A
        $this->userA = User::firstOrCreate(
            ['email' => 'test_user_a@wappiyo.com'],
            [
                'first_name' => 'User',
                'last_name' => 'Alpha',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
        $this->userA->email_verified_at = now();
        $this->userA->save();

        $this->orgA = Organization::firstOrCreate(
            ['identifier' => 'test-tenant-a-org'],
            [
                'name' => 'Tenant Alpha Corp',
                'created_by' => $this->userA->id,
            ]
        );

        Team::firstOrCreate(
            ['organization_id' => $this->orgA->id, 'user_id' => $this->userA->id],
            ['role' => 'owner', 'status' => 'active', 'created_by' => $this->userA->id]
        );

        \App\Models\Subscription::firstOrCreate(
            ['organization_id' => $this->orgA->id],
            [
                'plan_id' => 1,
                'status' => 'active',
                'start_date' => now(),
                'valid_until' => now()->addDays(30),
            ]
        );

        // Setup Org B and User B
        $this->userB = User::firstOrCreate(
            ['email' => 'test_user_b@wappiyo.com'],
            [
                'first_name' => 'User',
                'last_name' => 'Beta',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
        $this->userB->email_verified_at = now();
        $this->userB->save();

        $this->orgB = Organization::firstOrCreate(
            ['identifier' => 'test-tenant-b-org'],
            [
                'name' => 'Tenant Beta LLC',
                'created_by' => $this->userB->id,
            ]
        );

        Team::firstOrCreate(
            ['organization_id' => $this->orgB->id, 'user_id' => $this->userB->id],
            ['role' => 'owner', 'status' => 'active', 'created_by' => $this->userB->id]
        );

        \App\Models\Subscription::firstOrCreate(
            ['organization_id' => $this->orgB->id],
            [
                'plan_id' => 1,
                'status' => 'active',
                'start_date' => now(),
                'valid_until' => now()->addDays(30),
            ]
        );
    }

    public function test_tenant_b_cannot_update_tenant_a_contact_group(): void
    {
        $groupA = ContactGroup::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Org A VIP Customers',
            'created_by' => $this->userA->id,
        ]);

        $this->actingAs($this->userB, 'user');
        session(['current_organization' => $this->orgB->id]);

        $response = $this->post("/contact-groups/{$groupA->uuid}", [
            'name' => 'Hacked Group Name',
        ]);

        // Expect 404 ModelNotFound because group is scoped to current organization
        $response->assertStatus(404);

        $groupA->refresh();
        $this->assertEquals('Org A VIP Customers', $groupA->name);
    }

    public function test_tenant_b_cannot_delete_tenant_a_contact_group(): void
    {
        $groupA = ContactGroup::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Org A Protected Group',
            'created_by' => $this->userA->id,
        ]);

        $this->actingAs($this->userB, 'user');
        session(['current_organization' => $this->orgB->id]);

        $response = $this->delete('/contact-groups', [
            'uuids' => [$groupA->uuid],
        ]);

        $groupA->refresh();
        $this->assertNull($groupA->deleted_at);
        $this->assertDatabaseHas('contact_groups', ['id' => $groupA->id, 'deleted_at' => null]);
    }

    public function test_tenant_b_cannot_delete_tenant_a_contact_field(): void
    {
        $fieldA = ContactField::create([
            'organization_id' => $this->orgA->id,
            'name' => 'Customer Tier',
            'type' => 'input',
            'position' => 1,
            'required' => false,
        ]);

        $this->actingAs($this->userB, 'user');
        session(['current_organization' => $this->orgB->id]);

        // Delete field using uuid
        $this->delete("/contact-fields/{$fieldA->uuid}");

        $fieldA->refresh();
        $this->assertNull($fieldA->deleted_at);
    }

    public function test_tenant_b_cannot_add_note_to_tenant_a_contact(): void
    {
        $contactA = Contact::create([
            'organization_id' => $this->orgA->id,
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'phone' => '+15550001111',
            'created_by' => $this->userA->id,
        ]);

        $this->actingAs($this->userB, 'user');
        session(['current_organization' => $this->orgB->id]);

        $response = $this->post('/notes', [
            'contact' => $contactA->uuid,
            'notes' => 'Unauthorized note attempt by Org B',
        ]);

        // Expect 404 ModelNotFound because contact is scoped to current organization
        $response->assertStatus(404);

        $this->assertDatabaseMissing('chat_notes', [
            'contact_id' => $contactA->id,
            'content' => 'Unauthorized note attempt by Org B',
        ]);
    }

    public function test_non_owner_agent_cannot_update_organization_profile(): void
    {
        $agentUser = User::firstOrCreate(
            ['email' => 'agent_user@wappiyo.com'],
            [
                'first_name' => 'Agent',
                'last_name' => 'Support',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
        $agentUser->email_verified_at = now();
        $agentUser->save();

        Team::updateOrCreate(
            ['organization_id' => $this->orgA->id, 'user_id' => $agentUser->id],
            ['role' => 'agent', 'status' => 'active', 'created_by' => $this->userA->id]
        );

        $this->actingAs($agentUser, 'user');
        session(['current_organization' => $this->orgA->id]);

        $response = $this->put('/profile/organization', [
            'organization_name' => 'Hijacked Company Name',
            'address' => '123 Fake St',
            'city' => 'Anytown',
            'state' => 'NY',
            'zip' => '10001',
            'country' => 'US',
            'timezone' => 'America/New_York',
        ]);

        $response->assertStatus(403);

        $this->orgA->refresh();
        $this->assertEquals('Tenant Alpha Corp', $this->orgA->name);
    }
}

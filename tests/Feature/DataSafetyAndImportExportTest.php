<?php

namespace Tests\Feature;

use App\Exports\ContactGroupsExport;
use App\Exports\ContactsExport;
use App\Imports\ContactsImport;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use App\Services\ContactService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class DataSafetyAndImportExportTest extends TestCase
{
    protected $org;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::firstOrCreate(
            ['email' => 'datasafety_tester@wappiyo.com'],
            [
                'first_name' => 'Safety',
                'last_name' => 'Tester',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
        $this->user->email_verified_at = now();
        $this->user->save();

        $this->org = Organization::firstOrCreate(
            ['identifier' => 'data-safety-org'],
            [
                'name' => 'Data Safety Org',
                'created_by' => $this->user->id,
                'metadata' => json_encode(['timezone' => 'Asia/Kolkata']),
            ]
        );

        $subscription = Subscription::firstOrNew(['organization_id' => $this->org->id]);
        $subscription->status = 'active';
        $subscription->valid_until = now()->addMonths(6);
        $subscription->plan_id = 1;
        $subscription->save();

        \App\Models\Team::firstOrCreate(
            ['user_id' => $this->user->id, 'organization_id' => $this->org->id],
            ['role' => 'owner', 'created_by' => $this->user->id]
        );

        session()->put('current_organization', $this->org->id);
    }

    /** @test */
    public function empty_uuids_without_explicit_all_flag_does_not_delete_contacts()
    {
        // Create 3 contacts
        $c1 = Contact::create([
            'organization_id' => $this->org->id,
            'first_name' => 'Alice',
            'phone' => '+919876543210',
            'uuid' => (string) Str::uuid(),
            'created_by' => $this->user->id,
        ]);
        $c2 = Contact::create([
            'organization_id' => $this->org->id,
            'first_name' => 'Bob',
            'phone' => '+919876543211',
            'uuid' => (string) Str::uuid(),
            'created_by' => $this->user->id,
        ]);

        $service = new ContactService($this->org->id);
        $result = $service->delete([], false);

        $this->assertEmpty($result);
        $this->assertDatabaseHas('contacts', ['id' => $c1->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('contacts', ['id' => $c2->id, 'deleted_at' => null]);
    }

    /** @test */
    public function contact_controller_delete_rejects_empty_uuids_without_all_flag()
    {
        $c1 = Contact::create([
            'organization_id' => $this->org->id,
            'first_name' => 'Charlie',
            'phone' => '+919876543212',
            'uuid' => (string) Str::uuid(),
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->delete('/contacts', ['uuids' => []]);

        $response->assertSessionHas('status.type', 'error');
        $this->assertDatabaseHas('contacts', ['id' => $c1->id, 'deleted_at' => null]);
    }

    /** @test */
    public function contact_group_controller_delete_rejects_empty_uuids_without_all_flag()
    {
        $group = ContactGroup::create([
            'organization_id' => $this->org->id,
            'name' => 'VIP Clients',
            'uuid' => (string) Str::uuid(),
            'created_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user, 'user')
            ->withSession(['current_organization' => $this->org->id])
            ->delete('/contact-groups', ['uuids' => []]);

        $response->assertSessionHas('status.type', 'error');
        $this->assertDatabaseHas('contact_groups', ['id' => $group->id, 'deleted_at' => null]);
    }

    /** @test */
    public function contacts_import_handles_empty_or_null_phone_without_type_error()
    {
        $importer = new ContactsImport();

        // Row with null phone
        $model = $importer->model([
            'first_name' => 'NullPhone',
            'last_name' => 'Test',
            'phone' => null,
            'email' => 'nullphone@test.com',
        ]);

        $this->assertNull($model);
        $this->assertGreaterThan(0, $importer->getFailedImportsDueToFormat());

        // Row with empty string phone
        $modelEmpty = $importer->model([
            'first_name' => 'EmptyPhone',
            'last_name' => 'Test',
            'phone' => '',
            'email' => 'emptyphone@test.com',
        ]);

        $this->assertNull($modelEmpty);
    }

    /** @test */
    public function contacts_export_sanitizes_csv_formula_injection()
    {
        $maliciousContact = Contact::create([
            'organization_id' => $this->org->id,
            'first_name' => '=SUM(1+1)',
            'last_name' => '@ATTACK',
            'phone' => '+919876543299',
            'email' => '+malicious@test.com',
            'uuid' => (string) Str::uuid(),
            'created_by' => $this->user->id,
        ]);

        session()->put('current_organization', $this->org->id);

        $export = new ContactsExport();
        $collection = $export->collection();

        $exportedRow = $collection->firstWhere('first_name', "'=SUM(1+1)");
        $this->assertNotNull($exportedRow, 'Leading = formula should be prepended with a single quote');
        $this->assertEquals("'@ATTACK", $exportedRow['last_name']);
        $this->assertEquals("'+malicious@test.com", $exportedRow['email']);
    }

    /** @test */
    public function contact_groups_export_sanitizes_csv_formula_injection()
    {
        $maliciousGroup = ContactGroup::create([
            'organization_id' => $this->org->id,
            'name' => '=2+5*cmd',
            'uuid' => (string) Str::uuid(),
            'created_by' => $this->user->id,
        ]);

        session()->put('current_organization', $this->org->id);

        $export = new ContactGroupsExport();
        $collection = $export->collection();

        $exportedRow = $collection->firstWhere('group_name', "'=2+5*cmd");
        $this->assertNotNull($exportedRow, 'Leading = formula in group name should be prepended with a single quote');
    }
}

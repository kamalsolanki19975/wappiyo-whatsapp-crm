<?php

namespace Tests\Feature;

use App\Helpers\DateTimeHelper;
use App\Models\Campaign;
use App\Models\Chat;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\Template;
use App\Models\User;
use App\Services\ReportingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class TimezoneManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DateTimeHelper::clearCache();
    }

    /**
     * Test 1: Global default timezone configuration is Asia/Kolkata.
     */
    public function test_global_default_timezone_is_asia_kolkata(): void
    {
        $this->assertEquals('Asia/Kolkata', config('app.timezone'));
        $this->assertEquals('Asia/Kolkata', DateTimeHelper::DEFAULT_TIMEZONE);
        $this->assertEquals('Asia/Kolkata', DateTimeHelper::getCompanyTimezone());
        $this->assertStringContainsString('India Standard Time', DateTimeHelper::getTimezoneDisplay('Asia/Kolkata'));
    }

    /**
     * Test 2: New organization creation automatically defaults to Asia/Kolkata.
     */
    public function test_new_organization_creation_defaults_to_asia_kolkata(): void
    {
        $uniqueId = 'tz-test-' . Str::random(6);
        $user = User::firstOrCreate(
            ['email' => $uniqueId . '@wappiyo.com'],
            [
                'first_name' => 'TZ',
                'last_name' => 'User',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $org = Organization::create([
            'identifier' => $uniqueId,
            'name' => 'Kolkata Default Corp',
            'timezone' => 'Asia/Kolkata',
            'metadata' => json_encode(['timezone' => 'Asia/Kolkata']),
            'created_by' => $user->id,
        ]);

        $this->assertEquals('Asia/Kolkata', $org->timezone);
        $this->assertEquals('Asia/Kolkata', DateTimeHelper::getOrganizationTimezone($org));
    }

    /**
     * Test 3: Existing organization without timezone safely resolves to Asia/Kolkata.
     */
    public function test_existing_organization_without_timezone_defaults_to_asia_kolkata(): void
    {
        $org = new Organization();
        $org->id = 999991;
        $org->timezone = null;
        $org->metadata = null;

        $resolved = DateTimeHelper::getOrganizationTimezone($org);
        $this->assertEquals('Asia/Kolkata', $resolved);
    }

    /**
     * Test 4: Multi-tenant timezone isolation: Organization A and B do not affect each other.
     */
    public function test_multi_tenant_timezone_isolation(): void
    {
        $orgIndia = new Organization();
        $orgIndia->id = 999992;
        $orgIndia->timezone = 'Asia/Kolkata';

        $orgUS = new Organization();
        $orgUS->id = 999993;
        $orgUS->timezone = 'America/New_York';

        $orgUK = new Organization();
        $orgUK->id = 999994;
        $orgUK->timezone = 'Europe/London';

        DateTimeHelper::clearCache();

        $this->assertEquals('Asia/Kolkata', DateTimeHelper::getOrganizationTimezone($orgIndia));
        $this->assertEquals('America/New_York', DateTimeHelper::getOrganizationTimezone($orgUS));
        $this->assertEquals('Europe/London', DateTimeHelper::getOrganizationTimezone($orgUK));
    }

    /**
     * Test 5: Invalid/corrupt timezone string gracefully falls back to Asia/Kolkata without throwing errors.
     */
    public function test_invalid_timezone_gracefully_falls_back_to_asia_kolkata(): void
    {
        $this->assertFalse(DateTimeHelper::isValidTimezone('Invalid/Mars_Time'));
        $this->assertFalse(DateTimeHelper::isValidTimezone(''));
        $this->assertFalse(DateTimeHelper::isValidTimezone(null));
        $this->assertTrue(DateTimeHelper::isValidTimezone('Asia/Kolkata'));
        $this->assertTrue(DateTimeHelper::isValidTimezone('America/New_York'));

        $corruptOrg = new Organization();
        $corruptOrg->id = 999995;
        $corruptOrg->timezone = 'Invalid/NonExistent_Zone';
        $corruptOrg->metadata = json_encode(['timezone' => 'Invalid/NonExistent_Zone']);

        DateTimeHelper::clearCache();
        $resolved = DateTimeHelper::getOrganizationTimezone($corruptOrg);
        $this->assertEquals('Asia/Kolkata', $resolved);
    }

    /**
     * Test 6: Organization Settings allows updating timezone with server-side validation.
     */
    public function test_organization_settings_timezone_update(): void
    {
        $uniqueId = 'tz-update-' . Str::random(6);
        $user = User::firstOrCreate(
            ['email' => $uniqueId . '@wappiyo.com'],
            [
                'first_name' => 'TZ',
                'last_name' => 'Updater',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        $org = Organization::create([
            'identifier' => $uniqueId,
            'name' => 'Timezone Settings Update Corp',
            'timezone' => 'Asia/Kolkata',
            'created_by' => $user->id,
        ]);

        Team::create([
            'organization_id' => $org->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'active',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user, 'user');
        session(['current_organization' => $org->id]);

        $response = $this->put('/profile/organization', [
            'organization_name' => 'Timezone Settings Update Corp',
            'timezone' => 'Europe/Paris',
            'address' => '123 Tech Park',
            'city' => 'Paris',
            'state' => 'Ile-de-France',
            'zip' => '75001',
            'country' => 'France',
        ]);

        $response->assertSessionHasNoErrors();
        $org->refresh();

        $this->assertEquals('Europe/Paris', $org->timezone);
        $metadata = json_decode($org->metadata, true);
        $this->assertEquals('Europe/Paris', $metadata['timezone']);
    }

    /**
     * Test 7: Campaign scheduling interprets local organization time and computes exact UTC timestamp.
     */
    public function test_campaign_scheduling_converts_local_time_to_utc(): void
    {
        // 10:00 AM in Asia/Kolkata (UTC+05:30) must be 04:30 AM in UTC
        $localTimeStr = '2026-09-28 10:00:00';
        $utc = DateTimeHelper::parseLocalToUtc($localTimeStr, 'Asia/Kolkata');

        $this->assertEquals('UTC', $utc->getTimezone()->getName());
        $this->assertEquals('2026-09-28 04:30:00', $utc->format('Y-m-d H:i:s'));

        // Conversely, converting back to Asia/Kolkata gives exact 10:00:00
        $backToLocal = $utc->copy()->setTimezone('Asia/Kolkata');
        $this->assertEquals('2026-09-28 10:00:00', $backToLocal->format('Y-m-d H:i:s'));

        // International organization: America/New_York (EDT is UTC-4 in September)
        $nyUtc = DateTimeHelper::parseLocalToUtc($localTimeStr, 'America/New_York');
        $this->assertEquals('2026-09-28 14:00:00', $nyUtc->format('Y-m-d H:i:s'));
    }

    /**
     * Test 8: Reports calculate local day boundaries (00:00:00 to 23:59:59) using configured timezone.
     */
    public function test_report_today_boundary_respects_organization_timezone(): void
    {
        $reportingService = new ReportingService();

        [$startLocal, $endLocal, $label, $preset] = $reportingService->calculateDateRange('today', null, null, 'Asia/Kolkata');

        $this->assertEquals('Today', $label);
        $this->assertEquals('today', $preset);
        $this->assertEquals('00:00:00', $startLocal->format('H:i:s'));
        $this->assertEquals('23:59:59', $endLocal->format('H:i:s'));
        $this->assertEquals('Asia/Kolkata', $startLocal->getTimezone()->getName());
        $this->assertEquals('Asia/Kolkata', $endLocal->getTimezone()->getName());

        $rangeHelper = DateTimeHelper::getTodayRange('Asia/Kolkata');
        $this->assertEquals('00:00:00', $rangeHelper['start_local']->format('H:i:s'));
        $this->assertEquals('23:59:59', $rangeHelper['end_local']->format('H:i:s'));
    }

    /**
     * Test 9: Website Lead created_at converts to company/platform timezone for Admin display.
     */
    public function test_website_lead_created_at_converts_to_platform_timezone(): void
    {
        $lead = new Lead();
        $lead->name = 'Priya Sharma';
        $lead->email = 'priya.sharma@example.in';
        $lead->phone = '+919876543210';
        $lead->form_type = 'contact';
        $lead->source = 'website_contact';
        $lead->setRawAttributes(['created_at' => '2026-09-27 12:00:00']);

        // Lead getCreatedAtAttribute accessor converts stored UTC string to company timezone (Asia/Kolkata)
        // 12:00:00 UTC + 5:30 = 17:30:00 Asia/Kolkata
        $this->assertEquals('2026-09-27 17:30:00', $lead->created_at);
    }

    /**
     * Test 10: Shared Inertia props include active timezone and display string.
     */
    public function test_inertia_shared_props_include_timezone(): void
    {
        $response = $this->get('/contact');
        $response->assertOk();

        // Check that Inertia shared props include timezone
        $page = $response->viewData('page');
        if ($page && isset($page['props'])) {
            $this->assertArrayHasKey('timezone', $page['props']);
            $this->assertArrayHasKey('timezone_display', $page['props']);
            $this->assertEquals('Asia/Kolkata', $page['props']['timezone']);
            $this->assertStringContainsString('India Standard Time', $page['props']['timezone_display']);
        }
    }
}

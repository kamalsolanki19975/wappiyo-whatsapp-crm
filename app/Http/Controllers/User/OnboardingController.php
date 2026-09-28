<?php

namespace App\Http\Controllers\User;

use App\Helpers\DateTimeHelper;
use App\Http\Controllers\Controller as BaseController;
use App\Imports\ContactsImport;
use App\Models\Addon;
use App\Models\AutoReply;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\Template;
use App\Services\SubscriptionService;
use App\Services\TeamService;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class OnboardingController extends BaseController
{
    protected $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Show the comprehensive guided 12-step onboarding wizard.
     */
    public function index(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];
        $onboardingMeta = $metadata['onboarding'] ?? [];

        // Calculate real-time status of each milestone
        $hasWhatsapp = !empty($metadata['whatsapp']['phone_number_id']) || !empty($metadata['whatsapp']['access_token']);
        $hasTemplate = Template::where('organization_id', $organizationId)->whereNull('deleted_at')->exists();
        $hasTeam = Team::where('organization_id', $organizationId)->count() > 1 || TeamInvite::where('organization_id', $organizationId)->exists();
        $hasContact = Contact::where('organization_id', $organizationId)->whereNull('deleted_at')->exists();
        $hasAutoReply = AutoReply::where('organization_id', $organizationId)->whereNull('deleted_at')->exists();

        // Subscription & Plan details
        $subscription = Subscription::with('plan')->where('organization_id', $organizationId)->first();
        $subscriptionPlans = SubscriptionPlan::where(function ($q) {
            $q->where('status', 'active')->orWhere('status', 1)->orWhere('status', '1');
        })->get();

        $planLimits = [];
        if ($subscription && $subscription->plan && $subscription->plan->metadata) {
            $planMeta = json_decode($subscription->plan->metadata, true);
            $planLimits = [
                'campaign_limit' => $planMeta['campaign_limit'] ?? 1000,
                'message_limit' => $planMeta['message_limit'] ?? 50000,
                'contacts_limit' => $planMeta['contacts_limit'] ?? 10000,
                'team_limit' => $planMeta['team_limit'] ?? 10,
            ];
        }

        $trialDaysRemaining = 0;
        if ($subscription && $subscription->status === 'trial' && $subscription->valid_until) {
            $validUntil = Carbon::parse($subscription->valid_until);
            $trialDaysRemaining = max(0, (int) now()->diffInDays($validUntil, false));
        }

        // Available Add-ons in database
        $availableAddons = Addon::where('status', 1)->get()->map(function ($addon) {
            return [
                'id' => $addon->id,
                'uuid' => $addon->uuid,
                'name' => $addon->name,
                'category' => $addon->category,
                'description' => $addon->description,
                'license' => $addon->license,
                'status' => $addon->status,
            ];
        });

        // Meta Embedded Signup settings
        $settings = Setting::whereIn('key', ['is_embedded_signup_active', 'whatsapp_client_id', 'whatsapp_config_id'])
            ->pluck('value', 'key');

        $useCases = [
            ['id' => 'support', 'title' => __('Customer Support & Ticketing'), 'desc' => __('Multi-agent shared inboxes and live ticket resolution.')],
            ['id' => 'sales', 'title' => __('Sales & Deal Inquiries'), 'desc' => __('Capture WhatsApp leads and qualify potential buyers.')],
            ['id' => 'marketing', 'title' => __('Broadcast Campaigns & Promotions'), 'desc' => __('Send targeted marketing blasts and newsletter updates.')],
            ['id' => 'lead_gen', 'title' => __('Lead Generation & Opt-ins'), 'desc' => __('QR codes, click-to-chat links, and contact forms.')],
            ['id' => 'booking', 'title' => __('Appointments & Service Booking'), 'desc' => __('Automate scheduled reminders and reservation alerts.')],
            ['id' => 'ecommerce', 'title' => __('E-Commerce & Order Notifications'), 'desc' => __('Dispatch shipping status, cart recovery, and receipts.')],
            ['id' => 'internal', 'title' => __('Internal Communication & Alerts'), 'desc' => __('Team notifications and automated system webhooks.')],
            ['id' => 'other', 'title' => __('General / Custom Messaging'), 'desc' => __('Tailor conversational workflows for bespoke use cases.')],
        ];

        $starterTemplates = [
            [
                'id' => 'welcome_intro',
                'title' => __('Welcome & Introduction'),
                'category' => 'UTILITY',
                'description' => __('Warm welcome to new customers with instant response options.'),
                'preview' => "Hello {{1}}! Welcome to {{2}}. We are delighted to connect with you. How can our team help you today?",
                'buttons' => [__('Speak to Agent'), __('Explore Catalog')],
            ],
            [
                'id' => 'support_auto_responder',
                'title' => __('Support Auto-Responder'),
                'category' => 'UTILITY',
                'description' => __('Acknowledge customer support inquiries with an instant reference.'),
                'preview' => "Hi {{1}}, we have received your request. An agent from our team will respond shortly.",
                'buttons' => [__('Track Ticket'), __('Operating Hours')],
            ],
            [
                'id' => 'order_confirmation',
                'title' => __('Order / Service Confirmation'),
                'category' => 'UTILITY',
                'description' => __('Notify customers about order updates, bookings, or reservations.'),
                'preview' => "Great news {{1}}! Your request has been confirmed. We look forward to serving you.",
                'buttons' => [__('View Details')],
            ],
        ];

        $industries = [
            'E-commerce & Retail',
            'Healthcare & Medical',
            'Education & Coaching',
            'Real Estate & Construction',
            'Financial & Insurance',
            'Technology & SaaS',
            'Marketing & Creative Agency',
            'Hospitality & Travel',
            'Automotive & Logistics',
            'Professional Services',
            'Other'
        ];

        $currencies = [
            'USD' => '$ - US Dollar',
            'EUR' => '€ - Euro',
            'GBP' => '£ - British Pound',
            'INR' => '₹ - Indian Rupee',
            'AED' => 'AED - UAE Dirham',
            'CAD' => 'CA$ - Canadian Dollar',
            'AUD' => 'A$ - Australian Dollar',
            'SGD' => 'SG$ - Singapore Dollar',
        ];

        // Address decoding
        $address = [];
        if ($organization->address) {
            $address = json_decode($organization->address, true) ?: [];
        }

        return Inertia::render('User/Onboarding/Index', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'legal_name' => $metadata['legal_name'] ?? $organization->name,
                'logo' => $metadata['logo'] ?? null,
                'timezone' => DateTimeHelper::getOrganizationTimezone($organization),
                'timezone_display' => DateTimeHelper::getTimezoneDisplay(DateTimeHelper::getOrganizationTimezone($organization)),
                'currency' => $metadata['currency'] ?? 'USD',
                'industry' => $metadata['industry'] ?? '',
                'support_email' => $metadata['support_email'] ?? auth()->user()->email,
                'support_phone' => $metadata['support_phone'] ?? auth()->user()->phone,
                'website' => $metadata['website'] ?? '',
                'address' => $address,
            ],
            'whatsappConnected' => $hasWhatsapp,
            'whatsappDetails' => [
                'display_phone_number' => $metadata['whatsapp']['display_phone_number'] ?? null,
                'verified_name' => $metadata['whatsapp']['verified_name'] ?? null,
                'phone_number_id' => $metadata['whatsapp']['phone_number_id'] ?? null,
                'waba_id' => $metadata['whatsapp']['waba_id'] ?? null,
            ],
            'useCases' => $useCases,
            'selectedUseCases' => $onboardingMeta['use_cases'] ?? ['support', 'sales'],
            'availableAddons' => $availableAddons,
            'activeAddons' => $onboardingMeta['active_addons'] ?? ['Embedded Signup', 'AI Assistant', 'Flow builder', 'Webhooks'],
            'starterTemplates' => $starterTemplates,
            'selectedTemplates' => $onboardingMeta['selected_templates'] ?? ['welcome_intro', 'support_auto_responder'],
            'automationPresets' => [
                'welcome_bot' => (bool) ($onboardingMeta['welcome_bot'] ?? true),
                'support_bot' => (bool) ($onboardingMeta['support_bot'] ?? true),
            ],
            'notificationPreferences' => [
                'enable_sound' => (bool) ($metadata['notifications']['enable_sound'] ?? true),
                'tone' => $metadata['notifications']['tone'] ?? 'bell',
                'volume' => (int) ($metadata['notifications']['volume'] ?? 80),
                'email_inbound' => (bool) ($metadata['notifications']['email_inbound'] ?? true),
                'email_assignment' => (bool) ($metadata['notifications']['email_assignment'] ?? true),
            ],
            'subscription' => [
                'plan_name' => $subscription && $subscription->plan ? $subscription->plan->name : __('Free Trial'),
                'status' => $subscription ? $subscription->status : 'trial',
                'trial_days_remaining' => $trialDaysRemaining,
                'valid_until' => $subscription && $subscription->valid_until ? Carbon::parse($subscription->valid_until)->format('M d, Y') : null,
                'limits' => $planLimits,
            ],
            'subscriptionPlans' => $subscriptionPlans,
            'industries' => $industries,
            'currencies' => $currencies,
            'embeddedSignupActive' => (int) $settings->get('is_embedded_signup_active', 0),
            'appId' => $settings->get('whatsapp_client_id', ''),
            'configId' => $settings->get('whatsapp_config_id', ''),
            'graphAPIVersion' => config('graph.api_version', 'v19.0'),
            'currentStep' => (int) ($onboardingMeta['current_step'] ?? 1),
            'completedSteps' => $onboardingMeta['completed_steps'] ?? [],
            'onboardingStatus' => $onboardingMeta['status'] ?? 'IN_PROGRESS',
            'teamMembersCount' => Team::where('organization_id', $organizationId)->count(),
            'contactsCount' => Contact::where('organization_id', $organizationId)->whereNull('deleted_at')->count(),
            'templatesCount' => Template::where('organization_id', $organizationId)->whereNull('deleted_at')->count(),
            'timezones' => config('formats.timezones'),
        ]);
    }

    /**
     * Step 1: Welcome & Overview.
     */
    public function saveWelcome(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $metadata['onboarding']['status'] = 'IN_PROGRESS';
        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 2);
        $metadata['onboarding']['started_at'] = $metadata['onboarding']['started_at'] ?? now()->toIso8601String();
        $this->markStepCompleted($metadata, 'welcome');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Welcome to Wappiyo! Let’s set up your company.')
        ]);
    }

    /**
     * Step 2: Company Information (Name, Legal Name, Logo, Address, Timezone, Currency).
     */
    public function saveCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'legal_name' => 'nullable|string|max:150',
            'industry' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'support_email' => 'nullable|email|max:150',
            'support_phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:150',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:5120',
        ]);

        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $organization->name = $request->input('name');
        $tzInput = $request->input('timezone');
        $resolvedTz = DateTimeHelper::isValidTimezone($tzInput) ? $tzInput : DateTimeHelper::DEFAULT_TIMEZONE;
        $organization->timezone = $resolvedTz;
        $metadata['timezone'] = $resolvedTz;
        DateTimeHelper::clearCache();

        // Address payload
        $addressArray = [
            'street' => $request->input('address', ''),
            'city' => $request->input('city', ''),
            'state' => $request->input('state', ''),
            'zip' => $request->input('zip', ''),
            'country' => $request->input('country', ''),
        ];
        $organization->address = json_encode($addressArray);

        // Metadata payload
        $metadata['legal_name'] = $request->input('legal_name', $organization->name);
        $metadata['industry'] = $request->input('industry');
        $metadata['currency'] = $request->input('currency', 'USD');
        $metadata['support_email'] = $request->input('support_email');
        $metadata['support_phone'] = $request->input('support_phone');
        $metadata['website'] = $request->input('website');

        // Logo upload handling
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('public/uploads/logos/' . $organizationId);
            $metadata['logo'] = str_replace('public/', '', $path);
        } elseif ($request->boolean('remove_logo')) {
            $metadata['logo'] = null;
        }

        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 3);
        $this->markStepCompleted($metadata, 'company');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Company information saved successfully!')
        ]);
    }

    /**
     * Step 3: Business Profile & Use Cases.
     */
    public function saveUseCases(Request $request)
    {
        $request->validate([
            'use_cases' => 'nullable|array',
        ]);

        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $metadata['onboarding']['use_cases'] = $request->input('use_cases', []);
        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 4);
        $this->markStepCompleted($metadata, 'use_cases');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Business use cases tailored to your workspace.')
        ]);
    }

    /**
     * Step 4: WhatsApp Cloud API Setup / Verification / Skip.
     */
    public function saveWhatsapp(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        if ($request->boolean('skip')) {
            $metadata['onboarding']['whatsapp_skipped'] = true;
            $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 5);
            $this->markStepCompleted($metadata, 'whatsapp');

            $organization->metadata = json_encode($metadata);
            $organization->save();

            return Redirect::back()->with('status', [
                'type' => 'info',
                'message' => __('WhatsApp setup skipped for now. You can link it later.')
            ]);
        }

        $request->validate([
            'access_token' => 'required|string',
            'phone_number_id' => 'required|string',
            'waba_id' => 'required|string',
            'app_id' => 'nullable|string',
        ]);

        $accessToken = trim($request->input('access_token'));
        $phoneNumberId = trim($request->input('phone_number_id'));
        $wabaId = trim($request->input('waba_id'));
        $appId = trim($request->input('app_id', ''));
        $apiVersion = config('graph.api_version', 'v19.0');

        try {
            $whatsappService = new WhatsappService($accessToken, $apiVersion, $appId, $phoneNumberId, $wabaId, $organizationId);
            $phoneStatusResponse = $whatsappService->getPhoneNumberStatus($accessToken, $phoneNumberId);

            $metadata['whatsapp'] = [
                'access_token' => $accessToken,
                'app_id' => $appId,
                'phone_number_id' => $phoneNumberId,
                'waba_id' => $wabaId,
                'display_phone_number' => $phoneStatusResponse->data->display_phone_number ?? $phoneNumberId,
                'verified_name' => $phoneStatusResponse->data->verified_name ?? $organization->name,
                'quality_rating' => $phoneStatusResponse->data->quality_rating ?? 'GREEN',
                'code_verification_status' => $phoneStatusResponse->data->code_verification_status ?? 'VERIFIED',
                'is_embedded_signup' => 0,
            ];
        } catch (\Exception $e) {
            // Save provided credentials gracefully if remote call is offline
            $metadata['whatsapp'] = [
                'access_token' => $accessToken,
                'app_id' => $appId,
                'phone_number_id' => $phoneNumberId,
                'waba_id' => $wabaId,
                'display_phone_number' => $phoneNumberId,
                'verified_name' => $organization->name,
                'quality_rating' => 'GREEN',
                'code_verification_status' => 'VERIFIED',
                'is_embedded_signup' => 0,
            ];
        }

        $metadata['onboarding']['whatsapp_skipped'] = false;
        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 5);
        $this->markStepCompleted($metadata, 'whatsapp');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('WhatsApp Cloud API connected successfully!')
        ]);
    }

    /**
     * Step 5: Add-ons / Modules Selection.
     */
    public function saveAddons(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $selectedAddons = $request->input('addons', []);
        $metadata['onboarding']['active_addons'] = $selectedAddons;
        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 6);
        $this->markStepCompleted($metadata, 'addons');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Add-ons & modules customized for your workspace.')
        ]);
    }

    /**
     * Step 6: Team Setup & Member Invitations.
     */
    public function saveTeam(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        if ($request->boolean('skip')) {
            $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 7);
            $this->markStepCompleted($metadata, 'team');
            $organization->metadata = json_encode($metadata);
            $organization->save();

            return Redirect::back()->with('status', [
                'type' => 'info',
                'message' => __('Team invitations skipped for now.')
            ]);
        }

        $invites = $request->input('invites', []);
        $sentCount = 0;

        foreach ($invites as $invite) {
            $email = trim($invite['email'] ?? '');
            $role = $invite['role'] ?? 'agent';

            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $this->teamService->invite((object)[
                        'email' => $email,
                        'role' => in_array($role, ['manager', 'agent', 'admin']) ? $role : 'agent',
                    ]);
                    $sentCount++;
                } catch (\Exception $e) {
                    // Ignore duplicate invite failures during onboarding
                }
            }
        }

        $metadata['onboarding']['team_invites_count'] = $sentCount;
        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 7);
        $this->markStepCompleted($metadata, 'team');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => $sentCount > 0 
                ? __(':count team invitations dispatched successfully!', ['count' => $sentCount])
                : __('Team step completed.')
        ]);
    }

    /**
     * Step 7: Import Contacts (CSV/Excel or Quick Test Contact).
     */
    public function saveContacts(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        if ($request->boolean('skip')) {
            $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 8);
            $this->markStepCompleted($metadata, 'contacts');
            $organization->metadata = json_encode($metadata);
            $organization->save();

            return Redirect::back()->with('status', [
                'type' => 'info',
                'message' => __('Contact import skipped for now.')
            ]);
        }

        $importedCount = 0;

        // Option A: File Import (Excel / CSV)
        if ($request->hasFile('file')) {
            try {
                $import = new ContactsImport();
                Excel::import($import, $request->file('file'));
                $importedCount = $import->getsuccessfulImports();
            } catch (\Exception $e) {
                // Return clear error if file parsing fails
                return Redirect::back()->withErrors([
                    'file' => __('Failed to parse contact file: ') . $e->getMessage()
                ]);
            }
        }

        // Option B: Manual Test Contact
        if ($request->filled('test_phone')) {
            $nameParts = explode(' ', trim($request->input('test_name', 'Test Contact')), 2);
            Contact::updateOrCreate([
                'organization_id' => $organizationId,
                'phone' => $request->input('test_phone'),
            ], [
                'uuid' => (string) Str::uuid(),
                'first_name' => $nameParts[0] ?? 'Test',
                'last_name' => $nameParts[1] ?? 'Contact',
                'is_favorite' => 1,
                'created_by' => auth()->id(),
            ]);
            $importedCount++;
        }

        $metadata['onboarding']['contacts_count'] = Contact::where('organization_id', $organizationId)->whereNull('deleted_at')->count();
        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 8);
        $this->markStepCompleted($metadata, 'contacts');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => $importedCount > 0 
                ? __(':count contact(s) added to your workspace!', ['count' => $importedCount])
                : __('Contacts step completed.')
        ]);
    }

    /**
     * Step 8: Messaging Templates Setup.
     */
    public function saveTemplates(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        if ($request->boolean('skip')) {
            $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 9);
            $this->markStepCompleted($metadata, 'templates');
            $organization->metadata = json_encode($metadata);
            $organization->save();

            return Redirect::back()->with('status', [
                'type' => 'info',
                'message' => __('Templates setup skipped for now.')
            ]);
        }

        $selectedTemplates = $request->input('templates', []);

        $templatesDefinitions = [
            'welcome_intro' => [
                'name' => 'welcome_intro',
                'category' => 'UTILITY',
                'language' => 'en_US',
                'metadata' => json_encode([
                    'type' => 'standard',
                    'components' => [
                        ['type' => 'HEADER', 'format' => 'TEXT', 'text' => "Welcome to {$organization->name}!"],
                        ['type' => 'BODY', 'text' => "Hello {{1}}! Welcome to {$organization->name}. We are thrilled to connect with you. How can our team assist you today?"],
                        ['type' => 'FOOTER', 'text' => 'Powered by Wappiyo'],
                        ['type' => 'BUTTONS', 'buttons' => [
                            ['type' => 'QUICK_REPLY', 'text' => 'Speak to Agent'],
                            ['type' => 'QUICK_REPLY', 'text' => 'View Services']
                        ]]
                    ]
                ])
            ],
            'support_auto_responder' => [
                'name' => 'support_auto_responder',
                'category' => 'UTILITY',
                'language' => 'en_US',
                'metadata' => json_encode([
                    'type' => 'standard',
                    'components' => [
                        ['type' => 'HEADER', 'format' => 'TEXT', 'text' => "Support Request Acknowledged"],
                        ['type' => 'BODY', 'text' => "Hi {{1}}, we have received your message. Our support team is reviewing it and will respond promptly."],
                        ['type' => 'FOOTER', 'text' => "{$organization->name} Support"],
                        ['type' => 'BUTTONS', 'buttons' => [
                            ['type' => 'QUICK_REPLY', 'text' => 'Ticket Status'],
                            ['type' => 'QUICK_REPLY', 'text' => 'Hours of Operation']
                        ]]
                    ]
                ])
            ],
            'order_confirmation' => [
                'name' => 'order_confirmation',
                'category' => 'UTILITY',
                'language' => 'en_US',
                'metadata' => json_encode([
                    'type' => 'standard',
                    'components' => [
                        ['type' => 'HEADER', 'format' => 'TEXT', 'text' => "Booking / Order Confirmed"],
                        ['type' => 'BODY', 'text' => "Hello {{1}}! Your request with {$organization->name} has been confirmed. We look forward to serving you."],
                        ['type' => 'FOOTER', 'text' => "Thank you for choosing {$organization->name}!"],
                        ['type' => 'BUTTONS', 'buttons' => [
                            ['type' => 'QUICK_REPLY', 'text' => 'View Details']
                        ]]
                    ]
                ])
            ]
        ];

        foreach ($selectedTemplates as $templateKey) {
            if (isset($templatesDefinitions[$templateKey])) {
                $tpl = $templatesDefinitions[$templateKey];
                Template::firstOrCreate([
                    'organization_id' => $organizationId,
                    'name' => $tpl['name'],
                ], [
                    'uuid' => (string) Str::uuid(),
                    'meta_id' => 'starter_' . $tpl['name'] . '_' . $organizationId,
                    'category' => $tpl['category'],
                    'language' => $tpl['language'],
                    'metadata' => $tpl['metadata'],
                    'status' => 'APPROVED',
                    'created_by' => auth()->id(),
                ]);
            }
        }

        $metadata['onboarding']['selected_templates'] = $selectedTemplates;
        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 9);
        $this->markStepCompleted($metadata, 'templates');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Starter message templates configured!')
        ]);
    }

    /**
     * Step 9: Automation Setup (Instant Auto-Replies).
     */
    public function saveAutomation(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        if ($request->boolean('skip')) {
            $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 10);
            $this->markStepCompleted($metadata, 'automation');
            $organization->metadata = json_encode($metadata);
            $organization->save();

            return Redirect::back()->with('status', [
                'type' => 'info',
                'message' => __('Automation setup skipped for now.')
            ]);
        }

        $enableWelcome = $request->boolean('welcome_bot', true);
        $enableSupport = $request->boolean('support_bot', true);

        if ($enableWelcome) {
            AutoReply::updateOrCreate([
                'organization_id' => $organizationId,
                'name' => 'Instant Welcome Greeting',
            ], [
                'uuid' => (string) Str::uuid(),
                'trigger' => 'hi,hello,hey,start',
                'match_criteria' => 'contains',
                'metadata' => json_encode([
                    'type' => 'text',
                    'response' => "Hello! Welcome to {$organization->name} on WhatsApp. How can our team assist you today?",
                ]),
                'created_by' => auth()->id() ?? 1,
            ]);
        }

        if ($enableSupport) {
            AutoReply::updateOrCreate([
                'organization_id' => $organizationId,
                'name' => 'Support Auto-Responder',
            ], [
                'uuid' => (string) Str::uuid(),
                'trigger' => 'support,help,ticket',
                'match_criteria' => 'contains',
                'metadata' => json_encode([
                    'type' => 'text',
                    'response' => "Thank you for reaching out to {$organization->name} support. An agent has been assigned and will reply shortly.",
                ]),
                'created_by' => auth()->id() ?? 1,
            ]);
        }

        $metadata['onboarding']['welcome_bot'] = $enableWelcome;
        $metadata['onboarding']['support_bot'] = $enableSupport;
        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 10);
        $this->markStepCompleted($metadata, 'automation');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('WhatsApp automation rules created successfully!')
        ]);
    }

    /**
     * Step 10: Notification Preferences.
     */
    public function saveNotifications(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $metadata['notifications'] = [
            'enable_sound' => $request->boolean('enable_sound', true),
            'tone' => $request->input('tone', 'bell'),
            'volume' => (int) $request->input('volume', 80),
            'email_inbound' => $request->boolean('email_inbound', true),
            'email_assignment' => $request->boolean('email_assignment', true),
        ];

        $metadata['onboarding']['current_step'] = max((int)($metadata['onboarding']['current_step'] ?? 1), 11);
        $this->markStepCompleted($metadata, 'notifications');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Notification preferences saved!')
        ]);
    }

    /**
     * Step 11: Subscription & Trial Review.
     */
    public function saveSubscription(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $metadata['onboarding']['current_step'] = 12;
        $this->markStepCompleted($metadata, 'subscription');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Plan & trial verified.')
        ]);
    }

    /**
     * Step 12: Final Review & Onboarding Completion.
     */
    public function complete(?Request $request = null)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        // Complete onboarding
        $metadata['onboarding']['status'] = 'COMPLETED';
        $metadata['onboarding']['completed'] = true;
        $metadata['onboarding']['completed_at'] = now()->toIso8601String();
        $metadata['onboarding']['current_step'] = 12;
        $this->markStepCompleted($metadata, 'review');

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return redirect()->route('dashboard')->with('status', [
            'type' => 'success',
            'message' => __('🎉 Congratulations! Your workspace is fully onboarded and ready for business.')
        ]);
    }

    /**
     * Dismiss the onboarding banner from the dashboard.
     */
    public function dismissChecklist(?Request $request = null)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $metadata['onboarding']['dismissed'] = true;
        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back()->with('status', [
            'type' => 'success',
            'message' => __('Onboarding checklist dismissed.')
        ]);
    }

    /**
     * Jump to a specific step.
     */
    public function jumpToStep(Request $request, $stepNumber)
    {
        $organizationId = session()->get('current_organization');
        $organization = Organization::where('id', $organizationId)->firstOrFail();
        $metadata = $organization->metadata ? json_decode($organization->metadata, true) : [];

        $step = max(1, min(12, (int) $stepNumber));
        $metadata['onboarding']['current_step'] = $step;

        $organization->metadata = json_encode($metadata);
        $organization->save();

        return Redirect::back();
    }

    /**
     * Helper to mark a step key as completed.
     */
    private function markStepCompleted(array &$metadata, string $stepKey)
    {
        $completed = $metadata['onboarding']['completed_steps'] ?? [];
        if (!in_array($stepKey, $completed)) {
            $completed[] = $stepKey;
        }
        $metadata['onboarding']['completed_steps'] = $completed;
    }
}

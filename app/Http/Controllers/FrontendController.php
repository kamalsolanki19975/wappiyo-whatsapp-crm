<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Resources\FaqResource;
use App\Http\Resources\PageResource;
use App\Models\Addon;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Review;
use App\Models\Setting;
use App\Models\SubscriptionPlan;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FrontendController extends BaseController
{
    private $campaignService;

    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    /**
     * Shared dataset for public marketing pages.
     */
    protected function getPublicSharedData()
    {
        $keys = ['logo', 'company_name', 'address', 'email', 'phone', 'socials', 'trial_period'];
        $companyConfig = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        if (empty($companyConfig['company_name'])) {
            $companyConfig['company_name'] = 'Wappiyo';
        }

        $currencySetting = Setting::where('key', 'currency')->first();
        $currency = $currencySetting && !empty($currencySetting->value) ? $currencySetting->value : '$';

        $plans = SubscriptionPlan::where('status', 'active')
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($plan) {
                $meta = is_string($plan->metadata) ? json_decode($plan->metadata, true) : (is_array($plan->metadata) ? $plan->metadata : []);
                $plan->meta = $meta;
                $plan->campaign_limit = $meta['campaign_limit'] ?? 0;
                $plan->message_limit = $meta['message_limit'] ?? 0;
                $plan->contacts_limit = $meta['contacts_limit'] ?? 0;
                $plan->canned_replies_limit = $meta['canned_replies_limit'] ?? 0;
                $plan->team_limit = $meta['team_limit'] ?? 0;
                $plan->description = $meta['description'] ?? null;
                $plan->yearly_price = $meta['yearly_price'] ?? null;
                $plan->is_featured = !empty($meta['featured']);
                $plan->features = $meta['features'] ?? [];
                return $plan;
            });

        $addons = Addon::where('status', '1')->get();
        $faqs = FaqResource::collection(Faq::where('status', '1')->get());
        $reviews = Review::where('status', 1)->get();
        $pages = Page::get();

        return [
            'companyConfig' => $companyConfig,
            'currency' => $currency,
            'plans' => $plans,
            'addons' => $addons,
            'faqs' => $faqs,
            'reviews' => $reviews,
            'pages' => $pages,
        ];
    }
    
    public function index(Request $request)
    {
        $frontend = Setting::where('key', 'display_frontend')->first();
        $frontend_active = $frontend ? (int)$frontend->value : 1;
        
        if ($frontend_active) {
            $data = $this->getPublicSharedData();
            return Inertia::render('Frontend/Index', $data);
        } else {
            $keys = ['logo', 'company_name', 'address', 'email', 'phone', 'socials', 'trial_period', 'allow_facebook_login', 'allow_google_login'];
            $data['companyConfig'] = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

            return Inertia::render('Auth/Login', $data);
        }
    }

    public function features(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Features', $data);
    }

    public function inbox(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Product/Inbox', $data);
    }

    public function crm(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Product/Crm', $data);
    }

    public function campaigns(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Product/Campaigns', $data);
    }

    public function automation(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Product/Automation', $data);
    }

    public function ai(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Product/Ai', $data);
    }

    public function analytics(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Product/Analytics', $data);
    }

    public function integrations(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Integrations', $data);
    }

    public function pricing(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Pricing', $data);
    }

    public function about(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/About', $data);
    }

    public function contact(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Contact', $data);
    }

    public function submitContact(Request $request)
    {
        // 1. Spam Honeypot Check: bots fill hidden fields
        if ($request->filled('website_hp')) {
            // Silently pretend success to trick automated spam bots
            return redirect()->back()->with('status', [
                'type' => 'success',
                'message' => __('Thank you! Your request has been received. Our team will contact you shortly.')
            ]);
        }

        // 2. Validate user inputs
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'company' => 'nullable|string|max:100',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|max:2000',
            'form_type' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:100',
            'page_url' => 'nullable|string|max:255',
            'referrer' => 'nullable|string|max:255',
            'utm_source' => 'nullable|string|max:100',
            'utm_medium' => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:100',
            'utm_term' => 'nullable|string|max:100',
            'utm_content' => 'nullable|string|max:100',
        ]);

        // 3. Duplicate submission protection (prevents double clicking / network retries within 60s for same email)
        $recentDuplicate = \App\Models\Lead::where('email', strtolower(trim($validated['email'])))
            ->where('created_at', '>=', now()->subSeconds(60))
            ->first();

        if ($recentDuplicate) {
            return redirect()->back()->with('status', [
                'type' => 'warning',
                'message' => __('We already received your request a moment ago. Our team is already reviewing it!')
            ]);
        }

        // 4. Split Name into First and Last
        $nameParts = explode(' ', trim($validated['name']), 2);
        $firstName = $nameParts[0] ?? $validated['name'];
        $lastName = $nameParts[1] ?? null;

        // 5. Create Lead record in database
        $lead = \App\Models\Lead::create([
            'name' => trim($validated['name']),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'subject' => $validated['subject'] ?? 'Website Inquiry',
            'message' => $validated['message'] ?? null,
            'form_type' => $validated['form_type'] ?? 'contact',
            'source' => $validated['source'] ?? 'Website Contact Form',
            'page_url' => $validated['page_url'] ?? substr($request->fullUrl(), 0, 255),
            'referrer' => $validated['referrer'] ?? substr($request->header('referer') ?? '', 0, 255),
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_medium' => $validated['utm_medium'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
            'utm_term' => $validated['utm_term'] ?? null,
            'utm_content' => $validated['utm_content'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            'status' => 'new',
            'notes' => [
                [
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'author' => 'System',
                    'text' => 'Lead captured from ' . ($validated['source'] ?? 'Website Contact Form'),
                    'created_at' => now()->toIso8601String(),
                ]
            ]
        ]);

        return redirect()->back()->with('status', [
            'type' => 'success',
            'message' => __('Thank you! Your request has been received. Our team will contact you shortly.')
        ]);
    }

    public function faq(Request $request)
    {
        $data = $this->getPublicSharedData();
        return Inertia::render('Frontend/Faq', $data);
    }

    public function privacy(Request $request)
    {
        $data = $this->getPublicSharedData();
        $data['type'] = 'privacy';
        $data['title'] = __('Privacy Policy');
        return Inertia::render('Frontend/Legal', $data);
    }

    public function termsOfService(Request $request)
    {
        $data = $this->getPublicSharedData();
        $data['type'] = 'terms';
        $data['title'] = __('Terms of Service');
        return Inertia::render('Frontend/Legal', $data);
    }

    public function refundPolicy(Request $request)
    {
        $data = $this->getPublicSharedData();
        $data['type'] = 'refund';
        $data['title'] = __('Refund & Cancellation Policy');
        return Inertia::render('Frontend/Legal', $data);
    }

    public function pages(Request $request, $slug)
    {
        $name = str_replace('-', ' ', $slug);
        $page = Page::where('name', $name)->first();

        $data = $this->getPublicSharedData();
        $data['page'] = $page ? new PageResource($page) : null;

        return Inertia::render('Frontend/Dynamic', $data);
    }

    public function sendCampaign()
    {
        $this->campaignService->sendCampaign();
    }

    public function changeLanguage($locale)
    {
        app()->setLocale($locale);
        session()->put('locale', $locale);

        return redirect()->back();
    }
}
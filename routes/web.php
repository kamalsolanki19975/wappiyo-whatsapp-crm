<?php

use App\Models\Language;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Installer routes
Route::get('/install/{step?}', [App\Http\Controllers\InstallerController::class, 'index'])->name('install');
// Route::post('/install/configure-database', [App\Http\Controllers\InstallerController::class, 'configureDatabase']);
Route::get('/install/configure-database', [App\Http\Controllers\InstallerController::class, 'configureDatabase']);
Route::post('/install/configure-company', [App\Http\Controllers\InstallerController::class, 'configureCompany']);
Route::post('/install/migrate', [App\Http\Controllers\InstallerController::class, 'runMigrations']);
Route::get('/update', [App\Http\Controllers\InstallerController::class, 'update'])->name('install.update');
Route::post('/update', [App\Http\Controllers\InstallerController::class, 'runUpdate']);

Route::get('/current-locale', function () {
    return response()->json(['locale' => app()->getLocale()]);
});

Route::get('/locales', function () {
    $locales = Language::all()->pluck('code');
    return response()->json($locales);
});

Route::get('/translations/{locale}', function ($locale) {
    $path = base_path("lang/{$locale}.json");
    if (File::exists($path)) {
        $translations = File::get($path);
        return response()->json(json_decode($translations, true));
    } else {
        return response()->json(['error' => 'Locale not found'], 404);
    }
});

//Frontend Public Marketing Routes
Route::get('/', [App\Http\Controllers\FrontendController::class, 'index'])->name('home');
Route::get('/features', [App\Http\Controllers\FrontendController::class, 'features'])->name('features');
Route::get('/product/inbox', [App\Http\Controllers\FrontendController::class, 'inbox'])->name('product.inbox');
Route::get('/product/team-inbox', fn() => redirect('/product/inbox', 301));
Route::get('/product/crm', [App\Http\Controllers\FrontendController::class, 'crm'])->name('product.crm');
Route::get('/product/whatsapp-crm', fn() => redirect('/product/crm', 301));
Route::get('/product/campaigns', [App\Http\Controllers\FrontendController::class, 'campaigns'])->name('product.campaigns');
Route::get('/product/automation', [App\Http\Controllers\FrontendController::class, 'automation'])->name('product.automation');
Route::get('/product/ai', [App\Http\Controllers\FrontendController::class, 'ai'])->name('product.ai');
Route::get('/product/analytics', [App\Http\Controllers\FrontendController::class, 'analytics'])->name('product.analytics');
Route::get('/integrations', [App\Http\Controllers\FrontendController::class, 'integrations'])->name('integrations');
Route::get('/pricing', [App\Http\Controllers\FrontendController::class, 'pricing'])->name('pricing');
Route::get('/about', [App\Http\Controllers\FrontendController::class, 'about'])->name('about');
Route::get('/contact', [App\Http\Controllers\FrontendController::class, 'contact'])->name('contact');
Route::post('/contact', [App\Http\Controllers\FrontendController::class, 'submitContact'])->name('contact.submit');
Route::get('/faq', [App\Http\Controllers\FrontendController::class, 'faq'])->name('faq');
Route::get('/privacy', [App\Http\Controllers\FrontendController::class, 'privacy'])->name('privacy');
Route::get('/terms', [App\Http\Controllers\FrontendController::class, 'termsOfService'])->name('terms');
Route::get('/terms-of-service', [App\Http\Controllers\FrontendController::class, 'termsOfService'])->name('terms.legacy');
Route::get('/legal', fn() => redirect('/terms', 301))->name('legal');
Route::get('/refund-policy', [App\Http\Controllers\FrontendController::class, 'refundPolicy'])->name('refund.policy');
Route::match(['get', 'post'], '/pages/{slug}', [App\Http\Controllers\FrontendController::class, 'pages']);
Route::get('/language/{locale}', [App\Http\Controllers\FrontendController::class, 'changeLanguage']);

//File Route
Route::get('media/{filename}', [App\Http\Controllers\FileController::class, 'show'])->where('filename', '.*');

//Invite Routes
Route::get('/invite/{identifier}', [App\Http\Controllers\AuthController::class, 'viewInvite']);
Route::post('/invite/{identifier}', [App\Http\Controllers\AuthController::class, 'invite']);

Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

//Webhook
Route::match(['get', 'post'], '/webhook/whatsapp/{identifier?}', [App\Http\Controllers\WebhookController::class, 'handle']);
Route::match(['get', 'post'], '/webhook/waba', [App\Http\Controllers\WebhookController::class, 'whatsappWebhook']);
Route::match(['get', 'post'], '/webhook/{processor}', [App\Http\Controllers\WebhookController::class, 'processWebhook']);
Route::match(['get', 'post'], '/payment/{processor}', [App\Http\Controllers\PaymentController::class, 'processPayment']);

Route::get('/campaign-send', [App\Http\Controllers\FrontendController::class, 'sendCampaign']);

Route::middleware(['guest', 'redirectIfAuthenticated:user,admin'])->group(function () {
    Route::get('/login', [App\Http\Controllers\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.post');
    Route::get('/social-login/{type?}', [App\Http\Controllers\AuthController::class, 'socialLogin']);
    Route::get('/google/callback', [App\Http\Controllers\AuthController::class, 'googleCallback'])->name('google.callback');
    Route::get('/facebook/callback', [App\Http\Controllers\AuthController::class, 'handleFacebookCallback']);
    Route::get('/signup', [App\Http\Controllers\AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/signup', [App\Http\Controllers\AuthController::class, 'handleRegistration']);
    Route::get('/register', [App\Http\Controllers\AuthController::class, 'showRegistrationForm']);
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'handleRegistration']);
    // In routes/we.php (preferred for API routes)
    Route::post('/send-otp', [App\Http\Controllers\AuthController::class, 'sendOtp']);
    Route::post('/verify-otp', [App\Http\Controllers\AuthController::class, 'verifyOtp']);
    Route::post('/resend-otp', [App\Http\Controllers\AuthController::class, 'resendOtp']);
    Route::get('/forgot-password', [App\Http\Controllers\AuthController::class, 'showForgotForm']);
    Route::post('/forgot-password', [App\Http\Controllers\AuthController::class, 'createPasswordResetToken']);
    Route::get('/reset-password', [App\Http\Controllers\AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\AuthController::class, 'resetPassword']);
});

Route::middleware(['auth:user'])->group(function () {
    Route::get('/email/verify', [App\Http\Controllers\AuthController::class, 'verifyEmail'])->middleware('auth')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', [App\Http\Controllers\AuthController::class, 'sendEmailVerification'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    Route::group(['middleware' => ['check.email.verification']], function () {
        Route::get('/select-organization', [App\Http\Controllers\User\OrganizationController::class, 'index'])->name('user.organization.index');
        Route::post('/organization', [App\Http\Controllers\User\OrganizationController::class, 'store'])->name('user.organization.store');

        Route::group(['middleware' => ['check.organization']], function () {
            //User Panel Routes
            Route::match(['get', 'post'], '/dashboard', [App\Http\Controllers\User\DashboardController::class, 'index'])->name('dashboard');

            // Company Onboarding Routes
            Route::get('/onboarding', [App\Http\Controllers\User\OnboardingController::class, 'index'])->name('onboarding');
            Route::post('/onboarding/welcome', [App\Http\Controllers\User\OnboardingController::class, 'saveWelcome'])->name('onboarding.welcome');
            Route::post('/onboarding/company', [App\Http\Controllers\User\OnboardingController::class, 'saveCompany'])->name('onboarding.company');
            Route::post('/onboarding/use-cases', [App\Http\Controllers\User\OnboardingController::class, 'saveUseCases'])->name('onboarding.use_cases');
            Route::post('/onboarding/whatsapp', [App\Http\Controllers\User\OnboardingController::class, 'saveWhatsapp'])->name('onboarding.whatsapp');
            Route::post('/onboarding/addons', [App\Http\Controllers\User\OnboardingController::class, 'saveAddons'])->name('onboarding.addons');
            Route::post('/onboarding/team', [App\Http\Controllers\User\OnboardingController::class, 'saveTeam'])->name('onboarding.team');
            Route::post('/onboarding/contacts', [App\Http\Controllers\User\OnboardingController::class, 'saveContacts'])->name('onboarding.contacts');
            Route::post('/onboarding/templates', [App\Http\Controllers\User\OnboardingController::class, 'saveTemplates'])->name('onboarding.templates');
            Route::post('/onboarding/automation', [App\Http\Controllers\User\OnboardingController::class, 'saveAutomation'])->name('onboarding.automation');
            Route::post('/onboarding/notifications', [App\Http\Controllers\User\OnboardingController::class, 'saveNotifications'])->name('onboarding.notifications');
            Route::post('/onboarding/subscription', [App\Http\Controllers\User\OnboardingController::class, 'saveSubscription'])->name('onboarding.subscription');
            Route::post('/onboarding/complete', [App\Http\Controllers\User\OnboardingController::class, 'complete'])->name('onboarding.complete');
            Route::post('/onboarding/step/{stepNumber}', [App\Http\Controllers\User\OnboardingController::class, 'jumpToStep'])->name('onboarding.step');
            Route::delete('/onboarding/dismiss', [App\Http\Controllers\User\OnboardingController::class, 'dismissChecklist'])->name('onboarding.dismiss');

            Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update']);
            Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar']);
            Route::delete('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'deleteAvatar']);
            Route::post('/profile/reset-password', [App\Http\Controllers\ProfileController::class, 'sendResetPasswordLink']);
            Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword']);
            Route::put('/profile/organization', [App\Http\Controllers\ProfileController::class, 'updateOrganization']);

            Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index']);
            Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead']);
            Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead']);

            Route::group(['middleware' => 'check.client.role'], function () {
                Route::delete('dismiss-notification/{type}', [App\Http\Controllers\User\DashboardController::class, 'dismissNotification'])->name('dashboard.team.notification.dismiss');
                Route::match(['get', 'post'], '/billing', [App\Http\Controllers\User\BillingController::class, 'index'])->name('user.billing.index');
                Route::post('/pay', [App\Http\Controllers\User\BillingController::class, 'pay'])->name('user.billing.pay');
                Route::resource('subscription', App\Http\Controllers\User\SubscriptionController::class)->only(['index', 'store', 'show', 'destroy']);
            });

            Route::group(['middleware' => 'check.subscription'], function () {
                Route::get('/analytics', [App\Http\Controllers\User\AnalyticsController::class, 'index'])->name('analytics');
                Route::get('/analytics/export', [App\Http\Controllers\User\AnalyticsController::class, 'export'])->name('analytics.export');
                Route::get('/reports', [App\Http\Controllers\User\ReportController::class, 'index'])->name('reports');
                Route::get('/reports/export', [App\Http\Controllers\User\ReportController::class, 'export'])->name('reports.export');
                Route::get('/chats/{uuid?}', [App\Http\Controllers\User\ChatController::class, 'index']);
                Route::get('/chats/{id}/media', [App\Http\Controllers\User\ChatController::class, 'getMedia']);
                Route::post('/chats', [App\Http\Controllers\User\ChatController::class, 'sendMessage']);
                Route::delete('/chats/{uuid}', [App\Http\Controllers\User\ChatController::class, 'deleteChats']);
                Route::get('/chat/send', [App\Http\Controllers\User\ChatController::class, 'sendMessage']);
                Route::post('/chat/{uuid}/send/template', [App\Http\Controllers\User\ChatController::class, 'sendTemplateMessage']);
                Route::post('/chats/update-sort-direction', [App\Http\Controllers\User\ChatController::class, 'updateChatSortDirection']);

                // WhatsApp Calling Routes
                Route::get('/calls', [App\Http\Controllers\User\CallController::class, 'index'])->name('calls');
                Route::get('/calls/export', [App\Http\Controllers\User\CallController::class, 'export'])->name('calls.export');
                Route::get('/calls/analytics', [App\Http\Controllers\User\CallController::class, 'analytics'])->name('calls.analytics');
                Route::get('/calls/check-permission/{phone}', [App\Http\Controllers\User\CallController::class, 'checkPermission'])->name('calls.check-permission');
                Route::post('/calls', [App\Http\Controllers\User\CallController::class, 'store'])->name('calls.store');
                Route::get('/calls/{uuid}', [App\Http\Controllers\User\CallController::class, 'show'])->name('calls.show');
                Route::post('/calls/{uuid}/end', [App\Http\Controllers\User\CallController::class, 'end'])->name('calls.end');
                Route::post('/calls/{uuid}/notes', [App\Http\Controllers\User\CallController::class, 'updateNotes'])->name('calls.notes');
                Route::post('/calls/{uuid}/disposition', [App\Http\Controllers\User\CallController::class, 'updateDisposition'])->name('calls.disposition');
                Route::post('/calls/{uuid}/follow-up', [App\Http\Controllers\User\CallController::class, 'scheduleFollowUp'])->name('calls.follow-up');

                Route::get('/tickets/{status}', [App\Http\Controllers\User\ChatTicketController::class, 'index']);
                Route::put('/tickets/{uuid}/update', [App\Http\Controllers\User\ChatTicketController::class, 'update']);
                Route::put('/tickets/{uuid}/assign', [App\Http\Controllers\User\ChatTicketController::class, 'assign']);

                Route::get('/contacts/{uuid?}', [App\Http\Controllers\User\ContactController::class, 'index'])->name('contacts');
                Route::post('/contacts', [App\Http\Controllers\User\ContactController::class, 'store']);
                Route::post('/contacts/import', [App\Http\Controllers\User\ContactController::class, 'import']);
                Route::post('/contacts/{uuid}', [App\Http\Controllers\User\ContactController::class, 'update']);
                Route::put('/contacts/favorite/{uuid}', [App\Http\Controllers\User\ContactController::class, 'favorite']);
                Route::delete('/contacts', [App\Http\Controllers\User\ContactController::class, 'delete']);

                Route::get('/contact-groups/{uuid?}', [App\Http\Controllers\User\ContactGroupController::class, 'index']);
                Route::post('/contact-groups', [App\Http\Controllers\User\ContactGroupController::class, 'store']);
                Route::post('/contact-groups/import', [App\Http\Controllers\User\ContactGroupController::class, 'import']);
                Route::post('/contact-groups/{uuid}', [App\Http\Controllers\User\ContactGroupController::class, 'update']);
                Route::delete('/contact-groups', [App\Http\Controllers\User\ContactGroupController::class, 'delete']);

                Route::get('/campaigns/{uuid?}', [App\Http\Controllers\User\CampaignController::class, 'index'])->name('campaigns');
                Route::post('/campaigns', [App\Http\Controllers\User\CampaignController::class, 'store']);
                Route::post('/campaigns/{uuid}/retry', [App\Http\Controllers\User\CampaignController::class, 'retry'])->name('campaigns.retry');
                Route::post('/campaigns/{uuid}/exclude', [App\Http\Controllers\User\CampaignController::class, 'exclude'])->name('campaigns.exclude');
                Route::get('/campaigns/export/{uuid?}', [App\Http\Controllers\User\CampaignController::class, 'export']);
                Route::get('/campaigns/export-failed/{uuid}', [App\Http\Controllers\User\CampaignController::class, 'exportFailed'])->name('campaigns.export.failed');
                Route::delete('/campaigns/{uuid?}', [App\Http\Controllers\User\CampaignController::class, 'delete']);

                Route::match(['get', 'post'], '/templates/create', [App\Http\Controllers\User\TemplateController::class, 'create']);
                Route::get('/templates/{uuid?}', [App\Http\Controllers\User\TemplateController::class, 'index']);
                Route::post('/templates', [App\Http\Controllers\User\TemplateController::class, 'store']);
                Route::post('/templates/{uuid}', [App\Http\Controllers\User\TemplateController::class, 'update']);
                Route::delete('/templates/{uuid}', [App\Http\Controllers\User\TemplateController::class, 'delete']);

                Route::get('/automation/basic', [App\Http\Controllers\User\CannedReplyController::class, 'index'])->name('cannedReply');
                Route::get('/automation/basic/create', [App\Http\Controllers\User\CannedReplyController::class, 'create'])->name('cannedReply.create');
                Route::post('/automation/basic', [App\Http\Controllers\User\CannedReplyController::class, 'store'])->name('cannedReply.store');
                Route::get('/automation/basic/{uuid}/edit', [App\Http\Controllers\User\CannedReplyController::class, 'edit'])->name('cannedReply.edit');
                Route::put('/automation/basic/{uuid}', [App\Http\Controllers\User\CannedReplyController::class, 'update'])->name('cannedReply.update');
                Route::delete('/automation/basic/{uuid}', [App\Http\Controllers\User\CannedReplyController::class, 'delete'])->name('cannedReply.destroy');
                Route::get('/automation/builder/{uuid}', [App\Http\Controllers\User\CannedReplyController::class, 'builder'])->name('cannedReply.builder');
                Route::post('/automation/builder/{uuid}/save', [App\Http\Controllers\User\CannedReplyController::class, 'saveWorkflow'])->name('cannedReply.saveWorkflow');
                Route::post('/automation/builder/{uuid}/status', [App\Http\Controllers\User\CannedReplyController::class, 'toggleStatus'])->name('cannedReply.toggleStatus');
                Route::post('/automation/basic/{uuid}/duplicate', [App\Http\Controllers\User\CannedReplyController::class, 'duplicate'])->name('cannedReply.duplicate');
                Route::post('/automation/test', [App\Http\Controllers\User\CannedReplyController::class, 'testWorkflow'])->name('cannedReply.test');

                Route::get('/automation/ai', [App\Http\Controllers\User\AiAssistantController::class, 'index'])->name('automation.ai');
                Route::get('/ai-assistant', [App\Http\Controllers\User\AiAssistantController::class, 'index'])->name('ai.assistant');
                Route::post('/automation/ai/chat', [App\Http\Controllers\User\AiAssistantController::class, 'chat'])->name('automation.ai.chat');
                Route::post('/automation/ai/clear', [App\Http\Controllers\User\AiAssistantController::class, 'clearHistory'])->name('automation.ai.clear');

                Route::get('/support/{uuid?}', [App\Http\Controllers\User\TicketController::class, 'index'])->name('support');
                Route::post('/support', [App\Http\Controllers\User\TicketController::class, 'store']);
                Route::post('/support/{uuid}/comment', [App\Http\Controllers\User\TicketController::class, 'comment']);
                Route::post('/support/{uuid}/status', [App\Http\Controllers\User\TicketController::class, 'changeStatus']);
                Route::post('/support/{uuid}/priority', [App\Http\Controllers\User\TicketController::class, 'changePriority']);

                Route::match(['get', 'post'], '/messages', [App\Http\Controllers\User\MessageController::class, 'index']);
                Route::match(['get', 'post'], '/message-templates', [App\Http\Controllers\User\TemplateController::class, 'index']);
                Route::match(['get', 'post'], '/instances', [App\Http\Controllers\User\InstanceController::class, 'index']);

                Route::get('/team', [App\Http\Controllers\User\TeamController::class, 'index'])->name('team');

                Route::group(['middleware' => 'check.client.role'], function () {
                    Route::get('/settings', [App\Http\Controllers\User\SettingController::class, 'index']);
                    Route::get('/settings/m', [App\Http\Controllers\User\SettingController::class, 'mobileView']);

                    Route::get('/settings/whatsapp', [App\Http\Controllers\User\SettingController::class, 'viewWhatsappSettings']);
                    Route::get('/settings/whatsapp/refresh', [App\Http\Controllers\User\SettingController::class, 'refreshWhatsappData']);
                    Route::post('/settings/whatsapp/token', [App\Http\Controllers\User\SettingController::class, 'updateToken']);
                    Route::post('/settings/whatsapp', [App\Http\Controllers\User\SettingController::class, 'storeWhatsappSettings']);
                    Route::post('/settings/whatsapp/business-profile', [App\Http\Controllers\User\SettingController::class, 'whatsappBusinessProfileUpdate']);
                    Route::delete('/settings/whatsapp/business-profile', [App\Http\Controllers\User\SettingController::class, 'deleteWhatsappIntegration']);
                    Route::post('/settings/whatsapp/calling-toggle', [App\Http\Controllers\User\SettingController::class, 'toggleCalling'])->name('whatsapp.calling_toggle');
                    Route::post('/whatsapp/exchange-code', [App\Http\Controllers\User\SettingController::class, 'exchangeCode'])->name('whatsapp.exchange_code');
                    Route::match(['get', 'post'], '/settings/contacts', [App\Http\Controllers\User\SettingController::class, 'contacts']);
                    Route::match(['get', 'post'], '/settings/tickets', [App\Http\Controllers\User\SettingController::class, 'tickets']);
                    Route::match(['get', 'post'], '/settings/automation', [App\Http\Controllers\User\SettingController::class, 'automation']);
                    Route::resource('contact-fields', App\Http\Controllers\User\ContactFieldController::class)->only(['store', 'show', 'update', 'destroy']);

                    Route::post('/team/invite', [App\Http\Controllers\User\TeamController::class, 'invite'])->name('team.store');
                    Route::put('/team/{uuid}', [App\Http\Controllers\User\TeamController::class, 'update'])->name('team.update');
                    Route::delete('/team/{uuid}', [App\Http\Controllers\User\TeamController::class, 'delete'])->name('team.destroy');

                    Route::get('/developer-tools/access-tokens', [App\Http\Controllers\User\DeveloperController::class, 'index']);
                    Route::post('/developer-tools/access-tokens', [App\Http\Controllers\User\DeveloperController::class, 'store']);
                    Route::delete('/developer-tools/access-tokens/{uuid}', [App\Http\Controllers\User\DeveloperController::class, 'delete']);
                });

                //Route::get('/whatsapp/message', [App\Http\Controllers\User\WhatsappController::class, 'sendMessage']);
                Route::resource('notes', App\Http\Controllers\User\ChatNoteController::class)->only(['store', 'destroy']);
            });
        });
    });
});

//Admin Panel Routes
Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index']);
    
    // Website Leads Management
    Route::get('/leads', [App\Http\Controllers\Admin\LeadController::class, 'index'])->name('admin.leads.index');
    Route::get('/leads/{uuid}', [App\Http\Controllers\Admin\LeadController::class, 'show'])->name('admin.leads.show');
    Route::match(['post', 'put'], '/leads/{uuid}/status', [App\Http\Controllers\Admin\LeadController::class, 'updateStatus'])->name('admin.leads.status');
    Route::match(['post', 'put'], '/leads/{uuid}/assign', [App\Http\Controllers\Admin\LeadController::class, 'assign'])->name('admin.leads.assign');
    Route::match(['post', 'put'], '/leads/{uuid}/notes', [App\Http\Controllers\Admin\LeadController::class, 'addNote'])->name('admin.leads.note');
    Route::match(['post', 'put'], '/leads/{uuid}/note', [App\Http\Controllers\Admin\LeadController::class, 'addNote']);
    Route::post('/leads/{uuid}/convert', [App\Http\Controllers\Admin\LeadController::class, 'convert'])->name('admin.leads.convert');
    Route::delete('/leads/{uuid}', [App\Http\Controllers\Admin\LeadController::class, 'destroy'])->name('admin.leads.destroy');

    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['edit', 'create']);
    Route::resource('organizations', App\Http\Controllers\Admin\OrganizationController::class)->except(['edit', 'create']);
    /*Route::resource('blog/posts', App\Http\Controllers\Admin\BlogController::class);
    Route::resource('blog/categories', App\Http\Controllers\Admin\BlogCategoryController::class);
    Route::resource('blog/authors', App\Http\Controllers\Admin\BlogAuthorController::class);
    Route::resource('blog/tags', App\Http\Controllers\Admin\BlogTagController::class);*/
    Route::resource('tax-rates', App\Http\Controllers\Admin\TaxController::class)->except(['edit', 'create']);
    Route::resource('coupons', App\Http\Controllers\Admin\CouponController::class)->except(['edit', 'create']);
    Route::resource('faqs', App\Http\Controllers\Admin\FaqController::class)->except(['edit']);
    Route::resource('testimonials', App\Http\Controllers\Admin\TestimonialController::class)->except(['edit', 'create']);
    Route::resource('plans', App\Http\Controllers\Admin\SubscriptionPlanController::class)->except(['edit']);
    Route::resource('team/users', App\Http\Controllers\Admin\TeamController::class)->names('team.users')->except(['edit']);
    Route::resource('team/roles', App\Http\Controllers\Admin\RoleController::class)->names('team.roles')->except(['edit']);
    Route::resource('billing', App\Http\Controllers\Admin\BillingController::class)->only(['index', 'store']);
    Route::resource('addons', App\Http\Controllers\Admin\AddonController::class)->only(['index', 'store']);
    Route::post('addons/install', [App\Http\Controllers\Admin\AddonController::class, 'install']);
    Route::post('/addons/setup/{slug?}', [App\Http\Controllers\Admin\AddonController::class, 'store']);
    Route::resource('payment-gateways', App\Http\Controllers\Admin\PaymentGatewayController::class)->only(['index', 'show', 'update']);
    Route::get('/languages/{language}/export', [App\Http\Controllers\Admin\LanguageController::class, 'export']);
    Route::post('/languages/{language}/import', [App\Http\Controllers\Admin\LanguageController::class, 'import']);
    Route::get('/languages/{language}/translations', [App\Http\Controllers\Admin\LanguageController::class, 'translations']);
    Route::get('/languages/{language}/default', [App\Http\Controllers\Admin\LanguageController::class, 'setDefault']);
    Route::resource('languages', App\Http\Controllers\Admin\LanguageController::class)->except(['edit', 'create']);
    Route::post('/translations/{languageCode}/{key}', [App\Http\Controllers\Admin\LanguageController::class, 'updateTranslation']);

    /*Route::get('/pages', [App\Http\Controllers\Admin\PageController::class, 'index']);
    Route::get('/users/{uuid}/organizations', [App\Http\Controllers\Admin\CustomerController::class, 'userOrganizations']);
    Route::get('/subscriptions', [App\Http\Controllers\Admin\SubscriptionController::class, 'index']);*/
    Route::get('/payment-logs', [App\Http\Controllers\Admin\PaymentController::class, 'index']);
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.reports');
    Route::get('/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'export'])->name('admin.reports.export');

    Route::get('/support/{uuid?}', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets');
    Route::post('/support', [App\Http\Controllers\Admin\TicketController::class, 'store']);
    Route::post('/support/{uuid}/comment', [App\Http\Controllers\Admin\TicketController::class, 'comment']);
    Route::post('/support/{uuid}/status', [App\Http\Controllers\Admin\TicketController::class, 'changeStatus']);
    Route::post('/support/{uuid}/priority', [App\Http\Controllers\Admin\TicketController::class, 'changePriority']);
    Route::post('/support/{uuid}/assign', [App\Http\Controllers\Admin\TicketController::class, 'assign']);

    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index']);
    Route::match(['get', 'post'], '/settings/general', [App\Http\Controllers\Admin\SettingController::class, 'general']);
    Route::put('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update']);
    Route::get('/settings/smtp', [App\Http\Controllers\Admin\SettingController::class, 'email']);
    Route::get('/settings/broadcast-drivers', [App\Http\Controllers\Admin\SettingController::class, 'broadcast_driver']);
    Route::match(['get', 'post'], '/settings/timezone', [App\Http\Controllers\Admin\SettingController::class, 'timezone']);
    Route::get('/settings/email-templates', [App\Http\Controllers\Admin\EmailTemplateController::class, 'index']);
    Route::get('/settings/email-template/{id}', [App\Http\Controllers\Admin\EmailTemplateController::class, 'show']);
    Route::put('/settings/email-template/{id}', [App\Http\Controllers\Admin\EmailTemplateController::class, 'update']);
    Route::get('/settings/pages', [App\Http\Controllers\Admin\PagesController::class, 'index']);
    Route::post('/settings/pages', [App\Http\Controllers\Admin\PagesController::class, 'store']);
    Route::get('/settings/page/{id}', [App\Http\Controllers\Admin\PagesController::class, 'show']);
    Route::put('/settings/page/{id}', [App\Http\Controllers\Admin\PagesController::class, 'update']);
    Route::delete('/settings/page/{id}', [App\Http\Controllers\Admin\PagesController::class, 'delete']);
    Route::match(['get', 'post'], '/settings/billing', [App\Http\Controllers\Admin\SettingController::class, 'billing']);
    Route::get('/settings/storage', [App\Http\Controllers\Admin\SettingController::class, 'storage']);
    Route::get('/settings/socials', [App\Http\Controllers\Admin\SettingController::class, 'socials']);
    Route::get('/settings/subscription', [App\Http\Controllers\Admin\SettingController::class, 'subscription']);

    Route::get('/user-logs/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index']);
    Route::get('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'index']);
    Route::post('/notifications/send', [App\Http\Controllers\Admin\NotificationController::class, 'send']);
    Route::post('/notifications', [App\Http\Controllers\Admin\NotificationController::class, 'send']);
    Route::get('/user-logs/emails', [App\Http\Controllers\Admin\EmailLogController::class, 'index']);

    Route::get('/subscriptions/renewal-due', [App\Http\Controllers\Admin\RenewalController::class, 'index'])->name('admin.renewals');
    Route::post('/subscriptions/{id}/send-reminder', [App\Http\Controllers\Admin\RenewalController::class, 'sendManualReminder'])->name('admin.renewals.send');

    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update']);
    Route::post('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'updateAvatar']);
    Route::delete('/profile/avatar', [App\Http\Controllers\ProfileController::class, 'deleteAvatar']);
    Route::post('/profile/reset-password', [App\Http\Controllers\ProfileController::class, 'sendResetPasswordLink']);
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword']);
});

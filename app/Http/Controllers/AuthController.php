<?php

namespace App\Http\Controllers;

use DB;
use App\Helpers\Email;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\SignupRequest;
use App\Http\Requests\StoreUser;
use App\Http\Requests\StoreUserInvite;
use App\Http\Requests\PasswordValidateResetRequest;
use App\Services\AuthService;
use App\Services\PasswordResetService;
use App\Services\UserService;
use App\Models\Organization;
use App\Models\PasswordResetToken;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\TeamInvite;
use App\Models\User;
use App\Services\SocialLoginService;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\Models\Otp;
use App\Models\Notification;
use App\Mail\SignupOtpMail;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Session;
use Str;

class AuthController extends BaseController
{
    protected $userService;
    protected $role;
    protected $accessToken;

    public function __construct($role = 'user')
    {
        $this->userService = new UserService($role);
        $this->role = $role;
        $this->accessToken = 'EAApDmi22WZA0BO9z25XzJaJ66lrrVZBC5MtZA4lMaBa0J5qDv6vNlZAMKw8U8cMZBSv0IKe3PKh42rZADoJtNlZCH2Om9UsZBsTfTDfoeB8lzLK0pdvxeGPkJIBPUlEHlISmwefBpWDK8T4XduhBfHl23pwTzdGNr4ZAGxyAU8P9tHacWo8vCCzgfsS1G4CRvn29lpwZDZD';
    }

    public function showLoginForm()
    {
        $keys = ['logo', 'company_name', 'address', 'email', 'phone', 'socials', 'trial_period', 'allow_facebook_login', 'allow_google_login'];
        $data['companyConfig'] = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        return Inertia::render('Auth/Login', $data);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->where('deleted_at', null)->first();
        $guard = $user->role == 'admin' ? 'admin' : 'user';
        Auth::guard($guard)->attempt(['email' => $request->email, 'password' => $request->password]);

        //Check number of organizations
        if ($guard == 'user') {
            $teams = Team::where('user_id', auth()->user()->id);
            if ($teams->count() == 1) {
                $organizationId = $teams->first()->organization_id;
                session()->put('current_organization', $organizationId);

                // Check onboarding status for workspace owners
                if ($teams->first()->role === 'owner') {
                    $org = Organization::find($organizationId);
                    $meta = $org && $org->metadata ? json_decode($org->metadata, true) : [];
                    $onboardingStatus = $meta['onboarding']['status'] ?? 'NOT_STARTED';
                    if ($onboardingStatus !== 'COMPLETED') {
                        return redirect('/onboarding');
                    }
                }
            }
        }

        $this->createLoginNotification($user, $request);

        return redirect($user->role == 'admin' ? 'admin/dashboard' : '/dashboard');
    }

    public function handleLogin(StoreUser $request)
    {
        $user = $this->userService->store($request);
        $authService = (new AuthService($user))->authenticateSession($request);

        return redirect('/onboarding');
    }

    public function socialLogin(Request $request, $type)
    {
        if ($type === 'google') {
            return SocialLoginService::makeGoogleDriver()->redirect();
        } else if ($type === 'facebook') {
            //return Socialite::driver('facebook')->redirect();
            return SocialLoginService::makeFacebookDriver()->redirect();
        }
    }

    public function handleFacebookCallback(Request $request)
    {
        if ($request->has('error')) {
            return Redirect::route('login')->with(
                'status',
                [
                    'type' => 'success',
                    'message' => __('There was an error with Facebook login!')
                ]
            );
        }

        try {
            $facebookUser = SocialLoginService::makeFacebookDriver()->fields(['id', 'name', 'first_name', 'last_name', 'email', 'gender', 'verified'])->user();
            $user = User::where('facebook_id', $facebookUser->id)->where('status', '=', '1')->where('deleted_at', null)->first();

            if ($user) {
                if ($user->role == 'user') {
                    //Check if user belongs to organization, otherwise set one up
                    $team = Team::where('user_id', $user->id)->first();

                    if (!$team) {
                        //Create Organization
                        $organization = $this->createOrganization($user);

                        session()->put('current_organization', $organization->id);
                    }
                }

                $guard = $user->role == 'admin' ? 'admin' : 'user';
                Auth::guard($guard)->login($user);

                return redirect($user->role == 'admin' ? 'admin/dashboard' : '/dashboard');
            } else {
                DB::transaction(function () use ($facebookUser) {
                    // Check if the email exists and handle accordingly
                    $user = User::where('email', $facebookUser->email)->first();

                    if ($user) {
                        // Link the Facebook ID to the existing user
                        $user->facebook_id = $facebookUser->id;
                        $user->save();

                        //Check if user belongs to organization, otherwise set one up
                        $team = Team::where('user_id', $user->id)->first();

                        if (!$team) {
                            //Create Organization
                            $organization = $this->createOrganization($user);

                            session()->put('current_organization', $organization->id);
                        }
                    } else {
                        // Extract first name and last name
                        $nameParts = explode(' ', $facebookUser->name);
                        $firstName = $nameParts[0];
                        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                        // Create User
                        $user = new User();
                        $user->first_name = $firstName;
                        $user->last_name = $lastName;
                        $user->email = $facebookUser->email;
                        $user->facebook_id = $facebookUser->id;
                        $user->password = null;
                        $user->role = 'user';
                        $user->save();

                        //Create Organization
                        $organization = $this->createOrganization($user);

                        // Send Registration Email
                        Email::send('Registration', $user);

                        if (isset($config->value) && $config->value == '1') {
                            $user->sendEmailVerificationNotification();
                        }

                        session()->put('current_organization', $organization->id);
                    }

                    // Log the user in
                    Auth::guard('user')->login($user, true);
                });

                return redirect('dashboard');
            }
        } catch (\Exception $e) {
            // Handle exception, possibly log the error and redirect to an error page
            Log::error('User registration failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Registration failed, please try again.');
        }
    }

    public function googleCallback(Request $request)
    {
        if ($request->has('error')) {
            return Redirect::route('login')->with(
                'status',
                [
                    'type' => 'success',
                    'message' => __('There was an error with Google login!')
                ]
            );
        }

        try {
            $gUser = SocialLoginService::makeGoogleDriver()->user();

            $user = User::where('email', $gUser->email)->where('status', '=', '1')->where('deleted_at', null)->first();

            if ($user) {
                $guard = $user->role == 'admin' ? 'admin' : 'user';
                Auth::guard($guard)->login($user);

                return redirect($user->role == 'admin' ? 'admin/dashboard' : '/dashboard');
            } else {
                //Create User
                $name = explode(" ", $gUser->user['name']);

                $user = new User();
                $user->first_name = $name[0];
                $user->last_name = isset($name[1]) ? $name[1] : NULL;
                $user->email = $gUser->email;
                $user->password = NULL;
                $user->role = 'user';
                $user->save();

                $timestamp = now()->format('YmdHis');
                $randomString = Str::random(4);

                //Create Organization
                $organization = Organization::create([
                    'identifier' => $timestamp . $user->id . $randomString,
                    'name' => $name[0] . "'s organization",
                    'timezone' => 'Asia/Kolkata',
                    'metadata' => json_encode(['timezone' => 'Asia/Kolkata']),
                    'created_by' => $user->id
                ]);

                //Create Team
                $team = Team::create([
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                    'role' => 'owner',
                    'status' => 'active',
                    'created_by' => $user->id
                ]);

                $config = Setting::where('key', 'trial_period')->first();
                $has_trial = isset($config->value) && $config->value > 0 ? true : false;

                //Create Subscription
                Subscription::create([
                    'organization_id' => $organization->id,
                    'status' => $has_trial ? 'trial' : 'active',
                    'plan_id' => null,
                    'start_date' => now(),
                    'valid_until' => $has_trial ? date('Y-m-d H:i:s', strtotime('+' . $config->value . ' days')) : now(),
                ]);

                Email::send('Registration', $user);

                if (isset($config->value) && $config->value == '1') {
                    $user->sendEmailVerificationNotification();
                }

                Auth::guard('user')->login($user, true);

                return redirect('/onboarding');
            }
        } catch (\Exception $e) {
            // Handle exception, possibly log the error and redirect to an error page
            Log::error('User registration failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Registration failed, please try again.');
        }
    }

    private function createOrganization($user)
    {
        $timestamp = now()->format('YmdHis');
        $randomString = Str::random(4);

        // Create Organization
        $organization = Organization::create([
            'identifier' => $timestamp . $user->id . $randomString,
            'name' => $user->first_name . "'s organization",
            'timezone' => 'Asia/Kolkata',
            'metadata' => json_encode(['timezone' => 'Asia/Kolkata']),
            'created_by' => $user->id
        ]);

        // Create Team
        $team = Team::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'active',
            'created_by' => $user->id
        ]);

        $config = Setting::where('key', 'trial_period')->first();
        $has_trial = isset($config->value) && $config->value > 0 ? true : false;

        // Create Subscription
        Subscription::create([
            'organization_id' => $organization->id,
            'status' => $has_trial ? 'trial' : 'active',
            'plan_id' => null,
            'start_date' => now(),
            'valid_until' => $has_trial ? date('Y-m-d H:i:s', strtotime('+' . $config->value . ' days')) : now(),
        ]);

        return $organization;
    }

    public function showRegistrationForm(Request $request)
    {
        $keys = ['logo', 'company_name', 'address', 'email', 'phone', 'socials', 'trial_period', 'allow_facebook_login', 'allow_google_login'];
        $data['companyConfig'] = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        $data['selectedPlan'] = $request->query('plan');
        if ($request->query('plan')) {
            session()->put('selected_plan', $request->query('plan'));
        }

        return Inertia::render('Auth/Register', $data);
    }

    public function handleRegistration(SignupRequest $request)
    {
        $user = $this->userService->store($request);
        $authService = (new AuthService($user))->authenticateSession($request);
        $config = Setting::where('key', 'verify_email')->first();

        if ($request->filled('plan')) {
            session()->put('selected_plan', $request->input('plan'));
        }

        if (isset($config->value) && $config->value == '1') {
            $user->sendEmailVerificationNotification();
        }

        return redirect('/onboarding');
    }

    public function viewInvite($uuid)
    {
        $invite = TeamInvite::where('code', $uuid)->first();

        if (!$invite) {
            return Redirect::route('login')->with(
                'status',
                [
                    'type' => 'success',
                    'message' => __('That page does not exist!')
                ]
            );
        } else {
            $data['organization'] = Organization::where('id', $invite->organization_id)->first();
            $data['user'] = User::where('email', $invite->email)->where('role', 'user')->first();
            $data['invite'] = $invite;
            $data['code'] = $uuid;

            $keys = ['logo', 'company_name', 'address', 'email', 'phone', 'socials', 'trial_period', 'allow_facebook_login', 'allow_google_login'];
            $data['companyConfig'] = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

            return Inertia::render('Auth/Invite', $data);
        }
    }

    public function invite(StoreUserInvite $request, $inviteCode)
    {
        (new TeamService)->store($request, $inviteCode);

        return Redirect::route('dashboard');
    }

    public function showForgotForm(Request $request)
    {
        $keys = ['logo', 'company_name', 'address', 'email', 'phone', 'socials', 'trial_period'];
        $data['companyConfig'] = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        return Inertia::render('Auth/Forgot', $data);
    }

    public function createPasswordResetToken(PasswordResetRequest $request)
    {
        (new PasswordResetService)->generateResetLink($request->input('email'));

        return redirect('/forgot-password')->with(
            'status',
            [
                'type' => 'success',
                'message' => __('We\'ve sent you a password reset link to your email!')
            ]
        );
    }

    public function showResetForm(Request $request)
    {
        $email = $request->input('email');
        $token = $request->input('token');

        if (!(new PasswordResetService)->verifyResetCode($email, $token)) {
            return redirect('/login');
        }

        $keys = ['logo', 'company_name', 'address', 'email', 'phone', 'socials', 'trial_period'];
        $data['companyConfig'] = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        return Inertia::render('Auth/Reset', $data);
    }

    public function resetPassword(PasswordValidateResetRequest $request)
    {
        (new PasswordResetService)->resetPassword($request);

        return redirect('/login')->with(
            'status',
            [
                'type' => 'success',
                'message' => __('Password reset successful!')
            ]
        );
    }

    public function verifyEmail()
    {
        if (auth()->user()->email_verified_at != NULL) {
            return redirect('dashboard');
        } else {
            $keys = ['logo', 'company_name', 'address', 'email', 'phone', 'socials', 'trial_period'];
            $data['companyConfig'] = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

            return Inertia::render('Auth/VerifyEmail', $data);
        }
    }

    public function sendEmailVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with(
            'status',
            [
                'type' => 'success',
                'message' => __('Verification link sent!')
            ]
        );
    }

    public function sendOtp(SignupRequest $request)
    {
        $normalizedEmail = strtolower(trim((string) $request->input('email')));

        // Check email uniqueness before sending OTP
        if (User::whereRaw('LOWER(email) = ?', [$normalizedEmail])->whereNull('deleted_at')->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('An account with this email already exists. Please log in or use a different email address.'),
                'errors' => [
                    'email' => [__('An account with this email already exists. Please log in or use a different email address.')]
                ]
            ], 422);
        }

        // Cryptographically secure 6-digit OTP
        $otp = sprintf('%06d', random_int(100000, 999999));
        $hashedOtp = Hash::make($otp);

        Otp::updateOrCreate(
            ['email' => $normalizedEmail],
            [
                'phone' => $request->input('phone'),
                'otp' => $hashedOtp,
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
                'verified_at' => null,
            ]
        );

        // Store registration info in session for completion upon verification
        session()->put('pending_registration_' . md5($normalizedEmail), [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'organization_name' => $request->input('organization_name'),
            'email' => $normalizedEmail,
            'phone' => $request->input('phone'),
            'password' => $request->input('password'),
            'plan' => $request->input('plan'),
        ]);

        try {
            Mail::to($normalizedEmail)->send(new SignupOtpMail(
                (string) $request->input('first_name', 'User'),
                $otp,
                10
            ));
        } catch (\Throwable $e) {
            Log::warning('Email delivery failed for OTP: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'step' => 'verify_otp',
            'email' => $normalizedEmail,
            'message' => __('Verification code sent to your email ID.'),
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid verification code format. Please enter a 6-digit code.'),
                'errors' => $validator->errors()
            ], 422);
        }

        $normalizedEmail = strtolower(trim((string) $request->input('email')));
        $inputOtp = (string) $request->input('otp');

        $otpRecord = Otp::where('email', $normalizedEmail)->latest()->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => __('Verification code not found. Please request a new OTP.')
            ], 422);
        }

        if ($otpRecord->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => __('Verification code has expired. Please request a new OTP.')
            ], 422);
        }

        if ($otpRecord->attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => __('Too many incorrect attempts. Please request a new OTP.')
            ], 422);
        }

        if (!$otpRecord->isValid($inputOtp)) {
            $otpRecord->increment('attempts');
            $remaining = max(0, 5 - $otpRecord->attempts);
            return response()->json([
                'success' => false,
                'message' => __('Invalid verification code. :attempts attempt(s) remaining.', ['attempts' => $remaining])
            ], 422);
        }

        // OTP verified
        $otpRecord->update(['verified_at' => now()]);

        // Retrieve registration data from session
        $pendingData = session()->get('pending_registration_' . md5($normalizedEmail));

        // If session was cleared (e.g. different browser tab), fallback to request attributes if passed
        if (!$pendingData) {
            $pendingData = [
                'first_name' => $request->input('first_name', 'User'),
                'last_name' => $request->input('last_name', ''),
                'organization_name' => $request->input('organization_name'),
                'email' => $normalizedEmail,
                'phone' => $request->input('phone', $otpRecord->phone),
                'password' => $request->input('password', Str::random(16)),
                'plan' => $request->input('plan'),
            ];
        }

        // Double check email uniqueness before final insert
        if (User::whereRaw('LOWER(email) = ?', [$normalizedEmail])->whereNull('deleted_at')->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('An account with this email already exists. Please log in or use a different email address.')
            ], 422);
        }

        $user = User::create([
            'first_name' => $pendingData['first_name'],
            'last_name' => $pendingData['last_name'],
            'email' => $normalizedEmail,
            'phone' => $pendingData['phone'] ?? null,
            'password' => Hash::make($pendingData['password']),
            'role' => 'user',
            'status' => '1',
            'email_verified_at' => now(),
        ]);

        // Create Organization
        $organizationName = !empty($pendingData['organization_name'])
            ? $pendingData['organization_name']
            : ($user->first_name . "'s organization");

        $timestamp = now()->format('YmdHis');
        $randomString = Str::random(4);
        $organization = Organization::create([
            'identifier' => $timestamp . $user->id . $randomString,
            'name' => $organizationName,
            'timezone' => 'Asia/Kolkata',
            'metadata' => json_encode(['timezone' => 'Asia/Kolkata']),
            'created_by' => $user->id
        ]);

        // Create Team Owner
        Team::create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'status' => 'active',
            'created_by' => $user->id
        ]);

        $trialConfig = Setting::where('key', 'trial_period')->first();
        $trialDays = (isset($trialConfig->value) && (int) $trialConfig->value > 0) ? (int) $trialConfig->value : 14;

        Subscription::create([
            'organization_id' => $organization->id,
            'status' => 'trial',
            'plan_id' => null,
            'start_date' => now(),
            'valid_until' => now()->addDays($trialDays),
        ]);

        // Clean up pending session
        session()->forget('pending_registration_' . md5($normalizedEmail));

        // Authenticate
        Auth::guard('user')->login($user, true);
        session()->put('current_organization', $organization->id);

        // Create Login Notification
        $this->createLoginNotification($user, $request);

        return response()->json([
            'success' => true,
            'message' => __('Email verified successfully! Workspace ready.'),
            'redirect' => '/onboarding',
        ]);
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('Invalid email address.'),
                'errors' => $validator->errors()
            ], 422);
        }

        $normalizedEmail = strtolower(trim((string) $request->input('email')));
        $otpRecord = Otp::where('email', $normalizedEmail)->latest()->first();

        // Enforce 60-second cooldown
        if ($otpRecord && $otpRecord->updated_at && now()->diffInSeconds($otpRecord->updated_at) < 60) {
            $remaining = 60 - now()->diffInSeconds($otpRecord->updated_at);
            return response()->json([
                'success' => false,
                'message' => __('Please wait :seconds seconds before requesting another code.', ['seconds' => $remaining]),
                'seconds_remaining' => $remaining
            ], 429);
        }

        $otp = sprintf('%06d', random_int(100000, 999999));
        $hashedOtp = Hash::make($otp);

        Otp::updateOrCreate(
            ['email' => $normalizedEmail],
            [
                'otp' => $hashedOtp,
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
                'verified_at' => null,
            ]
        );

        $pendingData = session()->get('pending_registration_' . md5($normalizedEmail));
        $firstName = $pendingData['first_name'] ?? 'User';

        try {
            Mail::to($normalizedEmail)->send(new SignupOtpMail($firstName, $otp, 10));
        } catch (\Throwable $e) {
            Log::warning('Email delivery failed for resend OTP: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => __('A new verification code has been sent to your email.'),
        ]);
    }

    public function createLoginNotification(User $user, Request $request): void
    {
        try {
            $now = now()->setTimezone(config('app.timezone', 'Asia/Kolkata'));
            $userAgent = (string) $request->header('User-Agent');
            $device = 'Web Browser';

            if (str_contains($userAgent, 'Edg')) {
                $device = 'Microsoft Edge';
            } elseif (str_contains($userAgent, 'Chrome')) {
                $device = 'Google Chrome';
            } elseif (str_contains($userAgent, 'Safari')) {
                $device = 'Apple Safari';
            } elseif (str_contains($userAgent, 'Firefox')) {
                $device = 'Mozilla Firefox';
            } elseif (str_contains($userAgent, 'Postman') || str_contains($userAgent, 'curl')) {
                $device = 'API Client';
            }

            Notification::create([
                'user_id' => $user->id,
                'title' => __('New Login'),
                'comment' => __('Your Wappiyo account was successfully logged in on :date at :time from :device.', [
                    'date' => $now->format('M d, Y'),
                    'time' => $now->format('h:i A'),
                    'device' => $device,
                ]),
                'url' => '/dashboard',
                'seen' => false,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to create login notification: ' . $e->getMessage());
        }
    }


    public function logout()
    {
        Auth::guard('user')->logout();
        Session::flush();

        return redirect('login');
    }
}
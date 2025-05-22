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
use Carbon\Carbon;
use Inertia\Inertia;
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
                session()->put('current_organization', $teams->first()->organization_id);
            }
        }

        return redirect($user->role == 'admin' ? 'admin/dashboard' : '/dashboard');
    }

    public function handleLogin(StoreUser $request)
    {
        $user = $this->userService->store($request);
        $authService = (new AuthService($user))->authenticateSession($request);

        return redirect('/dashboard');
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

                return redirect('dashboard');
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

    public function showRegistrationForm()
    {
        $keys = ['logo', 'company_name', 'address', 'email', 'phone', 'socials', 'trial_period', 'allow_facebook_login', 'allow_google_login'];
        $data['companyConfig'] = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();

        return Inertia::render('Auth/Register', $data);
    }

    public function handleRegistration(SignupRequest $request)
    {
        $user = $this->userService->store($request);
        $authService = (new AuthService($user))->authenticateSession($request);
        $config = Setting::where('key', 'verify_email')->first();

        if (isset($config->value) && $config->value == '1') {
            $user->sendEmailVerificationNotification();
        }

        return redirect('/dashboard');
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

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->input('phone');
        $otp = rand(1000, 9999);

        Otp::updateOrCreate(
            ['phone' => $phone],
            ['otp' => $otp]
        );

        // $formattedphone = $phone;
        $formattedPhoneE164 = phone($request->input('phone'), $request->input('country'))->formatE164();
        $formattedphone = ltrim($formattedPhoneE164, '+');


        try {
            // $response = Http::withHeaders([
            //     'accept' => 'application/json',
            //     'authkey' => '282280AEE4Kj2nzJG67fe5ee3P1',
            //     'content-type' => 'application/json',
            // ])->post('https://control.msg91.com/api/v5/flow', [
            //             'template_id' => '677fb3c1d6fc05016e7de192',
            //             'recipients' => [
            //                 [
            //                     'mobiles' => $formattedphone,
            //                     'var1' => $otp,
            //                 ]
            //             ]
            //         ]);

            // Log::info('MSG91 OTP Send Response:', $response->json());

            $url = "https://graph.facebook.com/v18.0/675025299022404/messages";
            $response = Http::withToken($this->accessToken)->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $formattedphone, // Format: 15551234567
                'type' => 'template',
                'template' => [
                    'name' => 'singup',
                    'language' => [
                        'code' => 'en'
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $otp
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            //Log::info('Whatsaoo OTP Send Response:', $response->json());
            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP via SMS'
                ], 500);
            }


            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully on your whatsapp number'
            ]);
        } catch (\Exception $e) {
            Log::error('MSG91 Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error sending OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|phone',
            'otp' => 'required|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid input',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->input('phone');
        $otp = $request->input('otp');

        $otpRecord = Otp::where('phone', $phone)->first();

        if (!$otpRecord) {
            return response()->json([ 'success' => false,'message' => 'OTP not found'], 422);
        }

        if ($otpRecord->otp !== $otp) {
            return response()->json([ 'success' => false,'message' => 'Invalid OTP'], 422);
        }

        // OTP verified, remove record
        $otpRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'OTP verified'
        ]);

    }


    public function logout()
    {
        Auth::guard('user')->logout();
        Session::flush();

        return redirect('login');
    }
}
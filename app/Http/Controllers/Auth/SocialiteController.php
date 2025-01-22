<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\MonnifyToken;
use App\Models\MonnifyAccounts;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleGoogleCallback(Request $request)
    {
        try {

            $user = Socialite::driver('google')->user();
            
            $firstName = $user->user['given_name']; // Google provides 'given_name' for first name
            $lastName = $user->user['family_name'];// Google provides 'family_name' for last name
            
            $googleId = $user->id;

            $finduser = User::where('google_id', $user->id)->first();
            $existuser = User::where('email', $user->email)->first();

            if ($finduser || $existuser) {

                // Determine the user to log in: either by Google ID or email
                $userToLogin = $finduser ?: $existuser;
            
                // Log in the user
                Auth::guard('web')->login($userToLogin);
            
                // Regenerate session after login
                $request->session()->regenerate();
            
                // Get the authenticated user's ID
                $id = Auth::guard('web')->id();
                $user = User::findOrFail($id);
            
                // Save the session ID
                $user->google_id = $googleId;
                $user->session_id = session()->getId();
                $user->save();
            
                // Redirect to the user dashboard
                return redirect()->route('user.dashboard');

            } else {
                // Generate a secure random password
                $username = $firstName . ' ' .$lastName;
                $password = Str::random(12);
                $name = strtolower(str_replace(' ', '', $username));

                $newUser = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => Hash::make($password),
                    'is_email_verified' => 1,
                ]);
                
                $referral_code = $name;
                $newUser->first_name = $firstName;
                $newUser->last_name = $lastName;
                $newUser->referral_code = $referral_code;
                $newUser->is_email_verified = 1;
                $newUser->save();
        
                /*$token = Str::random(64);
        
                Mail::send('user.auth.mails.verify', ['token' => $token,
                                                      'email' => $email], function($message) use($request){
                    $message->to($request->email);
                    $message->subject('Email Verification');
                    });
        
                UserVerify::create([
                    'user_id' => $user->id,
                    'token' => $token
                ]); */
        
                if (session()->has('referrer')) {
                    $referrerCode = session('referrer');
                    $referrer = User::where('referral_code', $referrerCode)->first();

                    if ($referrer) {
                        Referral::create([
                            'referrer_id' => $referrer->id,
                            'user_id' => $newUser->id,
                        ]);

                        $this->userBonus($newUser);
                        $this->referrerBonus($referrer);
                    }

                    session()->forget('referrer');
                }

                Auth::guard('web')->login($newUser);

                $request->session()->regenerate();

                $id = Auth::guard('web')->id();
                $user = User::findOrFail($id);
        
                $user->session_id = session()->getId();
                $user->save();
        
                return redirect()->route('user.dashboard');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function handleFacebookCallback()
    {
        try {

            $user = Socialite::driver('facebook')->user();

            // Check if the email is verified
            if (!$user->hasValidEmail()) {
                // Handle unverified email address (optional)
                return redirect()->route('login')->with('error', 'Facebook email is not verified.');
            }

            $finduser = User::where('facebook_id', $user->id)->first();

            if($finduser){

                Auth::login($finduser);

                return redirect()->route('user.dashboard');

            }else{

                // Generate a secure random password
                $password = Str::random(12);
                $username = Str::random(6);

                $newUser = User::updateOrCreate(['email' => $user->email],[
                        'name' => $user->name,
                        'username' => $username,
                        'facebook_id'=> $user->id,
                        'password' => Hash::make($password),
                    ]);

                    if (session()->has('referrer')) {
                        $referrerCode = session('referrer');
                        $referrer = User::where('referral_code', $referrerCode)->first();

                        if ($referrer) {
                            Referral::create([
                                'referrer_id' => $referrer->id,
                                'user_id' => $newUser->id,
                            ]);

                            $this->userBonus($newUser);
                            $this->referrerBonus($referrer);
                        }

                        session()->forget('referrer');
                    }

                Auth::login($newUser);

                session()->regenerate();

                return redirect()->route('user.dashboard');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    //Referral bonus for new user
    function userBonus($newUser) {
        $newUser->referral_balance += 1000;
        $newUser->save();

        $trx = new Transaction();
        $trx->user_id = $newUser->id;
        $trx->amount = 100;
        $trx->post_balance = $newUser->balance;
        $trx->trx_type = '+';
        $trx->details = 'Received referral bonus';
        $trx->trx = getTrx();
        $trx->save();
    }

    //Referral Bonus for referer
    function referrerBonus($referrer) {

        $referrer->referral_balance += 1000;
        $referrer->save();

        $credit = new Transaction;
        $credit->user_id = $referrer->id;
        $credit->amount = 100;
        $credit->post_balance = $referrer->balance;
        $credit->trx_type = '+';
        $credit->details = 'Received referral bonus';
        $credit->trx = getTrx();
        $credit->save();
    }
}

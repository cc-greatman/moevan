<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\Referral;
use App\Models\MonnifyToken;
use App\Models\MonnifyAccounts;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserVerify;
use App\Rules\ReCaptchaV3;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * Display register page.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request) {
        $pageTitle = "Sign up - Moevan";

        if ($request->has('ref')) {
            session(['referrer' => $request->query('ref')]);
        }

        return view('user.auth.register', compact('pageTitle'));
    }

    /**
     * Handle account registration request
     *
     * @param RegisterRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request) {

        $user = User::create($request->validated());

        if ($request->input('referral_code')) {
            $referrer = User::where('referral_code', $request->input('referral_code'))->first();

                if ($referrer) {
                    Referral::create([
                        'referrer_id' => $referrer->id,
                        'user_id' => $user->id,
                    ]);

                    $referral_code = strtolower(str_replace(' ', '', $referrer->username));
                    $referrer->referral_code = $referral_code;
                    $referrer->save();

                } else {

                    return redirect()->back()->with('error', 'Invalid referral code.');
            }
        }

        $email = $user->email;

        $referral_code = strtolower(str_replace(' ', '', $user->username));
        $user->referral_code = $referral_code;
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->phone = $request->phone;
        $user->save();

        $token = Str::random(64);

        Mail::send('user.auth.mails.verify', ['token' => $token,
                                              'email' => $email], function($message) use($request){
            $message->to($request->email);
            $message->subject('Email Verification');
            });

        UserVerify::create([
            'user_id' => $user->id,
            'token' => $token
        ]);

        session()->forget('referrer');

        return redirect('login')->with('success', 'Your Moevan account has been created successfully! <br> Please login to start enjoying our services.');
    }

    function userBonus($user) {
        $user->referral_balance += 1000;
        $user->save();

        $trx = new Transaction;
        $trx->user_id = $user->id;
        $trx->amount = 10;
        $trx->post_balance = $user->balance;
        $trx->trx_type = '+';
        $trx->details = 'Received referral bonus';
        $trx->trx = getTrx();
        $trx->save();
    }

    function referrerBonus($referrer) {

        $referrer->referral_balance += 1000;
        $referrer->save();

        $credit = new Transaction;
        $credit->user_id = $referrer->id;
        $credit->amount = 10;
        $credit->post_balance = $referrer->balance;
        $credit->trx_type = '+';
        $credit->details = 'Received referral bonus';
        $credit->trx = getTrx();
        $credit->save();
    }
}

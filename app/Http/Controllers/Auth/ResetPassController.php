<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPassController extends Controller
{
    public function index() {

        $pageTitle = "Forgot Password - Moevan";

        return view('user.auth.forgot', compact('pageTitle'));
    }

    public function sendLink(Request $request) {

        $request->validate([
                'email' => 'required|email|exists:users'
            ],
            [
                'email.exists' => 'We can\'t find this email in our database'
        ]);
    
        $token = Str::random(64);
            
        $exists = DB::table('password_reset_tokens')->where('email', $request->email)->first();
        
        if($exists) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        }

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        Mail::send('user.auth.mails.forgot-pass', ['token' => $token], function($message) use($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return back()->with('success', 'We have e-mailed your password reset link!');
    }

    public function show($token) {

        $pageTitle = "Reset your password - Moevan";

        return view('user.auth.reset', compact('pageTitle'), ['token' => $token]);
    }

    public function perform(Request $request) {

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ],
        [
            'email.exists' => 'Use email registered with us!'
    ]);

        $updatePassword = DB::table('password_reset_tokens')
                            ->where([
                                'email' => $request->email,
                                'token' => $request->token
                            ])->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('user.login.show')->with('success', 'Your Password has been updated successfully');
    }
}

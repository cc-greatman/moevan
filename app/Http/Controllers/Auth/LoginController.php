<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Rules\ReCaptchaV3;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show() {
        $pageTitle = "Sign in - Moevan";

        session()->invalidate();
        session()->regenerateToken();

        return view('user.auth.login', compact('pageTitle'));
    }

    /**
     * Display admin page.
     *
     * @return Renderable
     */
    public function adminShow() {
        $pageTitle = "Admin - Moevan";

        return view('admin.auth.login', compact('pageTitle'));
    }

    /**
     * Handle admin login request
     *
     * @param Request $request
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.show.dashboard'));
        }

        return redirect()->route('admin.login.show')
        ->withErrors(trans('auth.failed'));
    }


    /**
     * Handle account login request
     *
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {

        $credentials = $request->getCredentials();

        if(!Auth::guard('user')->validate($credentials)):
            return redirect()->to('login')
                ->withErrors(trans('auth.failed'));
        endif;

        $user = Auth::getProvider()->retrieveByCredentials($credentials);

        Auth::login($user);

        return $this->authenticated($request, $user);
    }

    /**
     * Handle response after user authenticated
     *
     * @param Request $request
     * @param Auth $user
     *
     * @return \Illuminate\Http\Response
     */
    protected function authenticated(Request $request, $user)
    {
        $request->session()->regenerate();

        $id = Auth::guard('web')->id();
        $user = User::findOrFail($id);

        $user->session_id = session()->getId();
        $user->save();

        session()->put('fresh_login', true);

        return redirect()->route('user.dashboard');
    }
}

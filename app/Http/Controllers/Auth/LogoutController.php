<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Session as Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Log out account user.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function perform()
    {
        $id = Auth::guard('web')->id();
        $user = User::findOrFail($id);
        $user->session_id = null;
        $user->save();

        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect('login');
    }

    /**
     * Log out admin user.
     *
     * @return \Illuminate\Routing\Redirector
     */
    public function logout() {

        Session::flush();

        Session::invalidate();

        Auth::guard('admin')->logout();

        return redirect()->route('admin.login.show');
    }
}

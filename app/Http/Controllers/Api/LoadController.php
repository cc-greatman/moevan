<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LoadController extends Controller
{
    public function wallet(Request $request) {

        // Validate the request
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user has a wallet balance (assuming a `wallet_balance` column exists)
        $walletBalance = $user->balance ?? 0.00;

        // Return the JSON response
        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => [
                'product' => [
                    'wallet' => number_format($walletBalance, 2),
                ],
            ],
        ]);
    }
}

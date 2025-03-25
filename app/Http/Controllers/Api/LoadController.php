<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Networks;
use App\Models\User;
use Illuminate\Http\Request;

class LoadController extends Controller
{
    public function wallet(Request $request) {

        // Validate the request
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $apiKey = $request->header('Authorization');
        $apiKey = str_replace('Bearer ', '', $apiKey);

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'code' => 404, 'message' => 'User not found']);
        }

        if ($user->api_key !== $apiKey) {
            return response()->json(['status' => false, 'code' => 403, 'message' => 'User Api Key does not match'], 403);
        }

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

    public function networks(Request $request) {

        // Validate the request
        $request->validate([
            'networks' => 'required|string|in:all',
        ]);

        // Retrieve all networks
        $networks = Networks::all()->map(function ($network) {
            return [
                'network' => $network->name,
                'network_id' => $network->network_id,
            ];
        });

        // Return response
        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => [
                'product' => $networks,
            ],
        ]);
    }

    public function dataTypes(Request $request) {
        
        // Validate request
        $request->validate([
            'network_id' => 'required|string|exists:networks,network_id',
        ]);

        // Get the network
        $network = Networks::with(['dataPlans' => function ($query) {
            $query->select('type', 'network_id')->distinct(); // Get unique data types for the network
        }])->where('network_id', $request->network_id)->first();

        if (!$network) {
            return response()->json(['status' => false, 'code' => 404, 'message' => 'Network not found'], 404);
        }

        // Extract unique data types from data plans
        $dataTypes = $network->dataPlans->map(function ($plan) use ($network) {
            return [
                'network' => $network->name,
                'networkId' => $network->network_id,
                'name' => optional($plan->dataType)->name, // Get data type name from relationship
            ];
        })->unique('name')->values(); // Ensure unique data types

        // Return response
        return response()->json([
            'status' => true,
            'code' => 200,
            'data' => [
                'product' => $dataTypes,
            ],
        ]);
    }
}

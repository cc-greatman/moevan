<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProxyController extends Controller
{
    public function validateSmartcard(Request $request) {
        
        // Define the external API URL
        $url = 'https://autopilotng.com/api/live/v1/validate/smartcard-no';

        // Send the request to the external API with necessary headers and body
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('AUTOPILOT_API_KEY'),
            'X-CSRF-TOKEN' => csrf_token()
        ])->post($url, [
            'cableType' => $request->input('cableType'),
            'smartCardNo' => $request->input('smartCardNo'),
        ]);
        
        // Temporary test in your controller
        \Log::info(env('AUTOPILOT_API_KEY'));
        // Log response headers and body for inspection
        \Log::info('API Response Headers: ' . json_encode($response->headers()));
        \Log::info('API Response Body: ' . json_encode($response->json()));

        // Return the response from the external API
        return response()->json($response->json(), $response->status());
    }
}

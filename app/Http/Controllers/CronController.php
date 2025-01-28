<?php

namespace App\Http\Controllers;

use App\Models\ProvidersSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    public function syncGotv() {

        // Make the API request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('AUTOPILOT_API_KEY'),
        ])->post('https://autopilotng.com/api/live/v1/load/cable-packages', [
            'cableType' => 'GOTV'
        ]);

        // Get the response data as an array
        $responseData = $response->json();

        // Check if the request was successful
        if (isset($responseData['code']) && ($responseData['code'] == '200')) {
            // Loop through the 'product' array in the response
            foreach ($responseData['data']['product'] as $product) {
                ProvidersSubscription::updateOrCreate(
                    ['variation_code' => $product['planId']], // Update if the variation_code exists
                    [
                        'providers' => 2, // Static value for providers
                        'variation_code' => $product['planId'],
                        'name' => $product['planName'],
                        'amount' => $product['ourPrice'],
                    ]
                );
            }

            Log::info('Cable packages populated successfully.');
        } else {
            Log::error('Failed to retrieve cable packages. Response code: ' . $responseData['code']);
        }
    }

    public function syncDstv() {

        // Make the API request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('AUTOPILOT_API_KEY'),
        ])->post('https://autopilotng.com/api/live/v1/load/cable-packages', [
            'cableType' => 'DSTV'
        ]);

        // Get the response data as an array
        $responseData = $response->json();

        // Check if the request was successful
        if (isset($responseData['code']) && ($responseData['code'] == '200')) {
            // Loop through the 'product' array in the response
            foreach ($responseData['data']['product'] as $product) {
                ProvidersSubscription::updateOrCreate(
                    ['variation_code' => $product['planId']], // Update if the variation_code exists
                    [
                        'providers' => 1, // Static value for providers
                        'variation_code' => $product['planId'],
                        'name' => $product['planName'],
                        'amount' => $product['ourPrice'],
                    ]
                );
            }

            Log::info('Cable packages populated successfully.');
        } else {
            Log::error('Failed to retrieve cable packages. Response code: ' . $responseData['code']);
        }
    }


    public function syncStar() {

        // Make the API request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('AUTOPILOT_API_KEY'),
        ])->post('https://autopilotng.com/api/live/v1/load/cable-packages', [
            'cableType' => 'STARTIMES'
        ]);

        // Get the response data as an array
        $responseData = $response->json();

        // Check if the request was successful
        if (isset($responseData['code']) && ($responseData['code'] == '200')) {
            // Loop through the 'product' array in the response
            foreach ($responseData['data']['product'] as $product) {
                ProvidersSubscription::updateOrCreate(
                    ['variation_code' => $product['planId']], // Update if the variation_code exists
                    [
                        'providers' => 3, // Static value for providers
                        'variation_code' => $product['planId'],
                        'name' => $product['planName'],
                        'amount' => $product['ourPrice'],
                    ]
                );
            }

            Log::info('Cable packages populated successfully.');
        } else {
            Log::error('Failed to retrieve cable packages. Response code: ' . $responseData['code']);
        }
    }

    public function syncShow() {

        // Make the API request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . env('AUTOPILOT_API_KEY'),
        ])->post('https://autopilotng.com/api/live/v1/load/cable-packages', [
            'cableType' => 'SHOWMAX'
        ]);

       // Get the response data as an array
        $responseData = $response->json();

        // Check if the request was successful
        if (isset($responseData['code']) && ($responseData['code'] == '200')) {
            // Loop through the 'product' array in the response
            foreach ($responseData['data']['product'] as $product) {
                ProvidersSubscription::updateOrCreate(
                    ['variation_code' => $product['planId']], // Update if the variation_code exists
                    [
                        'providers' => 4, // Static value for providers
                        'variation_code' => $product['planId'],
                        'name' => $product['planName'],
                        'amount' => $product['ourPrice'],
                    ]
                );
            }

            Log::info('Cable packages populated successfully.');
        } else {
            Log::error('Failed to retrieve cable packages. Response code: ' . $responseData['code']);
        }
    }
}

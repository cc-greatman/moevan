<?php

namespace App\Http\Controllers;

use App\Models\ProvidersSubscription;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Env;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function syncServiceVariations()
    {
        $apiUrl = 'https://api-service.vtpass.com/api/service-variations?serviceID=showmax'; // Replace with your actual API URL
        $response = Http::get($apiUrl);

        if ($response->successful()) {
            $apiData = $response->json();

            // Get existing records from the database
            $existingVariations = ProvidersSubscription::all()->keyBy('variation_code');

            // Process API variations
            foreach ($apiData['content']['variations'] as $variation) {
                $variationCode = $variation['variation_code'];

                // Update or insert records
                $existingVariation = $existingVariations->get($variationCode);
                if ($existingVariation) {
                    // Update existing record
                    $existingVariation->update([
                        'name' => $variation['name'],
                        'variation_amount' => $variation['variation_amount'],
                    ]);
                } else {
                    // Insert new record
                    $create = ProvidersSubscription::create([
                        'variation_code' => $variationCode,
                        'name' => $variation['name'],
                        'amount' => $variation['variation_amount'],
                    ]);

                    $create->providers = 4;
                    $create->save();
                }
            }

            // Delete records not present in the API response
            $existingVariations->each(function ($existingVariation, $variationCode) use ($apiData) {
                if (collect($apiData['content']['variations'])->contains('variation_code', $variationCode)) {
                    $existingVariation->delete();
                }
            });

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Failed to fetch API data']);
    }

    public function verifyMeter(Request $request)
    {


        // Parameters for the API request
        $apiUrl = 'https://sandbox.vtpass.com/api/merchant-verify';
        $apiKey = 'a7d3d2701169b0f7ad475f2b1e04c244';
        $secretKey = 'SK_14767e0a4339394497d35c4ba3cf3d005d2b90e5e7e';

        // Make the API request to verify the meter number
        $client = new Client();

        try {
            $response = $client->post($apiUrl, [
                'headers' => [
                    'api-key' => $apiKey,
                    'secret-key' => $secretKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'billersCode' => '1212121212',
                    'serviceID' => 'dstv',
                ],
            ]);

            $apiResponse = json_decode($response->getBody(), true);

            // Display the API response in JSON format
            return response()->json($apiResponse);
        } catch (\Exception $e) {
            // Return an error response for any exception
            return response()->json([
                'success' => false,
                'message' => 'Error making API request',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function postData()
    {
        $baseUrl = 'https://autopilotng.com/api/live'; // Replace with your actual base URL
        $api_key = Env::get('AUTOPILOT_API_KEY'); // Replace with your actual API key

        $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $api_key,
            ])
            ->post($baseUrl . '/v1/load/data-types', [
                'networkId' => '4',
            ]);

        // You can handle the response as needed
        $statusCode = $response->status(); // Get the HTTP status code
        $responseData = $response->json(); // Get the response data in JSON format

        // Additional processing based on your requirements

        return $responseData;
    }

    public function success() {


        return view('user.messages.success');
    }

    public function error() {

        if (!session()->has('error')) {
            return redirect()->route('user.dashboard');
        }

        return view('user.messages.error');
    }

    public function warning() {

        if (!session()->has('warning')) {
            return redirect()->route('user.dashboard');
        }

        return view('user.messages.warning');
    }
}

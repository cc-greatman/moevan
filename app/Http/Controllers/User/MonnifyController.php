<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MonnifyToken;
use App\Models\MonnifyAccounts;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletTransactions;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MonnifyController extends Controller
{
    public function generateToken() {

        MonnifyToken::truncate();

        $api_key = config('app.monnify_api_key');
        $secret_key = config('app.monnify_secret_key');

        $credentials = base64_encode("$api_key:$secret_key");

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $credentials,
            'Content-Type' => 'application/json', // Add Content-Type header
        ])->post('https://api.monnify.com/api/v1/auth/login');

        $responseData = $response->json();
        $accessToken = $responseData['responseBody']['accessToken'];
        $expiresIn = $responseData['responseBody']['expiresIn'];

        MonnifyToken::create([
            'access_token' => $accessToken,
            'expires_at' => now()->addSeconds($expiresIn)
        ]);
    }

    public function viewManual() {

        $pageTitle = "Manual Funding - Moevan";

        return view('user.topup.transfer', compact('pageTitle'));
    }

    public function processManual(Request $request) {

        // Validate request
        $validated = $request->validate([
                                    'bank' => 'required|string',
                                    'amount' => 'required|numeric',
                                    'accountNo' => 'required|numeric',
                                    'reference' => 'required|string|max:255',
                                ]);

        $ref = $this->generateRequestId();

        try {
            // Your Autopilot API key
            $ipayngApiKey = env('IPAYNG_API_KEY');

            // Base URL of the API
            $baseUrl = 'https://ipayng.com/api/live/v1';

            $body = [
                'accountNo' => $validated['accountNo'],
                'sessionIdOrReference' => $validated['reference'],
                'amount' => $validated['amount'],
                'reference' => $ref
            ];

            $head = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $ipayngApiKey,
            ];

            // Make the HTTP request
            $responses = Http::withHeaders($head)->timeout(38001)->post($baseUrl . '/automanual', $body);

            $responseData = $responses->json();

            // Check response status and content
            if (isset($responseData['code']) && in_array($responseData['code'], ['200', '201', '500'])) {

                $apiResponse = $responseData['data']['message'];

                $user = auth()->guard('web')->user();

                // Generate transaction reference
                $trx = getTrx(); // Assuming getTrx() generates a transaction reference

                // Pre-balance before transaction
                $preBal = $user->balance;

                // Create WalletTransactions record with status '2' (pending)
                $walletTransaction = new WalletTransactions();
                $walletTransaction->user_id = $user->id;
                $walletTransaction->sender_name = $validated['reference'];
                $walletTransaction->pre_bal = $preBal;
                $walletTransaction->amount = $request->amount;
                $walletTransaction->post_bal = $user->balance;
                $walletTransaction->reference_number = $ref;
                $walletTransaction->mode = "Manual Funding";
                $walletTransaction->details = $responseData['data']['message'];
                $walletTransaction->status = '2'; // Pending status
                $walletTransaction->type = "+";
                $walletTransaction->receipt_path = $responseData['data']['reference'];
                $walletTransaction->trx = $trx;
                $walletTransaction->save();

                // Create Transaction record with status '2' (pending)
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $request->amount;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type = '+';
                $transaction->details = 'Pending approval';
                $transaction->status = '2'; // Pending status
                $transaction->trx = $trx;
                $transaction->save();

                return redirect()->route('user.trx.warning')->with('warning', $apiResponse);
            } else {

                $message = $responseData['data']['message'];

                return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
            }

        } catch (\Exception $e) {

            $message = $responseData['data']['message'];

            Log::error('Caught exception: ' . get_class($e) . ' - ' . $e->getMessage());
            return redirect()->route('user.trx.error')->with('error', 'Network is currently unavailable.');
        }
    }

    public function reserveAccount() {

        $contractCode = config('app.monnify_contract_code');

        $accessToken = MonnifyToken::first();
        $yourAccessToken = $accessToken->access_token;

        // Make a POST request to the Monnify API endpoint
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $yourAccessToken,
        ])->get('https://api.monnify.com/api/v1/banks');

        $responseData = $response->json();

        return $responseData;
    }

    public function generateRequestId() {

        // Set the timezone to GMT+1
        $timezone = new DateTimeZone('Europe/Berlin'); // GMT+1

        // Get the current date and time
        $now = new DateTime('now', $timezone);

        // Format the date and time as YYYYMMDDHHII
        $formattedDateTime = $now->format('YmdHi');

        // Generate a random alphanumeric string (you can adjust the length as needed)
        $randomString = Str::random(9); // Adjust the length as needed

        // Concatenate the formatted date and time with the random string
        $requestId = $formattedDateTime . $randomString;

        // Check if the length of $requestId is at least 25 characters
        if (strlen($requestId) < 25) {
            // Calculate the number of additional characters needed
            $additionalCharactersNeeded = 25 - strlen($requestId);

            // Append additional random alphanumeric characters to reach 25 characters
            $additionalRandomString = Str::random($additionalCharactersNeeded);

            // Concatenate the additional random string
            $requestId .= $additionalRandomString;
        }

        // Use $requestId as needed
        return $requestId;
    }

}

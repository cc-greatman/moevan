<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DataType;
use App\Models\Networks;
use App\Models\Providers;
use App\Models\Settings;
use App\Models\Referral;
use App\Models\MonnifyToken;
use App\Models\GeneralSwitch;
use App\Models\MonnifyAccounts;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ViewController extends Controller
{
    public function home() {
        $pageTitle = "Home - Moevan Enterprise";

        $etisalat = Settings::find(3);
        $airtel = Settings::find(4);
        $glo = Settings::find(5);
        $mtn = Settings::find(6);

        return view('frontend.index', compact('pageTitle', 'etisalat', 'airtel', 'glo', 'mtn'));
    }

    public function index() {
        $pageTitle = "Dashboard - Moevan";

        $userReferralCount = Referral::where('referrer_id', Auth::id())->count();

        $popup = GeneralSwitch::find(4);

        $user = User::findorFail(Auth::guard('web')->user()->id);

        $settings = Settings::first();

        $moniepoint = null;
        $wema = null;
        $sterling = null;


        if($user->is_kyc_verified === 1) {

            $sterling = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '232')
                                            ->first();

            if ($sterling === null) {
                $contractCode = config('app.monnify_contract_code');

                $accReference = Str::random(16);

                // Prepare the request payload
                $payload = [
                    'accountReference'=> $accReference,
                    'accountName'=> $user->username,
                    'currencyCode'=> "NGN",
                    'contractCode'=> $contractCode,
                    'customerEmail'=> $user->email,
                    'customerName'=> $user->username,
                    'getAllAvailableBanks'=> false,
                    'preferredBanks' => ["232"]
                ];

                // Conditionally add 'nin' and 'bvn' if they are not null
                if (!is_null($user->nin)) {
                    $payload['nin'] = $user->nin;
                }

                if (!is_null($user->bvn)) {
                    $payload['bvn'] = $user->bvn;
                }

                $accessToken = MonnifyToken::first();
                $yourAccessToken = $accessToken->access_token;

                // Make a POST request to the Monnify API endpoint
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $yourAccessToken,
                ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                $responseData = $response->json();

                // Check if the response was successful
                if ($responseData['requestSuccessful']) {
                    $responseBody = $responseData['responseBody'];
                    // Extract account information
                    $accounts = $responseBody['accounts'];
                    foreach ($accounts as $account) {
                        $bankName = $account['bankName'];
                        $accountNumber = $account['accountNumber'];
                        $accountName = $account['accountName'];
                        $bankCode = $account['bankCode'];
                        // Create a MonnifyAccount record
                        MonnifyAccounts::create([
                            'user_id' => $user->id,
                            'bank_name' => $bankName,
                            'account_reference' => $accReference,
                            'account_number' => $accountNumber,
                            'account_name' => $accountName,
                            'bank_code' => $bankCode,
                        ]);
                    }
                } else {

                    $contractCode = config('app.monnify_contract_code');

                    $accReference = Str::random(16);

                    // Prepare the request payload
                    $payload = [
                        'accountReference'=> $accReference,
                        'accountName'=> $user->username,
                        'currencyCode'=> "NGN",
                        'contractCode'=> $contractCode,
                        'customerEmail'=> $user->email,
                        'customerName'=> $user->username,
                        'getAllAvailableBanks'=> false,
                        'preferredBanks' => ["232"]
                    ];

                    // Conditionally add 'nin' and 'bvn' if they are not null
                    if (!is_null($user->nin)) {
                        $payload['nin'] = $user->nin;
                    }

                    if (!is_null($user->bvn)) {
                        $payload['bvn'] = $user->bvn;
                    }

                    $accessToken = MonnifyToken::first();
                    $yourAccessToken = $accessToken->access_token;

                    // Make a POST request to the Monnify API endpoint
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $yourAccessToken,
                    ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                    $responseData = $response->json();

                    // Check if the response was successful
                    if ($responseData['requestSuccessful']) {
                        $responseBody = $responseData['responseBody'];
                        // Extract account information
                        $accounts = $responseBody['accounts'];
                        foreach ($accounts as $account) {
                            $bankName = $account['bankName'];
                            $accountNumber = $account['accountNumber'];
                            $accountName = $account['accountName'];
                            $bankCode = $account['bankCode'];
                            // Create a MonnifyAccount record
                            MonnifyAccounts::create([
                                'user_id' => $user->id,
                                'bank_name' => $bankName,
                                'account_reference' => $accReference,
                                'account_number' => $accountNumber,
                                'account_name' => $accountName,
                                'bank_code' => $bankCode,
                            ]);
                        }
                    } else {

                        Log::error('No accounts found in the response body. Response body: ' . json_encode($responseData));
                        // Handle unsuccessful response
                        $responseMessage = $responseData['responseMessage'];
                        $responseCode = $responseData['responseCode'];
                        // Handle the error appropriately
                    }
                }

                $sterling = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '232')
                                            ->first();
            }

            $wema = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '035')
                                            ->first();

            if ($wema === null) {
                $contractCode = config('app.monnify_contract_code');

                $accReference = Str::random(16);

                // Prepare the request payload
                $payload = [
                    'accountReference'=> $accReference,
                    'accountName'=> $user->username,
                    'currencyCode'=> "NGN",
                    'contractCode'=> $contractCode,
                    'customerEmail'=> $user->email,
                    'customerName'=> $user->username,
                    'getAllAvailableBanks'=> false,
                    'preferredBanks' => ["035"]
                ];

                // Conditionally add 'nin' and 'bvn' if they are not null
                if (!is_null($user->nin)) {
                    $payload['nin'] = $user->nin;
                }

                if (!is_null($user->bvn)) {
                    $payload['bvn'] = $user->bvn;
                }

                $accessToken = MonnifyToken::first();
                $yourAccessToken = $accessToken->access_token;

                // Make a POST request to the Monnify API endpoint
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $yourAccessToken,
                ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                $responseData = $response->json();

                // Check if the response was successful
                if ($responseData['requestSuccessful']) {
                    $responseBody = $responseData['responseBody'];
                    // Extract account information
                    $accounts = $responseBody['accounts'];
                    foreach ($accounts as $account) {
                        $bankName = $account['bankName'];
                        $accountNumber = $account['accountNumber'];
                        $accountName = $account['accountName'];
                        $bankCode = $account['bankCode'];
                        // Create a MonnifyAccount record
                        MonnifyAccounts::create([
                            'user_id' => $user->id,
                            'bank_name' => $bankName,
                            'account_reference' => $accReference,
                            'account_number' => $accountNumber,
                            'account_name' => $accountName,
                            'bank_code' => $bankCode,
                        ]);
                    }
                } else {

                    $contractCode = config('app.monnify_contract_code');

                    $accReference = Str::random(16);

                    // Prepare the request payload
                    $payload = [
                        'accountReference'=> $accReference,
                        'accountName'=> $user->username,
                        'currencyCode'=> "NGN",
                        'contractCode'=> $contractCode,
                        'customerEmail'=> $user->email,
                        'customerName'=> $user->username,
                        'getAllAvailableBanks'=> false,
                        'preferredBanks' => ["035"]
                    ];

                    // Conditionally add 'nin' and 'bvn' if they are not null
                    if (!is_null($user->nin)) {
                        $payload['nin'] = $user->nin;
                    }

                    if (!is_null($user->bvn)) {
                        $payload['bvn'] = $user->bvn;
                    }

                    $accessToken = MonnifyToken::first();
                    $yourAccessToken = $accessToken->access_token;

                    // Make a POST request to the Monnify API endpoint
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $yourAccessToken,
                    ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                    $responseData = $response->json();

                    // Check if the response was successful
                    if ($responseData['requestSuccessful']) {
                        $responseBody = $responseData['responseBody'];
                        // Extract account information
                        $accounts = $responseBody['accounts'];
                        foreach ($accounts as $account) {
                            $bankName = $account['bankName'];
                            $accountNumber = $account['accountNumber'];
                            $accountName = $account['accountName'];
                            $bankCode = $account['bankCode'];
                            // Create a MonnifyAccount record
                            MonnifyAccounts::create([
                                'user_id' => $user->id,
                                'bank_name' => $bankName,
                                'account_reference' => $accReference,
                                'account_number' => $accountNumber,
                                'account_name' => $accountName,
                                'bank_code' => $bankCode,
                            ]);
                        }
                    } else {

                        Log::error('No accounts found in the response body. Response body: ' . json_encode($responseData));
                        // Handle unsuccessful response
                        $responseMessage = $responseData['responseMessage'];
                        $responseCode = $responseData['responseCode'];
                        // Handle the error appropriately
                    }
                }

                $wema = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '035')
                                            ->first();
            }

            $moniepoint = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '50515')
                                            ->first();

            if ($moniepoint === null) {
                $contractCode = config('app.monnify_contract_code');

                $accReference = Str::random(16);

                // Prepare the request payload
                $payload = [
                    'accountReference'=> $accReference,
                    'accountName'=> $user->username,
                    'currencyCode'=> "NGN",
                    'contractCode'=> $contractCode,
                    'customerEmail'=> $user->email,
                    'customerName'=> $user->username,
                    'getAllAvailableBanks'=> false,
                    'preferredBanks' => ["50515"]
                ];

                // Conditionally add 'nin' and 'bvn' if they are not null
                if (!is_null($user->nin)) {
                    $payload['nin'] = $user->nin;
                }

                if (!is_null($user->bvn)) {
                    $payload['bvn'] = $user->bvn;
                }

                $accessToken = MonnifyToken::first();
                $yourAccessToken = $accessToken->access_token;

                // Make a POST request to the Monnify API endpoint
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $yourAccessToken,
                ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                $responseData = $response->json();

                // Check if the response was successful
                if ($responseData['requestSuccessful']) {
                    $responseBody = $responseData['responseBody'];
                    // Extract account information
                    $accounts = $responseBody['accounts'];
                    foreach ($accounts as $account) {
                        $bankName = $account['bankName'];
                        $accountNumber = $account['accountNumber'];
                        $accountName = $account['accountName'];
                        $bankCode = $account['bankCode'];
                        // Create a MonnifyAccount record
                        MonnifyAccounts::create([
                            'user_id' => $user->id,
                            'bank_name' => $bankName,
                            'account_reference' => $accReference,
                            'account_number' => $accountNumber,
                            'account_name' => $accountName,
                            'bank_code' => $bankCode,
                        ]);
                    }
                } else {

                    $contractCode = config('app.monnify_contract_code');

                    $accReference = Str::random(16);

                    // Prepare the request payload
                    $payload = [
                        'accountReference'=> $accReference,
                        'accountName'=> $user->username,
                        'currencyCode'=> "NGN",
                        'contractCode'=> $contractCode,
                        'customerEmail'=> $user->email,
                        'customerName'=> $user->username,
                        'getAllAvailableBanks'=> false,
                        'preferredBanks' => ["50515"]
                    ];

                    // Conditionally add 'nin' and 'bvn' if they are not null
                    if (!is_null($user->nin)) {
                        $payload['nin'] = $user->nin;
                    }

                    if (!is_null($user->bvn)) {
                        $payload['bvn'] = $user->bvn;
                    }

                    $accessToken = MonnifyToken::first();
                    $yourAccessToken = $accessToken->access_token;

                    // Make a POST request to the Monnify API endpoint
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $yourAccessToken,
                    ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                    $responseData = $response->json();

                    // Check if the response was successful
                    if ($responseData['requestSuccessful']) {
                        $responseBody = $responseData['responseBody'];
                        // Extract account information
                        $accounts = $responseBody['accounts'];
                        foreach ($accounts as $account) {
                            $bankName = $account['bankName'];
                            $accountNumber = $account['accountNumber'];
                            $accountName = $account['accountName'];
                            $bankCode = $account['bankCode'];
                            // Create a MonnifyAccount record
                            MonnifyAccounts::create([
                                'user_id' => $user->id,
                                'bank_name' => $bankName,
                                'account_reference' => $accReference,
                                'account_number' => $accountNumber,
                                'account_name' => $accountName,
                                'bank_code' => $bankCode,
                            ]);
                        }
                    } else {

                        Log::error('No accounts found in the response body. Response body: ' . json_encode($responseData));
                        // Handle unsuccessful response
                        $responseMessage = $responseData['responseMessage'];
                        $responseCode = $responseData['responseCode'];
                        // Handle the error appropriately
                    }
                }

                $moniepoint = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '50515')
                                            ->first();
            }

        }

        $referral_code = $user->referral_code;

        if($referral_code === null) {

            $man = User::findOrFail($user->id);

            $man->referral_code = $user->username;
            $man->save();

            $referral_code = $man->referral_code;
        }

        $notifications = Transaction::where('user_id', $user->id)
                             ->latest() // Order by creation timestamp in descending order
                             ->take(3)  // Take the first three records
                             ->get();

        $message = Settings::where('id', 2)->first();

        return view('user.dashboard', compact('pageTitle', 'userReferralCount', 'moniepoint', 'wema', 'sterling', 'notifications', 'referral_code', 'settings', 'message', 'popup'));
    }

    public function viewMethods() {

        $pageTitle = "Payment Methods - Moevan";

        return view('user.topup.paystack', compact('pageTitle'));
    }

    public function viewMonnify () {

        $pageTitle = "Fund Wallet - Moevan";

        $user = User::findorFail(Auth::guard('web')->user()->id);

        $moniepoint = null;
        $wema = null;
        $sterling = null;


        if($user->is_kyc_verified === 1) {

            $sterling = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '232')
                                            ->first();

            if ($sterling === null) {
                $contractCode = config('app.monnify_contract_code');

                $accReference = Str::random(16);

                // Prepare the request payload
                $payload = [
                    'accountReference'=> $accReference,
                    'accountName'=> $user->username,
                    'currencyCode'=> "NGN",
                    'contractCode'=> $contractCode,
                    'customerEmail'=> $user->email,
                    'customerName'=> $user->username,
                    'getAllAvailableBanks'=> false,
                    'preferredBanks' => ["232"]
                ];

                // Conditionally add 'nin' and 'bvn' if they are not null
                if (!is_null($user->nin)) {
                    $payload['nin'] = $user->nin;
                }

                if (!is_null($user->bvn)) {
                    $payload['bvn'] = $user->bvn;
                }

                $accessToken = MonnifyToken::first();
                $yourAccessToken = $accessToken->access_token;

                // Make a POST request to the Monnify API endpoint
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $yourAccessToken,
                ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                $responseData = $response->json();

                // Check if the response was successful
                if ($responseData['requestSuccessful']) {
                    $responseBody = $responseData['responseBody'];
                    // Extract account information
                    $accounts = $responseBody['accounts'];
                    foreach ($accounts as $account) {
                        $bankName = $account['bankName'];
                        $accountNumber = $account['accountNumber'];
                        $accountName = $account['accountName'];
                        $bankCode = $account['bankCode'];
                        // Create a MonnifyAccount record
                        MonnifyAccounts::create([
                            'user_id' => $user->id,
                            'bank_name' => $bankName,
                            'account_reference' => $accReference,
                            'account_number' => $accountNumber,
                            'account_name' => $accountName,
                            'bank_code' => $bankCode,
                        ]);
                    }
                } else {

                    $contractCode = config('app.monnify_contract_code');

                    $accReference = Str::random(16);

                    // Prepare the request payload
                    $payload = [
                        'accountReference'=> $accReference,
                        'accountName'=> $user->username,
                        'currencyCode'=> "NGN",
                        'contractCode'=> $contractCode,
                        'customerEmail'=> $user->email,
                        'customerName'=> $user->username,
                        'getAllAvailableBanks'=> false,
                        'preferredBanks' => ["232"]
                    ];

                    // Conditionally add 'nin' and 'bvn' if they are not null
                    if (!is_null($user->nin)) {
                        $payload['nin'] = $user->nin;
                    }

                    if (!is_null($user->bvn)) {
                        $payload['bvn'] = $user->bvn;
                    }

                    $accessToken = MonnifyToken::first();
                    $yourAccessToken = $accessToken->access_token;

                    // Make a POST request to the Monnify API endpoint
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $yourAccessToken,
                    ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                    $responseData = $response->json();

                    // Check if the response was successful
                    if ($responseData['requestSuccessful']) {
                        $responseBody = $responseData['responseBody'];
                        // Extract account information
                        $accounts = $responseBody['accounts'];
                        foreach ($accounts as $account) {
                            $bankName = $account['bankName'];
                            $accountNumber = $account['accountNumber'];
                            $accountName = $account['accountName'];
                            $bankCode = $account['bankCode'];
                            // Create a MonnifyAccount record
                            MonnifyAccounts::create([
                                'user_id' => $user->id,
                                'bank_name' => $bankName,
                                'account_reference' => $accReference,
                                'account_number' => $accountNumber,
                                'account_name' => $accountName,
                                'bank_code' => $bankCode,
                            ]);
                        }
                    } else {

                        Log::error('No accounts found in the response body. Response body: ' . json_encode($responseData));
                        // Handle unsuccessful response
                        $responseMessage = $responseData['responseMessage'];
                        $responseCode = $responseData['responseCode'];
                        // Handle the error appropriately
                    }
                }

                $sterling = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '232')
                                            ->first();
            }

            $wema = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '035')
                                            ->first();

            if ($wema === null) {
                $contractCode = config('app.monnify_contract_code');

                $accReference = Str::random(16);

                // Prepare the request payload
                $payload = [
                    'accountReference'=> $accReference,
                    'accountName'=> $user->username,
                    'currencyCode'=> "NGN",
                    'contractCode'=> $contractCode,
                    'customerEmail'=> $user->email,
                    'customerName'=> $user->username,
                    'getAllAvailableBanks'=> false,
                    'preferredBanks' => ["035"]
                ];

                // Conditionally add 'nin' and 'bvn' if they are not null
                if (!is_null($user->nin)) {
                    $payload['nin'] = $user->nin;
                }

                if (!is_null($user->bvn)) {
                    $payload['bvn'] = $user->bvn;
                }

                $accessToken = MonnifyToken::first();
                $yourAccessToken = $accessToken->access_token;

                // Make a POST request to the Monnify API endpoint
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $yourAccessToken,
                ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                $responseData = $response->json();

                // Check if the response was successful
                if ($responseData['requestSuccessful']) {
                    $responseBody = $responseData['responseBody'];
                    // Extract account information
                    $accounts = $responseBody['accounts'];
                    foreach ($accounts as $account) {
                        $bankName = $account['bankName'];
                        $accountNumber = $account['accountNumber'];
                        $accountName = $account['accountName'];
                        $bankCode = $account['bankCode'];
                        // Create a MonnifyAccount record
                        MonnifyAccounts::create([
                            'user_id' => $user->id,
                            'bank_name' => $bankName,
                            'account_reference' => $accReference,
                            'account_number' => $accountNumber,
                            'account_name' => $accountName,
                            'bank_code' => $bankCode,
                        ]);
                    }
                } else {

                    $contractCode = config('app.monnify_contract_code');

                    $accReference = Str::random(16);

                    // Prepare the request payload
                    $payload = [
                        'accountReference'=> $accReference,
                        'accountName'=> $user->username,
                        'currencyCode'=> "NGN",
                        'contractCode'=> $contractCode,
                        'customerEmail'=> $user->email,
                        'customerName'=> $user->username,
                        'getAllAvailableBanks'=> false,
                        'preferredBanks' => ["035"]
                    ];

                    // Conditionally add 'nin' and 'bvn' if they are not null
                    if (!is_null($user->nin)) {
                        $payload['nin'] = $user->nin;
                    }

                    if (!is_null($user->bvn)) {
                        $payload['bvn'] = $user->bvn;
                    }

                    $accessToken = MonnifyToken::first();
                    $yourAccessToken = $accessToken->access_token;

                    // Make a POST request to the Monnify API endpoint
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $yourAccessToken,
                    ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                    $responseData = $response->json();

                    // Check if the response was successful
                    if ($responseData['requestSuccessful']) {
                        $responseBody = $responseData['responseBody'];
                        // Extract account information
                        $accounts = $responseBody['accounts'];
                        foreach ($accounts as $account) {
                            $bankName = $account['bankName'];
                            $accountNumber = $account['accountNumber'];
                            $accountName = $account['accountName'];
                            $bankCode = $account['bankCode'];
                            // Create a MonnifyAccount record
                            MonnifyAccounts::create([
                                'user_id' => $user->id,
                                'bank_name' => $bankName,
                                'account_reference' => $accReference,
                                'account_number' => $accountNumber,
                                'account_name' => $accountName,
                                'bank_code' => $bankCode,
                            ]);
                        }
                    } else {

                        Log::error('No accounts found in the response body. Response body: ' . json_encode($responseData));
                        // Handle unsuccessful response
                        $responseMessage = $responseData['responseMessage'];
                        $responseCode = $responseData['responseCode'];
                        // Handle the error appropriately
                    }
                }

                $wema = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '035')
                                            ->first();
            }

            $moniepoint = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '50515')
                                            ->first();

            if ($moniepoint === null) {
                $contractCode = config('app.monnify_contract_code');

                $accReference = Str::random(16);

                // Prepare the request payload
                $payload = [
                    'accountReference'=> $accReference,
                    'accountName'=> $user->username,
                    'currencyCode'=> "NGN",
                    'contractCode'=> $contractCode,
                    'customerEmail'=> $user->email,
                    'customerName'=> $user->username,
                    'getAllAvailableBanks'=> false,
                    'preferredBanks' => ["50515"]
                ];

                // Conditionally add 'nin' and 'bvn' if they are not null
                if (!is_null($user->nin)) {
                    $payload['nin'] = $user->nin;
                }

                if (!is_null($user->bvn)) {
                    $payload['bvn'] = $user->bvn;
                }

                $accessToken = MonnifyToken::first();
                $yourAccessToken = $accessToken->access_token;

                // Make a POST request to the Monnify API endpoint
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $yourAccessToken,
                ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                $responseData = $response->json();

                // Check if the response was successful
                if ($responseData['requestSuccessful']) {
                    $responseBody = $responseData['responseBody'];
                    // Extract account information
                    $accounts = $responseBody['accounts'];
                    foreach ($accounts as $account) {
                        $bankName = $account['bankName'];
                        $accountNumber = $account['accountNumber'];
                        $accountName = $account['accountName'];
                        $bankCode = $account['bankCode'];
                        // Create a MonnifyAccount record
                        MonnifyAccounts::create([
                            'user_id' => $user->id,
                            'bank_name' => $bankName,
                            'account_reference' => $accReference,
                            'account_number' => $accountNumber,
                            'account_name' => $accountName,
                            'bank_code' => $bankCode,
                        ]);
                    }
                } else {

                    $contractCode = config('app.monnify_contract_code');

                    $accReference = Str::random(16);

                    // Prepare the request payload
                    $payload = [
                        'accountReference'=> $accReference,
                        'accountName'=> $user->username,
                        'currencyCode'=> "NGN",
                        'contractCode'=> $contractCode,
                        'customerEmail'=> $user->email,
                        'customerName'=> $user->username,
                        'getAllAvailableBanks'=> false,
                        'preferredBanks' => ["50515"]
                    ];

                    // Conditionally add 'nin' and 'bvn' if they are not null
                    if (!is_null($user->nin)) {
                        $payload['nin'] = $user->nin;
                    }

                    if (!is_null($user->bvn)) {
                        $payload['bvn'] = $user->bvn;
                    }

                    $accessToken = MonnifyToken::first();
                    $yourAccessToken = $accessToken->access_token;

                    // Make a POST request to the Monnify API endpoint
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $yourAccessToken,
                    ])->timeout(60)->post('https://api.monnify.com/api/v2/bank-transfer/reserved-accounts', $payload);

                    $responseData = $response->json();

                    // Check if the response was successful
                    if ($responseData['requestSuccessful']) {
                        $responseBody = $responseData['responseBody'];
                        // Extract account information
                        $accounts = $responseBody['accounts'];
                        foreach ($accounts as $account) {
                            $bankName = $account['bankName'];
                            $accountNumber = $account['accountNumber'];
                            $accountName = $account['accountName'];
                            $bankCode = $account['bankCode'];
                            // Create a MonnifyAccount record
                            MonnifyAccounts::create([
                                'user_id' => $user->id,
                                'bank_name' => $bankName,
                                'account_reference' => $accReference,
                                'account_number' => $accountNumber,
                                'account_name' => $accountName,
                                'bank_code' => $bankCode,
                            ]);
                        }
                    } else {

                        Log::error('No accounts found in the response body. Response body: ' . json_encode($responseData));
                        // Handle unsuccessful response
                        $responseMessage = $responseData['responseMessage'];
                        $responseCode = $responseData['responseCode'];
                        // Handle the error appropriately
                    }
                }

                $moniepoint = MonnifyAccounts::where('user_id', $user->id)
                                            ->where('bank_code', '50515')
                                            ->first();
            }

        }

        return view('user.topup.monnify', compact('pageTitle', 'moniepoint', 'wema', 'sterling'));
    }

    public function autoManual() {
        $pageTitle = 'Automated Manual - Moevan';

        return view('user.topup.transfer', compact('pageTitle'));
    }

    public function profile() {
        $pageTitle = "Manage Profile - Moevan";

        return view('user.profile', compact('pageTitle'));
    }

    public function kycVerification() {
        $pageTitle = "KYC Verification - Moevan";

        $id = auth()->guard('web')->user()->id;

        $user = User::findOrFail($id);

        $errors = [];

        if (empty($user->first_name)) {
            $errors[] = 'First name is required.';
        }

        if (empty($user->last_name)) {
            $errors[] = 'Last name is required.';
        }

        if (empty($user->phone)) {
            $errors[] = 'Phone number is required.';
        }

        if (empty($user->dob)) {
            $errors[] = 'Date of birth is required.';
        }

        if (empty($user->address)) {
            $errors[] = 'Address is required.';
        }

        if (!empty($errors)) {

            return redirect()->route('user.profile.show')
                ->with('error', implode(' ', $errors) . ' Please complete your Personal Details before proceeding to KYC.');
        }


        if ($user->is_kyc_verified == '1') {

            return redirect()->route('user.dashboard')->with('success', 'KYC has already been completed successfully');
        }

        return view('user.kyc', compact('pageTitle'));
    }

    public function bonus() {
        $pageTitle = "Withdraw Referral Bonus - Moevan";

        return view('user.referral', compact('pageTitle'));
    }

    public function pricing() {
        $pageTitle = "Pricing Plans - Moevan";

        return view('user.pricing', compact('pageTitle'));
    }

    public function buyData() {
        $pageTitle = "Buy Data - Moevan";

        $networks = Networks::take(4)->get();
        $type = DataType::all();

        return view('user.internet.buy', compact('pageTitle', 'networks', 'type'));
    }
    public function buyAirtime() {
        $pageTitle = "Buy Airtime - Moevan";

        $networks = Networks::all();

        return view('user.voice.airtime', compact('pageTitle', 'networks'));
    }
    public function buyElectric() {
        $pageTitle = "Buy Electric - Moevan";

        return view('user.electric.buy', compact('pageTitle'));
    }
    public function buyCable() {
        $pageTitle = "Buy Cable - Moevan";

        $providers = Providers::all();

        return view('user.cable.buy', compact('pageTitle', 'providers'));
    }
    public function fundPaystack() {
        $pageTitle = "Wallet Top-Up - Moevan";

        return view('user.topup.paystack', compact('pageTitle'));
    }
    public function businessReg() {
        $pageTitle = "Business Registration - Moevan";

        return view('user.business', compact('pageTitle'));
    }
    public function fundMonnify() {
        $pageTitle = "Wallet Top-Up - Moevan";

        $user = User::findorFail(Auth::id());

        $bank = MonnifyAccounts::where('user_id', $user->id)
                                ->orderBy('bank_name')
                                ->get();

        return view('user.topup.monnify', compact('pageTitle', 'bank'));
    }
}

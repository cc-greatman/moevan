<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AgentDataPlanPrice;
use App\Models\DataPlans;
use App\Models\DataControls;
use App\Models\DataTransactions;
use App\Models\DataType;
use App\Models\Networks;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletTransactions;
use App\Rules\ReCaptchaV3;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InternetController extends Controller
{
    public function processDataSub(Request $request)
    {

        // Validate the form data
        $validatedData = $request->validate([
            'phone' => 'required|numeric',
            'plan' => 'required|numeric',
            'network_id' => 'required|numeric',
            'amount' => 'required|numeric|min:50'
        ], [
            'amount.min' => 'The minimum possible amount is ₦50'
        ]);
        $amount =  $validatedData['amount'];

        // Check user's balance
        $user = User::find(auth()->guard('web')->id()); // Assuming the user is logged in

        if(!$user) {
            return redirect()->route('user.trx.error')->with('error', 'Nice one Thief');
        }
        if ($amount > $user->balance) {
            return redirect()->route('user.trx.error')->with('error', 'Insufficient balance.');
        }

        $dataType = $request->input('dataType');

        // Get form data
        $phone = $validatedData['phone'];
        $plan_id = $validatedData['plan'];
        $network_id = $validatedData['network_id'];
        $commission = $amount * 0.01;

        $planDetails = DataPlans::where('data_id', $plan_id)->with('dataType')->first();
        $sub_id      = $planDetails->sub_id;
        $bello_id    = $planDetails->bello_id;
        $networkName = Networks::findOrFail($planDetails->network_id);
        $networkId   = $networkName->networkId;
        $network = $networkName->network;

        $trx = getTrx();

        $reference = $this->generateRequestId();

        $manual = DataControls::where('network', $network_id)->where('data_type', $dataType)->first();
        $main_switch = DataControls::where('network', $network_id)->where('data_type', $dataType)->first();
        $agent = DataControls::where('id', 14)->first();

        if($manual->status == '1') {

            $this->handleManualResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, 'Data purchase transaction initiated!', $phone, $plan_id, 'MOEVAN');

            return redirect()->route('user.trx.success')->with(['response' => 'Your Data Purchase was successful. Enjoy premium services with Moevan!', 'success' => 'Transaction Successful! Data Subscription Successful!']);
        }

        if($main_switch->active == '0') {

            return redirect()->route('user.trx.error')->with('error', 'This Bundle is currently not available at the moment. Use other types of bundles.');
        }

        if($user->account_type == '3' && $planDetails->dataType->id == '6' && $agent->status == '1' ) {

             $this->handleManualResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, 'Data purchase transaction initiated!', $phone, $plan_id, 'MOEVAN');

            return redirect()->route('user.trx.success')->with(['response' => 'Your Data Purchase was successful. Enjoy premium services with Moevan!', 'success' => 'Transaction Successful! Data Subscription Successful!']);
        }

        if($user->account_type == '3' && $planDetails->dataType->id == '6' && $agent->active == '0') {

             return redirect()->route('user.trx.error')->with('error', 'This Bundle is currently not available at the moment. Use other types of bundles.');
        }

        if($user->account_type == '2' && $planDetails->dataType->id == '6' && $planDetails->dataType->status == '1') {

             $this->handleManualResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, 'Data purchase transaction initiated!', $phone, $plan_id, 'MOEVAN');

            return redirect()->route('user.trx.success')->with(['response' => 'Your Data Purchase was successful. Enjoy premium services with Moevan!', 'success' => 'Transaction Successful! Data Subscription Successful!']);
        }

        if($user->account_type == '2' && $planDetails->dataType->id == '6' && $planDetails->dataType->active == '0') {

             return redirect()->route('user.trx.error')->with('error', 'This Bundle is currently not available at the moment. Use other types of bundles.');
        }

        $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, null, $phone, $plan_id, 'INITIAL');

        if($planDetails->dataType->id == '7') {
            try {
                // Your Autopilot API key
                $autopilotApiKey = env('AUTOPILOT_API_KEY');

                // Base URL of the API
                $baseUrl = 'https://autopilotng.com/api/live';

                $body = [
                    'networkId' => $networkId,
                ];

                if($planDetails->data_id != '3004') {
                    $body['dataType'] = 'DATA TRANSFER';
                } else {
                    $body['dataType'] = 'AWOOF GIFTING';
                }

                $body['planId'] = $planDetails->plan_id;
                $body['phone'] = $phone;
                $body['reference'] = $reference;

                $head = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $autopilotApiKey,
                ];

                // Make the HTTP request
                $responses = Http::withHeaders($head)->timeout(38001)->post($baseUrl . '/v1/data', $body);

                $responseData = $responses->json();

                // Check response status and content
                if (isset($responseData['code']) && in_array($responseData['code'], ['200', '201', '500'])) {
                    $apiResponse = $responseData['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $responseData['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            } catch (\Exception $e) {

                $message = $responseData['data']['message'];

                $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                Log::error('Caught exception: ' . get_class($e) . ' - ' . $e->getMessage());
                return redirect()->route('user.trx.error')->with('error', 'Network is currently unavailable.');
            }
        }

        if($network_id == '2' && $planDetails->dataType->id == '5') {

            try {
                // Your Autopilot API key
                $autopilotApiKey = env('AUTOPILOT_API_KEY');

                // Base URL of the API
                $baseUrl = 'https://autopilotng.com/api/live';

                $body = [
                    'networkId' => $networkId,
                ];

                $body['dataType'] = 'CORPORATE GIFTING';

                $body['planId'] = $planDetails->plan_id;
                $body['phone'] = $phone;
                $body['reference'] = $reference;

                $head = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $autopilotApiKey,
                ];

                // Make the HTTP request
                $responses = Http::withHeaders($head)->timeout(38001)->post($baseUrl . '/v1/data', $body);

                $responseData = $responses->json();

                // Check response status and content
                if (isset($responseData['code']) && in_array($responseData['code'], ['200', '201', '500'])) {
                    $apiResponse = $responseData['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $responseData['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            } catch (\Exception $e) {

                $message = $responseData['data']['message'];

                $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                Log::error('Caught exception: ' . get_class($e) . ' - ' . $e->getMessage());
                return redirect()->route('user.trx.error')->with('error', 'Network is currently unavailable.');
            }
        }

        if($network_id == '1' || $network_id == '2' || $network_id == '4' && $planDetails->dataType->id == '6' || $planDetails->dataType->id == '5') {
            try {
                // Your Autopilot API key
                $autopilotApiKey = env('AUTOPILOT_API_KEY');

                // Base URL of the API
                $baseUrl = 'https://autopilotng.com/api/live';

                $body = [
                    'networkId' => $networkId,
                ];

                if($planDetails->dataType->id == '6') {
                    $body['dataType'] = 'DATA TRANSFER';
                } else {
                    $body['dataType'] = 'CORPORATE GIFTING';
                }

                $body['planId'] = $planDetails->plan_id;
                $body['phone'] = $phone;
                $body['reference'] = $reference;

                $head = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $autopilotApiKey,
                ];

                // Make the HTTP request
                $responses = Http::withHeaders($head)->timeout(38001)->post($baseUrl . '/v1/data', $body);

                $responseData = $responses->json();

                // Check response status and content
                if (isset($responseData['code']) && in_array($responseData['code'], ['200', '201', '500'])) {
                    $apiResponse = $responseData['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {

                    try {
                        $header = [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer '.env('AREWA_API_KEY'),
                        ];

                        $payload = [
                            'service' => 'buy-data',
                            'network' => $networkName->name,
                            'mobile' => $phone,
                            'bundle_id' => $planDetails->are_id,
                        ];

                        $response = Http::withHeaders($header)
                                            ->timeout(38001)
                                            ->post('https://arewabundleapi.com/api/v2/buyservice', $payload);

                        $responseDa = $response->json();

                        if(isset($responseDa['status']) && $responseDa['status'] === 'success') {

                            $apiResponse = $responseDa['content'];

                            // Handle other cases
                            $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AREWA');

                            return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);

                        } elseif (isset($responseDa['status']) && ($responseDa['status'] === "failed" || $responseDa['status'] === "fail") || (isset($responseDa['message']) && $responseDa['message'] === "Transaction failed")) {

                            if ($network_id == '1' && $planDetails->dataType->id == '6') {
                                try {
                                    // Make API request
                                    $header = [
                                        'Authorization' => 'Token '.env('BELLO_API_KEY'),
                                        'Content-Type' => 'application/json',
                                    ];

                                    $data = [
                                        'network' => $network,
                                        'mobile_number' => $phone,
                                        'plan' => $bello_id,
                                        'Ported_number' => true,
                                    ];

                                    $response = Http::withHeaders($header)
                                            ->timeout(38001)
                                            ->post('https://bello-ventures.com/api/data/', $data);

                                    $responseDat = $response->json();

                                    if (isset($responseDat['Status']) && strtoupper($responseDat['Status']) === 'FAILED' || empty($responseDat) || isset($responseDat['plan']['0'])) {
                                        // Handle failed response
                                        $msg = $responseDat['api_response']??$responseDat['error']??null;

                                        $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                                        return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');

                                    } elseif (isset($responseDat['error']) || empty($responseDat)) {
                                        // Handle failed response
                                        $msg = $responseDat['api_response']??$responseDat['error']??null;

                                        $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                                        return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');

                                    } else {
                                        $apiResponse = $responseDat['api_response'];
                                        // Handle other cases
                                        $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'BELLO-VENTURES');

                                        return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                                    }
                                } catch (\Exception $e) {

                                    $msg = $responseDat['api_response']??$responseDat['error']??null;

                                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                                    Log::error('An error occurred while processing the successful response: ' . $e->getMessage() .$msg);

                                    // Ensure $msg is a string
                                    if (is_array($msg)) {
                                        $msg = json_encode($msg);
                                    }
                                    return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');
                                }
                            } else {
                                $responseDa['status'] = 'failed';
                            }

                        } elseif (isset($responseDa['status']) && $responseDa['status'] === 'pending') {

                            $apiResponse = $responseDa['content'];

                            // Handle other cases
                            $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AREWA');

                            return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                        }
                    } catch (\Exception $e) {

                        $msg = $responseDa['content']??$responseDa['message']??null;

                        $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                        Log::error('An error occurred while processing the successful response: ' . $e->getMessage() .$msg);

                        // Ensure $msg is a string
                        if (is_array($msg)) {
                            $msg = json_encode($msg);
                        }

                        return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');

                    }
                }
            } catch (\Exception $e) {

                $message = $responseData['data']['message'];

                $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                Log::error('Caught exception: ' . get_class($e) . ' - ' . $e->getMessage());
                return redirect()->route('user.trx.error')->with('error', 'Network is currently unavailable.');
            }
        }

        if (in_array(optional($planDetails->dataType)->id, ['4', '8', '9', '10'])) {
            try {
                // Autopilot API key
                $autopilotApiKey = env('AUTOPILOT_API_KEY');
                $baseUrl = 'https://autopilotng.com/api/live';

                // Determine dataType
                 if (optional($planDetails->dataType)->id == '8' || optional($planDetails->dataType)->id == '9') {
                        $dataType = 'BROADBAND GIFTING';
                    } else {
                        $dataType = 'DIRECT GIFTING';
                    }

                // Prepare request body
                $body = [
                    'networkId' => $networkId,
                    'dataType' => $dataType,
                    'planId' => $planDetails->plan_id,
                    'phone' => $phone,
                    'reference' => $reference
                ];

                // Headers
                $head = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $autopilotApiKey,
                ];

                // Make the HTTP request
                $responses = Http::withHeaders($head)->timeout(30)->post("$baseUrl/v1/data", $body);

                $responseData = $responses->json();

                // Check response status and content
                if (isset($responseData['code']) && in_array($responseData['code'], ['200', '201', '500'])) {
                    $apiResponse = $responseData['data']['message'] ?? 'Transaction Successful';

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with([
                        'response' => $apiResponse,
                        'success' => 'Transaction Successful! Data Subscription Successful!'
                    ]);
                } else {
                    $message = $responseData['data']['message'] ?? 'Unknown error occurred.';

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            } catch (\Exception $e) {
                Log::error('Caught exception: ' . get_class($e) . ' - ' . $e->getMessage());

                $message = 'Network is currently unavailable.';
                $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                return redirect()->route('user.trx.error')->with('error', $message);
            }
        }

        if ($network_id == '1' && $planDetails->dataType->id == '7' ||$planDetails->dataType->id == '2' && in_array(optional($plan_id), ['111111', '226', '1111', '1222', '1223', '1224', '1225', '1226', '1227', '80010'])) {
            try {
                // Autopilot API key
                $autopilotApiKey = env('AUTOPILOT_API_KEY');
                $baseUrl = 'https://autopilotng.com/api/live';

                // Determine dataType
                $dataType = 'DIRECT GIFTING';

                // Prepare request body
                $body = [
                    'networkId' => $networkId,
                    'dataType' => $dataType,
                    'planId' => $planDetails->plan_id,
                    'phone' => $phone,
                    'reference' => $reference
                ];

                // Headers
                $head = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $autopilotApiKey,
                ];

                // Make the HTTP request
                $responses = Http::withHeaders($head)->timeout(30)->post("$baseUrl/v1/data", $body);
                $responseData = $responses->json();

                // Check response status and content
                if (isset($responseData['code']) && in_array($responseData['code'], ['200', '201', '500'])) {
                    $apiResponse = $responseData['data']['message'] ?? 'Transaction Successful';

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with([
                        'response' => $apiResponse,
                        'success' => 'Transaction Successful! Data Subscription Successful!'
                    ]);
                } else {
                    $message = $responseData['data']['message'] ?? 'Unknown error occurred.';

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            } catch (\Exception $e) {
                Log::error('Caught exception: ' . get_class($e) . ' - ' . $e->getMessage());

                $message = 'Network is currently unavailable.';
                $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                return redirect()->route('user.trx.error')->with('error', $message);
            }
        }

                try {
                    $header = [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer '.env('AREWA_API_KEY'),
                    ];

                    $payload = [
                        'service' => 'buy-data',
                        'network' => $networkName->name,
                        'mobile' => $phone,
                        'bundle_id' => $planDetails->are_id,
                    ];

                    if($planDetails->dataType->id == '5' && $network_id == '4') {
                        $responseDa = [
                            'status' => 'failed',
                        ];
                    } else {

                        $response = Http::withHeaders($header)
                                        ->timeout(38001)
                                        ->post('https://arewabundleapi.com/api/v2/buyservice', $payload);

                        $responseDa = $response->json();
                    }

                    if(isset($responseDa['status']) && $responseDa['status'] === 'success') {

                        $apiResponse = $responseDa['content'];

                        // Handle other cases
                        $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AREWA');

                        return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);

                    } elseif (isset($responseDa['status']) && ($responseDa['status'] === "failed" || $responseDa['status'] === "fail") || (isset($responseDa['message']) && $responseDa['message'] === "Transaction failed")) {

                        try{
                            $apiKey = env('JUSTPAY_API_KEY');

                            $callbackUrl = "https://moevan.com";

                            // Prepare the request payload
                            $payload = [
                                'api_key' => $apiKey,
                                'product_code' => $planDetails->product_code,
                                'phone' => $phone,
                                'callback' => $callbackUrl,
                            ];

                            // Define basic headers
                            $headers = [
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                            ];

                            // Make the API request with headers
                            $response = Http::withHeaders($headers)->timeout(38001)
                                            ->post('https://justpayng.com/api/v2/datashare/index.php', $payload);

                            $responseData = $response->json();

                            // Check if the transaction was successful
                            if ($responseData['error_code'] == '1986' && $responseData['status'] === true) {

                                $apiResponse = $responseData['data']['true_response'];
                                // Handle other cases
                                $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'JUSTPAY');

                                return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                                } else {

                                    try {
                                        // Make API request
                                        $header = [
                                            'Authorization' => 'Token '.env('BELLO_API_KEY'),
                                            'Content-Type' => 'application/json',
                                        ];

                                        $data = [
                                            'network' => $network,
                                            'mobile_number' => $phone,
                                            'plan' => $bello_id,
                                            'Ported_number' => true,
                                        ];

                                        $response = Http::withHeaders($header)
                                                ->timeout(38001)
                                                ->post('https://bello-ventures.com/api/data/', $data);

                                        $responseDat = $response->json();

                                        if (isset($responseDat['Status']) && strtoupper($responseDat['Status']) === 'FAILED' || empty($responseDat) || isset($responseDat['plan']['0'])) {
                                            // Handle failed response
                                            $msg = $responseDat['api_response']??$responseDat['error']??null;

                                            $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                                            return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');

                                        } elseif (isset($responseDat['error']) || empty($responseDat)) {
                                            // Handle failed response
                                            $msg = $responseDat['api_response']??$responseDat['error']??null;

                                            $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                                            return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');

                                        } else {
                                            $apiResponse = $responseDat['api_response'];
                                            // Handle other cases
                                            $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'BELLO-VENTURES');

                                            return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                                        }
                                    } catch (\Exception $e) {

                                        $msg = $responseDat['api_response']??$responseDat['error']??null;

                                        $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                                        Log::error('An error occurred while processing the successful response: ' . $e->getMessage() .$msg);

                                        // Ensure $msg is a string
                                        if (is_array($msg)) {
                                            $msg = json_encode($msg);
                                        }
                                        return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');
                                    }
                                }
                        } catch (\Exception $e) {

                            $msg = $responseData['data']['true_response']??$responseData['server_message']??$responseData['data']['server_message']??$responseData['error']??"Transaction Declined by Provider JPNG. Refer to them";

                            $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'JUSTPAY');

                            Log::error('An error occurred while processing the successful response: ' . $e->getMessage() .$msg);

                            // Ensure $msg is a string
                            if (is_array($msg)) {
                                $msg = json_encode($msg);
                            }
                            return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');
                        }

                    } elseif (isset($responseDa['status']) && $responseDa['status'] === 'pending') {

                        $apiResponse = $responseDa['content'];

                        // Handle other cases
                        $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AREWA');

                        return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                    }
                } catch (\Exception $e) {

                    $msg = $responseDa['content']??$responseDa['message']??null;

                    Log::error('An error occurred while processing the successful response: ' . $e->getMessage().$msg);

                    try{

                        $apiKey = env('JUSTPAY_API_KEY');

                        $callbackUrl = "https://moevan.com";

                        // Prepare the request payload
                        $payload = [
                            'api_key' => $apiKey,
                            'product_code' => $planDetails->product_code,
                            'phone' => $phone,
                            'callback' => $callbackUrl,
                        ];

                        // Define basic headers
                        $headers = [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ];

                        // Make the API request with headers
                        $response = Http::withHeaders($headers)
                                        ->timeout(38001)
                                        ->post('https://justpayng.com/api/v2/datashare/index.php', $payload);

                        $responseData = $response->json();

                        // Check if the transaction was successful
                        if ($responseData['error_code'] == '1986' && $responseData['status'] === true) {

                            $apiResponse = $responseData['data']['true_response'];
                            // Handle other cases
                            $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'JUSTPAY');

                            return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                        } else {

                            try {
                                // Make API request
                                $header = [
                                    'Authorization' => 'Token '.env('BELLO_API_KEY'),
                                    'Content-Type' => 'application/json',
                                ];

                                $data = [
                                    'network' => $network,
                                    'mobile_number' => $phone,
                                    'plan' => $bello_id,
                                    'Ported_number' => true,
                                ];

                                $response = Http::withHeaders($header)
                                        ->timeout(38001)
                                        ->post('https://bello-ventures.com/api/data/', $data);

                                $responseDat = $response->json();

                                if (isset($responseDat['Status']) && strtoupper($responseDat['Status']) === 'FAILED' || empty($responseDat) || isset($responseDat['plan']['0'])) {
                                    // Handle failed response
                                    $msg = $responseDat['api_response']??$responseDat['error']??null;

                                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                                    Log::error('An error occurred while processing the successful response: ' . $e->getMessage() .$msg);

                                    // Ensure $msg is a string
                                    if (is_array($msg)) {
                                        $msg = json_encode($msg);
                                    }

                                    return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');
                                } elseif (isset($responseDat['error']) || empty($responseDat)) {
                                    // Handle failed response
                                    $msg = $responseDat['api_response']??$responseDat['error']??null;

                                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                                    // Ensure $msg is a string
                                    if (is_array($msg)) {
                                        $msg = json_encode($msg);
                                    }

                                    return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');
                                } else {
                                    $apiResponse = $responseDat['api_response'];
                                    // Handle other cases
                                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'BELLO-VENTURES');

                                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                                }
                            } catch (\Exception $e) {

                                $msg = $responseDat['api_response']??$responseDat['error']??null;

                                Log::error('An error occurred while processing the successful response: ' . $e->getMessage().$msg);

                                $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'BELLO-VENTURES');

                                // Ensure $msg is a string
                                if (is_array($msg)) {
                                    $msg = json_encode($msg);
                                }

                                return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');

                            }
                        }
                    } catch (\Exception $e) {

                        $msg = $responseData['data']['true_response']??$responseData['server_message']??$responseData['data']['server_message']??$responseData['error']??"Transaction Declined by Provider JPNG. Refer to them";

                        $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $msg, $phone, $plan_id, 'JUSTPAY');

                        // Log the error or handle it as needed
                        Log::error('An error occurred while processing the successful response: ' . $e->getMessage(). $msg);

                        // Ensure $msg is a string
                        if (is_array($msg)) {
                            $msg = json_encode($msg);
                        }

                        return redirect()->route('user.trx.error')->with('error', $msg??'Network is currently Unavailable');
                    }
                }
    }

    public function getDataTypes() {
    try {
        // Retrieve the 'network_id' from the query parameters
        $networkId = request()->query('network_id');
        if (is_null($networkId)) {
            return response()->json(['error' => 'Network ID is required'], 400);
        }

        $user = Auth::user();
        if (is_null($user)) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Initialize the query builder for DataType
        $query = DataType::with('dataPlans')
            ->whereHas('dataPlans', function ($query) use ($networkId) {
                $query->where('network_id', $networkId);
            });

        // Define the custom order arrays
        $customOrder = [
            'SPECIAL DATA' => 1,
            'SME' => 2,
            'SME2' => 3,
            'CORPORATE GIFTING' => 4,
            'GIFTING' => 5,
        ];

        $mtnOrder = [
            'XTRASPECIAL' => 1,
            'SPECIAL DATA' => 2,
            'SPECIAL OFFER' => 3,
            'GIFTING' => 4,
        ];

        $rearrangedOrder = [
            'SPECIAL DATA' => 1,
            'CORPORATE GIFTING' => 2,
            'GIFTING' => 3,
            'SME' => 4,
            'SME2' => 5,
        ];

        // Adjust the query based on account_type and network_id
        if ($networkId == '1') {
            $query->whereIn('id', [4, 6, 7, 10]);
        } elseif ($user->account_type == '1' && $networkId == '4') {
            $query->whereIn('id', [4, 5, 7]);
        } elseif (in_array($user->account_type, ['2', '3']) && $networkId == '4') {
            $query->whereIn('id', [4, 5,7]);
        } elseif ($networkId == '2') {
            $query->whereIn('id', [4, 5, 7]);
        } elseif ($networkId == '3') {
            $query->whereIn('id', [4, 5]);
        }

        // Determine the order to use based on network_id
        $orderToUse = match ($networkId) {
            '2', '4' => $rearrangedOrder,
            '1' => $mtnOrder,
            default => $customOrder,
        };

        // Get the data types with custom ordering
        $dataTypes = $query->get()->sortBy(fn($dataType) => $orderToUse[$dataType->name] ?? PHP_INT_MAX)->values();

        // Transform the data type names if conditions are met
        $dataTypes->transform(function ($dataType) use ($user, $networkId) {
            if ($user->account_type == '1') {
                if ($networkId == '1') {
                    $dataType->name = match ($dataType->name) {
                        'SPECIAL OFFER' => 'SMALL BUNDLES',
                        'GIFTING' => 'BIG BUNDLES',
                        'SME2' => 'SMALL BUNDLES 2',
                        default => $dataType->name,
                    };
                } elseif ($networkId == '2' || $networkId == '3') {
                    $dataType->name = match ($dataType->name) {
                        'CORPORATE GIFTING' => 'SMALL BUNDLES',
                        'GIFTING' => 'BIG BUNDLES',
                        default => $dataType->name,
                    };
                } elseif ($networkId == '4') {
                    $dataType->name = match ($dataType->name) {
                        'SPECIAL DATA' => 'SMALL BUNDLES',
                        'CORPORATE GIFTING' => 'SMALL BUNDLES 2',
                        'GIFTING' => 'BIG BUNDLES',
                        default => $dataType->name,
                    };
                }
            } elseif (in_array($user->account_type, ['2', '3']) && $networkId == '1') {
                $dataType->name = match ($dataType->name) {
                    'SPECIAL OFFER' => 'SME',
                    'SME2' => 'COPORATE GIFTING',
                    default => $dataType->name,
                };
            } elseif ($networkId === "4" && $dataType->name === "BROADBAND BUNDLES") {
                $dataType->name = 'ROUTER BUNDLES';
            }

            return $dataType;
        });
        return response()->json($dataTypes);
    } catch (\Exception $e) {
        Log::error('Error in getDataTypes: ' . $e->getMessage());
        return response()->json(['error' => 'An unexpected error occurred'], 500);

    }
}

    public function getDataPlans(Request $request) {
        $network = $request->input('network_id');
        $dataType = $request->input('dataType');
        $user = Auth::user();

        // Initialize the query builder
        $query = DataPlans::query()->with(['network', 'dataType']);

        // Check if both network_id and dataType are provided
        if ($network && $dataType) {
            // Add conditions to filter by network_id and dataType
            $query->where('network_id', $network)->where('type', $dataType);

            // Limit results based on network_id and dataType
            if ($network == 1 && $dataType == 5) {
                $dataPlans = $query->orderBy('price', 'asc')->take(6)->get();
            } elseif ($network == 2 && $dataType == 5) {
                $dataPlans = $query->orderBy('price', 'asc')->take(7)->get();
            } elseif ($network == 3 && $dataType == 5) {
                $dataPlans = $query->orderBy('price', 'asc')->take(10)->get();
            } elseif ($network == 4 && $dataType == 5) {
                $dataPlans = $query->orderBy('price', 'asc')->take(7)->get();
            } else {
                $dataPlans = $query->orderBy('price', 'asc')->get();
            }

            // Retrieve specific prices for the agent if the user is an agent
            if ($user->account_type == 3) {
                foreach ($dataPlans as $dataPlan) {
                    $agentPrice = AgentDataPlanPrice::where('agent_id', $user->id)
                        ->where('data_id', $dataPlan->data_id)
                        ->first();

                    $dataPlan->a_price = $agentPrice ? $agentPrice->price : $dataPlan->a_price;
                }
            }
        } else {
            // If either network_id or dataType is not provided, return an empty result
            $dataPlans = [];
        }

        return response()->json($dataPlans);

    }

    public function generateRequestId() {
        // Set the timezone to GMT+1
        $timezone = new DateTimeZone('Europe/Berlin'); // GMT+1

        // Get the current date and time
        $now = new DateTime('now', $timezone);

        // Format the date and time as YYYYMMDDHHII
        $timestamp = $now->format('YmdHi');

        // Generate a more unique ID using uniqid() with entropy
        $uniquePart = substr(md5(uniqid(mt_rand(), true)), 0, 17); // 17 characters

        // Final request ID (25 characters)
        $requestId = $timestamp . $uniquePart; // 8 + 17 = 25

        return $requestId;
    }

    public function getAdminDataTypes() {

        // Retrieve the 'network_id' from the query parameters
        $networkId = request()->query('network_id');

        // Initialize the query builder for DataType
        $query = DataType::with('dataPlans')
            ->whereHas('dataPlans', function ($query) use ($networkId) {
                $query->where('network_id', $networkId);
            });

        // Define the custom order for data types
        $customOrder = [
            'SPECIAL DATA' => 1,
            'SPECIAL OFFER' => 2,
            'SME' => 3,
            'SME2' => 4,
            'CORPORATE GIFTING' => 5,
            'GIFTING' => 6,
            'XTRASPECIAL' => 7,
            // Add other data type names and their corresponding order numbers as needed
        ];

        // Check if the selected network is 1
        if ($networkId == 1) {
            // If the selected network is 1, modify the query to include data types 2, 3, 4, and 5
            $query->whereIn('id', [2, 4, 5, 6, 7, 8, 9, 10]);
        } else {
            // If the selected network is not 1, modify the query to include data types 4 and 5
            $query->whereIn('id', [4, 5, 6, 7, 8, 9, 10]);
        }

        // Get the data types with custom ordering
        $dataTypes = $query->get()->sortBy(function ($dataType) use ($customOrder) {
            // Get the order number from the custom order array, default to a high value if not found
            return $customOrder[$dataType->name] ?? PHP_INT_MAX;
        })->values(); // Re-index the array after sorting

        if ($networkId == '4') {
            $dataTypes->transform(function ($dataType) {
                if ($dataType->name == 'BROADBAND BUNDLES') {
                    $dataType->name = 'ROUTER BUNDLES';
                }
                return $dataType;
            });
        }

        return response()->json($dataTypes);
    }

    public function getAdminDataTypesR() {

        // Retrieve the 'network_id' from the query parameters
        $networkId = request()->query('network_id');

        // Initialize the query builder for DataType
        $query = DataType::with('dataPlans')
            ->whereHas('dataPlans', function ($query) use ($networkId) {
                $query->where('network_id', $networkId);
            });

        // Define the custom order for data types
        $customOrder = [
            'SPECIAL DATA' => 1,
            'SPECIAL OFFER' => 2,
            'SME' => 3,
            'SME2' => 4,
            'CORPORATE GIFTING' => 5,
            'GIFTING' => 6,
            'XTRASPECIAL' => 7,
            // Add other data type names and their corresponding order numbers as needed
        ];

        // Check if the selected network is 1
        if ($networkId == 1) {
            // If the selected network is 1, modify the query to include data types 2, 3, 4, and 5
            $query->whereIn('id', [2, 3, 4, 5, 6, 7, 8, 9, 10]);
        } else {
            // If the selected network is not 1, modify the query to include data types 4 and 5
            $query->whereIn('id', [4, 5, 6, 7, 8, 9, 10]);
        }

        // Get the data types with custom ordering
        $dataTypes = $query->get()->sortBy(function ($dataType) use ($customOrder) {
            // Get the order number from the custom order array, default to a high value if not found
            return $customOrder[$dataType->name] ?? PHP_INT_MAX;
        })->values(); // Re-index the array after sorting

        if ($networkId == '4') {
            $dataTypes->transform(function ($dataType) {
                if ($dataType->name == 'BROADBAND BUNDLES') {
                    $dataType->name = 'ROUTER BUNDLES';
                }
                return $dataType;
            });
        }

        return response()->json($dataTypes);
    }

    public function getAdminDataTypesA() {

        // Retrieve the 'network_id' from the query parameters
        $networkId = request()->query('network_id');

        // Initialize the query builder for DataType
        $query = DataType::with('dataPlans')
            ->whereHas('dataPlans', function ($query) use ($networkId) {
                $query->where('network_id', $networkId);
            });

        // Define the custom order for data types
        $customOrder = [
            'SPECIAL DATA' => 1,
            'SPECIAL OFFER' => 2,
            'SME' => 3,
            'SME2' => 4,
            'CORPORATE GIFTING' => 5,
            'GIFTING' => 6,
            'XTRASPECIAL' => 7,
            // Add other data type names and their corresponding order numbers as needed
        ];

        // Check if the selected network is 1
        if ($networkId == 1) {
            // If the selected network is 1, modify the query to include data types 2, 3, 4, and 5
            $query->whereIn('id', [2, 3, 4, 5, 6, 7, 8, 9, 10]);
        } else {
            // If the selected network is not 1, modify the query to include data types 4 and 5
            $query->whereIn('id', [4, 5, 6, 7, 8, 9, 10]);
        }

        // Get the data types with custom ordering
        $dataTypes = $query->get()->sortBy(function ($dataType) use ($customOrder) {
            // Get the order number from the custom order array, default to a high value if not found
            return $customOrder[$dataType->name] ?? PHP_INT_MAX;
        })->values(); // Re-index the array after sorting

        if ($networkId == '4') {
            $dataTypes->transform(function ($dataType) {
                if ($dataType->name == 'BROADBAND BUNDLES') {
                    $dataType->name = 'ROUTER BUNDLES';
                }
                return $dataType;
            });
        }

        return response()->json($dataTypes);
    }

    public function getAdminDataPlans(Request $request) {

        $network = $request->input('network_id');
        $dataType = $request->input('dataType');

        // Initialize the query builder
        $query = DataPlans::query()->with(['network', 'dataType']);

        // Check if both network_id and dataType are provided
        if ($network && $dataType) {
            // Add conditions to filter by network_id and dataType
            $query->where('network_id', $network)->where('type', $dataType);

            if ($network == 1 && $dataType == 5) {
                    $dataPlans = $query->orderBy('price', 'asc')->take(6)->get();
                } elseif ($network == 2 && $dataType == 5) {
                    $dataPlans = $query->orderBy('price', 'asc')->take(7)->get();
                } elseif ($network == 3 && $dataType == 5) {
                    $dataPlans = $query->orderBy('price', 'asc')->take(10)->get();
                } elseif ($network == 4 && $dataType == 5) {
                    $dataPlans = $query->orderBy('price', 'asc')->take(7)->get();
                } else {
                    $dataPlans = $query->orderBy('price', 'asc')->get();
                }

        } else {
            // If either network_id or dataType is not provided, return an empty result
            $dataPlans = [];
        }

    return response()->json($dataPlans);

    }

    function handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, $provider) {

        // Check if a transaction with the same reference or trx already exists
        $existingTransaction = DataTransactions::where('reference', $reference)->orWhere('trx', $trx)->first();

        if ($existingTransaction) {
            // Update existing transaction record
            $existingTransaction->provider = $provider;
            $existingTransaction->post_bal = $user->balance;
            $existingTransaction->response = $apiResponse;
            $existingTransaction->status = '1';
            $existingTransaction->save();

            // Commit the transaction
           DB::commit();

           // Save processed notification to the database
           DB::table('processed_notifications')->insert([
               'reference_number' => $reference,
               'created_at' => now(),
               'updated_at' => now(),
           ]);
        } else {
            // Create new transaction record
            $update = new DataTransactions;
            $preBal = $user->balance;

            $update->pre_bal = $preBal;
            $user->balance -= $amount;
            $user->save();

            $update->amount = $amount;
            $update->post_bal = $user->balance;
            $update->user_id = $user->id;
            $update->trx = $trx;
            $update->reference = $reference;
            $update->data_trx = 'Bought ' . $networkName->name . ' ' . $planDetails->size . ' ' . $planDetails->dataType->name . ' for ' . $phone;
            $update->response = $apiResponse;
            $update->provider = $provider;
            $update->plan_id = $plan_id;
            $update->phone = $phone;
            $update->status = '0';
            $update->save();
        }

        // Update Transaction Table
        $transaction = Transaction::where('trx', $trx)->first();
        if ($transaction) {
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->details = 'Bought ' . $networkName->name . ' ' . $planDetails->size . ' ' . $planDetails->dataType->name . ' for ' . $phone;
            $transaction->status = '1';
            $transaction->save();
        } else {
            // Create new transaction record if not found (this assumes 'Transaction' model exists)
            $transaction = new Transaction;
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type = '-';
            $transaction->details = 'Bought ' . $networkName->name . ' ' . $planDetails->size . ' ' . $planDetails->dataType->name . ' for ' . $phone;
            $transaction->status = '0';
            $transaction->trx = $trx;
            $transaction->save();
        }

    }

    function handleManualResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, $provider) {

        // Check if a transaction with the same reference or trx already exists
        $existingTransaction = DataTransactions::where('reference', $reference)->orWhere('trx', $trx)->first();

        if ($existingTransaction) {
            // Update existing transaction record
            $existingTransaction->provider = $provider;
            $existingTransaction->post_bal = $user->balance;
            $existingTransaction->response = $apiResponse;
            $existingTransaction->status = '1';
            $existingTransaction->save();

            // Commit the transaction
           DB::commit();

           // Save processed notification to the database
           DB::table('processed_notifications')->insert([
               'reference_number' => $reference,
               'created_at' => now(),
               'updated_at' => now(),
           ]);
        } else {
            // Create new transaction record
            $update = new DataTransactions;
            $preBal = $user->balance;

            $update->pre_bal = $preBal;
            $user->balance -= $amount;
            $user->save();

            $update->amount = $amount;
            $update->post_bal = $user->balance;
            $update->user_id = $user->id;
            $update->trx = $trx;
            $update->reference = $reference;
            $update->data_trx = 'Bought ' . $networkName->name . ' ' . $planDetails->size . ' ' . $planDetails->dataType->name . ' for ' . $phone;
            $update->response = $apiResponse;
            $update->provider = $provider;
            $update->plan_id = $plan_id;
            $update->phone = $phone;
            $update->status = '1';
            $update->save();
        }

        // Update Transaction Table
        $transaction = Transaction::where('trx', $trx)->first();
        if ($transaction) {
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->details = 'Bought ' . $networkName->name . ' ' . $planDetails->size . ' ' . $planDetails->dataType->name . ' for ' . $phone;
            $transaction->status = '1';
            $transaction->save();
        } else {
            // Create new transaction record if not found (this assumes 'Transaction' model exists)
            $transaction = new Transaction;
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type = '-';
            $transaction->details = 'Bought ' . $networkName->name . ' ' . $planDetails->size . ' ' . $planDetails->dataType->name . ' for ' . $phone;
            $transaction->status = '1';
            $transaction->trx = $trx;
            $transaction->save();
        }

    }

    function handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, $provider) {

        // Check if a transaction with the same reference or trx already exists
        $existingTransaction = DataTransactions::where('reference', $reference)->orWhere('trx', $trx)->first();

        if ($existingTransaction) {
            $user->balance += $amount;
            $user->save();

            // Update existing transaction record
            $existingTransaction->provider = $provider;
            $existingTransaction->post_bal = $user->balance;
            $existingTransaction->response = $apiResponse ?? "Failed Transaction";
            $existingTransaction->status = '0';
            $existingTransaction->save();

        }

        // Update Transaction Table
        $transaction = Transaction::where('trx', $trx)->first();
        if ($transaction) {
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->details = 'Bought ' . $networkName->name . ' ' . $planDetails->size . ' ' . $planDetails->dataType->name . ' for ' . $phone;
            $transaction->status = '0';
            $transaction->save();
        }

    }

}

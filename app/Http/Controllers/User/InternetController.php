<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AgentDataPlanPrice;
use App\Models\DataPlans;
use App\Models\DataControls;
use App\Models\DataTransactions;
use App\Models\DataType;
use App\Models\Networks;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Console\View\Components\Info;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InternetController extends Controller
{
    public function processDataSub(Request $request)
    {
        $token = $request->input('transaction_token');
        $sessionToken = session('transaction_token');

        // Validate the transaction token
        if ($token === null || $sessionToken === null || $token !== $sessionToken) {
            Log::warning('Invalid transaction token', [
                'expected' => session('transaction_token'),
                'received' => $token,
            ]);

            return redirect()->route('user.transactions.data.show');
        }


        // Invalidate the token after use
        session()->forget('transaction_token');

        // Validate the form data
        $validatedData = $request->validate([
            'phone' => 'required|numeric',
            'plan' => 'required|numeric',
            'network_id' => 'required|numeric',
            'dataType' => 'required|numeric'
        ]);
        // Check user's balance
        $user = User::findOrFail(auth()->guard('web')->id()); // Assuming the user is logged in

        if(!$user) {
            return redirect()->route('user.trx.error')->with('error', 'Nice one Thief');
        }

        // Get form data
        $phone = $validatedData['phone'];
        $plan_id = $validatedData['plan'];
        $network_id = $validatedData['network_id'];
        $planDetails = DataPlans::where('data_id', $plan_id)->with('dataType')->first();// Get the plan details
        $networkName = Networks::findOrFail($planDetails->network_id);
        $networkId   = $networkName->networkId;
        $network = $networkName->network;
        $dataType = $validatedData['dataType'];

        if ($user->account_type === 3) {
            $agentPrice = AgentDataPlanPrice::where(['agent_id' => $user->id,
                                                    'data_id' => $plan_id])->first();
            if ($agentPrice === null) {
                // Populate the agent_data_plan_prices table for the newly upgraded agent
                $dataPlans = DataPlans::all();
                foreach ($dataPlans as $dataPlan) {
                    AgentDataPlanPrice::updateOrCreate(
                        ['agent_id' => $user->id, 'data_id' => $dataPlan->data_id],
                        ['price' => $dataPlan->a_price]
                    );
                }
                $agentPrice = AgentDataPlanPrice::where(['agent_id' => $user->id,
                                                    'data_id' => $plan_id])->first();
            }
        }

        $amount = match ($user->account_type) {
            1 => $planDetails->price,
            2 => $planDetails->r_price,
            3 => $agentPrice->price,
        };

        if (!$planDetails) {
            return redirect()->route('user.trx.error')->with('error', 'Unidentified data plan.');
        }

        if ($amount === null || $amount > $user->balance) {
            return redirect()->route('user.trx.error')->with('error', 'Insufficient balance.');
        }

        $commission = $amount * 0.01;

        $trx = getTrx();

        $reference = $this->generateRequestId();

        $manual = DataControls::where('network', $network_id)->where('data_type', $dataType)->first();
        $main_switch = DataControls::where('network', $network_id)->where('data_type', $dataType)->first();
        $reseller = DataControls::where('id', 24)->first();
        $agent = DataControls::where('id', 25)->first();

        //Checking if the Data Bundle is active
        $statusCheck = $this->manualStatusCheck($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $dataType, $phone, $plan_id, $agent, $reseller, $manual, $main_switch, $network_id);

        if (strpos($statusCheck, 'success:') === 0) {
            $successMessage = substr($statusCheck, 8); // Extract message after "success:"
            return redirect()->route('user.trx.success')->with(['response' => $successMessage, 'success' => 'Transaction Successful! Data Subscription Successful!']);
        } elseif (strpos($statusCheck, 'error:') === 0) {
            $errorMessage = substr($statusCheck, 6); // Extract message after "error:"
            return redirect()->route('user.trx.error')->with('error', $errorMessage);
        }

        //Initiate initial data transaction on user account
        $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, null, $phone, $plan_id, 'INITIAL');

        //MTN Network Calls
        if ($network_id == 1) {
            //MTN SME(weekly plans)
            if($planDetails->dataType->id === 2) {
                $apiType = "DIRECT GIFITNG";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
            //MTN GIFTING(monthly plans)
            elseif ($planDetails->dataType->id === 4) {
                if (in_array($planDetails->data_id, [3000, 3001, 3002, 3003])) {
                    $apiType = "DATA TRANSFER";
                } else {
                    $apiType = "DIRECT GIFTING";
                }
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
            //MTN GIFTING(Wweekly plans)
            elseif ($planDetails->dataType->id === 5) {
                $apiType = "DIRECT GIFTING";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
            //MTN SPECIAL DATA(Wweekly plans)
            elseif ($planDetails->dataType->id === 7) {
                $apiType = "DIRECT GIFTING";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
            //MTN XTRASPECIAL DATA(Wweekly plans)
            elseif ($planDetails->dataType->id === 10) {
                $apiType = "DIRECT GIFTING";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
        }
        //GLO Network Calls
        elseif ($network_id == 2) {
            //GLO SME(weekly plans)
            if($planDetails->dataType->id === 2) {
                $apiType = "DIRECT GIFITNG";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
            //GLO GIFTING
            if ($planDetails->dataType->id === 4) {
                if (in_array($planDetails->data_id, [264, 265, 262, 267, 261])) {
                    $apiType = "CORPORATE GIFTING";
                } else {
                    $apiType = "DIRECT GIFTING";
                }
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
            //GLO CORPORATE GIFITING
            elseif ($planDetails->dataType->id === 5) {
                $apiType = "CORPORATE GIFTING";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {

                    $finalResponse = $this->justpayApi($planDetails, $phone);

                    if ($finalResponse['error_code'] == '1986' && $finalResponse['status'] === true) {

                        $apiResponse = $finalResponse['data']['true_response'];
                        // Handle other cases
                        $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'JUSTPAY');

                        return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                    } else {

                        $message = $finalResponse['data']['true_response'] ?? $finalResponse['server_message'] ?? $finalResponse['data']['server_message'] ?? $finalResponse['error'] ?? "Transaction Declined by Provider JPNG. Refer to them";

                        if (empty($message)) {
                            $message = "Network is currently Unavailable";
                        }

                        $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'JUSTPAY');

                        return redirect()->route('user.trx.error')->with('error', $message);
                    }
                }
            }
            //GLO SPECIAL DATA
            elseif ($planDetails->dataType->id === 7) {
                $apiType = "AWOOF GIFTING";
                // Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
        }
        //9MOBILE Network Calls
        elseif ($network_id == 3) {
            //9MOBILE GIFTING
            if ($planDetails->dataType->id === 4) {
                $apiType = "DIRECT GIFTING";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
            //9MOBILE CORPORATE GIFTING
            elseif ($planDetails->dataType->id === 5) {
                $apiType = "CORPORATE GIFTING";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {

                    $finalResponse = $this->justpayApi($planDetails, $phone);

                    if ($finalResponse['error_code'] == '1986' && $finalResponse['status'] === true) {

                        $apiResponse = $finalResponse['data']['true_response'];
                        // Handle other cases
                        $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'JUSTPAY');

                        return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                    } else {

                        $message = $finalResponse['data']['true_response'] ?? $finalResponse['server_message'] ?? $finalResponse['data']['server_message'] ?? $finalResponse['error'] ?? "Transaction Declined by Provider JPNG. Refer to them";

                        if (empty($message)) {
                            $message = "Network is currently Unavailable";
                        }

                        $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'JUSTPAY');

                        return redirect()->route('user.trx.error')->with('error', $message);
                    }
                }
            }
        }
        //AIRTEL Network Calls
        elseif ($network_id == 4) {
            //AIRTEL SME(weekly plans)
            if ($planDetails->dataType->id === 2) {
                $apiType = "DIRECT GIFTING";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
            //AIRTEL GIFTING(monthly plans)
            elseif ($planDetails->dataType->id === 4) {
                $apiType = "DIRECT GIFTING";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTOPILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
            }
            //AIRTEL CORPORATE GIFTING
            elseif ($planDetails->dataType->id === 5) {
                $apiType = "CORPORATE GIFTING";
                //Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {

                    $finalResponse = $this->justpayApi($planDetails, $phone);

                    if ($finalResponse['error_code'] == '1986' && $finalResponse['status'] === true) {

                        $apiResponse = $finalResponse['data']['true_response'];
                        // Handle other cases
                        $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'JUSTPAY');

                        return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                    } else {

                        $message = $finalResponse['data']['true_response'] ?? $finalResponse['server_message'] ?? $finalResponse['data']['server_message'] ?? $finalResponse['error'] ?? "Transaction Declined by Provider JPNG. Refer to them";

                        if (empty($message)) {
                            $message = "Network is currently Unavailable";
                        }

                        $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'JUSTPAY');

                        return redirect()->route('user.trx.error')->with('error', $message);
                    }
                }
            }
            //AIRTEL SPECIAL DATA
            elseif ($planDetails->dataType->id === 7) {
                if ($planDetails->data_id == '80003') {
                    $apiType = "DIRECT GIFTING";
                } else {
                    $apiType = "AWOOF GIFTING";
                }
                // Call Autopilot API
                $finalResponse = $this->autopilotApi($networkId, $planDetails, $phone, $reference, $apiType);

                // Check response status and content
                if (isset($finalResponse['code']) && in_array($finalResponse['code'], ['200', '201', '500'])) {
                    $apiResponse = $finalResponse['data']['message'];

                    $this->handleSuccessfulResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.success')->with(['response' => $apiResponse, 'success' => 'Transaction Successful! Data Subscription Successful!']);
                } else {
                    $message = $finalResponse['data']['message'];

                    $this->handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $message, $phone, $plan_id, 'AUTO-PILOT');

                    return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
                }
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
                'SME/DATA TRANSFER' => 3,
                'SME2' => 3,
                'CORPORATE GIFTING' => 4,
                'GIFTING' => 5,
            ];

            $mtnOrder = [
                'SPECIAL DATA' => 1,
                'CORPORATE GIFTING' => 2,
                'SME/DATA TRANSFER' => 3,
                'GIFTING' => 4,
                'XTRASPECIAL' => 5,
            ];

            $rearrangedOrder = [
                'SPECIAL DATA' => 1,
                'CORPORATE GIFTING' => 2,
                'SME/DATA TRANSFER' => 3,
                'GIFTING' => 4,
                'SME2' => 5,
            ];

            // Adjust the query based on account_type and network_id
            if ($networkId == '1') {
                $query->whereIn('id', [2, 4, 5, 7]);
            } elseif ($networkId == '2' || $networkId == '4') {
                $query->whereIn('id', [2, 4, 5, 7]);
            } elseif ($networkId == '3') {
                $query->whereIn('id', [4]);
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
                if (in_array($user->account_type, ['1','2', '3'])) {
                    if ($networkId == '1') {
                        $dataType->name = match ($dataType->name) {
                            'CORPORATE GIFTING' => 'DAILY PLANS',
                            'SME/DATA TRANSFER' => 'WEEKLY PLANS',
                            'GIFTING' => 'MONTHLY PLANS',
                            default => $dataType->name,
                        };
                    } elseif ($networkId == '2' || $networkId == '4') {
                        $dataType->name = match ($dataType->name) {
                            'CORPORATE GIFTING' => 'DAILY PLANS',
                            'SME/DATA TRANSFER' => 'WEEKLY PLANS',
                            'GIFTING' => 'MONTHLY PLANS',
                            default => $dataType->name,
                        };
                    } elseif ($networkId == '3') {
                        $dataType->name = match ($dataType->name) {
                            'GIFTING' => 'MONTHLY PLANS',
                            default => $dataType->name,
                        };
                    }
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
        $query = DataPlans::query()->with(['network', 'dataType', 'mainDataType']);

        // Check if both network_id and dataType are provided
        if ($network && $dataType) {
            // Filter by network_id and dataType
            $query->where('network_id', $network)->where('type', $dataType);

            // Retrieve the data plans
            $dataPlans = $query->orderBy('price', 'asc')->get();

            // Retrieve agent-specific prices if the user is an agent
            if ($user->account_type == 3) {
                foreach ($dataPlans as $dataPlan) {
                    $agentPrice = AgentDataPlanPrice::where('agent_id', $user->id)
                        ->where('data_id', $dataPlan->data_id)
                        ->first();

                    // Add a_price dynamically
                    $dataPlan->a_price = $agentPrice ? $agentPrice->price : $dataPlan->a_price;
                }
            }

            // Ensure relationships are loaded properly
            $dataPlans->load('dataType','mainDataType');
        } else {
            $dataPlans = [];
        }
        return response()->json($dataPlans);
    }

    public function generateRequestId() {

        $timestamp = now('Africa/Lagos')->format('YmdHis');

        $prefix = "MOEVAN";

        // Generate 9 random alphanumeric characters (27 - 12 - 6 = 9)
        $randomString = Str::random(7);

        // Ensure the final request ID is exactly 27 characters
        $requestId = strtoupper(substr($prefix . $timestamp . $randomString, 0, 30));

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

        // Define the custom order arrays
        $customOrder = [
            'SPECIAL DATA' => 1,
            'SME/DATA TRANSFER' => 3,
            'SME2' => 3,
            'CORPORATE GIFTING' => 4,
            'GIFTING' => 5,
        ];

        $mtnOrder = [
            'SPECIAL DATA' => 1,
            'CORPORATE GIFTING' => 2,
            'SME/DATA TRANSFER' => 3,
            'GIFTING' => 4,
            'XTRASPECIAL' => 5,
        ];

        $rearrangedOrder = [
            'SPECIAL DATA' => 1,
            'CORPORATE GIFTING' => 2,
            'SME/DATA TRANSFER' => 3,
            'GIFTING' => 4,
            'SME2' => 5,
        ];

        // Adjust the query based on account_type and network_id
        if ($networkId == '1') {
            $query->whereIn('id', [2, 4, 5, 7, 10]);
        } elseif ($networkId == '2' || $networkId == '4') {
            $query->whereIn('id', [2, 4, 5, 7]);
        } elseif ($networkId == '3') {
            $query->whereIn('id', [4]);
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
        $dataTypes->transform(function ($dataType) use ($networkId) {
                if ($networkId == '1') {
                    $dataType->name = match ($dataType->name) {
                        'CORPORATE GIFTING' => 'DAILY PLANS',
                        'SME/DATA TRANSFER' => 'WEEKLY PLANS',
                        'GIFTING' => 'MONTHLY PLANS',
                        default => $dataType->name,
                    };
                } elseif ($networkId == '2' || $networkId == '4') {
                    $dataType->name = match ($dataType->name) {
                        'CORPORATE GIFTING' => 'DAILY PLANS',
                        'SME/DATA TRANSFER' => 'WEEKLY PLANS',
                        'GIFTING' => 'MONTHLY PLANS',
                        default => $dataType->name,
                    };
                } elseif ($networkId == '3') {
                    $dataType->name = match ($dataType->name) {
                        'GIFTING' => 'MONTHLY PLANS',
                        default => $dataType->name,
                    };
                }

            return $dataType;
        });
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

        // Define the custom order arrays
        $customOrder = [
            'SPECIAL DATA' => 1,
            'SME/DATA TRANSFER' => 3,
            'SME2' => 3,
            'CORPORATE GIFTING' => 4,
            'GIFTING' => 5,
        ];

        $mtnOrder = [
            'SPECIAL DATA' => 1,
            'CORPORATE GIFTING' => 2,
            'SME/DATA TRANSFER' => 3,
            'GIFTING' => 4,
            'XTRASPECIAL' => 5,
        ];

        $rearrangedOrder = [
            'SPECIAL DATA' => 1,
            'CORPORATE GIFTING' => 2,
            'SME/DATA TRANSFER' => 3,
            'GIFTING' => 4,
            'SME2' => 5,
        ];

        // Adjust the query based on account_type and network_id
        if ($networkId == '1') {
            $query->whereIn('id', [2, 4, 5, 7, 10]);
        } elseif ($networkId == '2' || $networkId == '4') {
            $query->whereIn('id', [2, 4, 5, 7]);
        } elseif ($networkId == '3') {
            $query->whereIn('id', [4]);
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
        $dataTypes->transform(function ($dataType) use ($networkId) {
                if ($networkId == '1') {
                    $dataType->name = match ($dataType->name) {
                        'CORPORATE GIFTING' => 'DAILY PLANS',
                        'SME/DATA TRANSFER' => 'WEEKLY PLANS',
                        'GIFTING' => 'MONTHLY PLANS',
                        default => $dataType->name,
                    };
                } elseif ($networkId == '2' || $networkId == '4') {
                    $dataType->name = match ($dataType->name) {
                        'CORPORATE GIFTING' => 'DAILY PLANS',
                        'SME/DATA TRANSFER' => 'WEEKLY PLANS',
                        'GIFTING' => 'MONTHLY PLANS',
                        default => $dataType->name,
                    };
                } elseif ($networkId == '3') {
                    $dataType->name = match ($dataType->name) {
                        'GIFTING' => 'MONTHLY PLANS',
                        default => $dataType->name,
                    };
                }
            return $dataType;
        });
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

        // Define the custom order arrays
        $customOrder = [
            'SPECIAL DATA' => 1,
            'SME/DATA TRANSFER' => 3,
            'SME2' => 3,
            'CORPORATE GIFTING' => 4,
            'GIFTING' => 5,
        ];

        $mtnOrder = [
            'SPECIAL DATA' => 1,
            'CORPORATE GIFTING' => 2,
            'SME/DATA TRANSFER' => 3,
            'GIFTING' => 4,
            'XTRASPECIAL' => 5,
        ];

        $rearrangedOrder = [
            'SPECIAL DATA' => 1,
            'CORPORATE GIFTING' => 2,
            'SME/DATA TRANSFER' => 3,
            'GIFTING' => 4,
            'SME2' => 5,
        ];

        // Adjust the query based on account_type and network_id
        if ($networkId == '1') {
            $query->whereIn('id', [2, 4, 5, 7, 10]);
        } elseif ($networkId == '2' || $networkId == '4') {
            $query->whereIn('id', [2, 4, 5, 7]);
        } elseif ($networkId == '3') {
            $query->whereIn('id', [4]);
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
        $dataTypes->transform(function ($dataType) use ($networkId) {
                if ($networkId == '1') {
                    $dataType->name = match ($dataType->name) {
                        'CORPORATE GIFTING' => 'DAILY PLANS',
                        'SME/DATA TRANSFER' => 'WEEKLY PLANS',
                        'GIFTING' => 'MONTHLY PLANS',
                        default => $dataType->name,
                    };
                } elseif ($networkId == '2' || $networkId == '4') {
                    $dataType->name = match ($dataType->name) {
                        'CORPORATE GIFTING' => 'DAILY PLANS',
                        'SME/DATA TRANSFER' => 'WEEKLY PLANS',
                        'GIFTING' => 'MONTHLY PLANS',
                        default => $dataType->name,
                    };
                } elseif ($networkId == '3') {
                    $dataType->name = match ($dataType->name) {
                        'GIFTING' => 'MONTHLY PLANS',
                        default => $dataType->name,
                    };
                }

            return $dataType;
        });

        return response()->json($dataTypes);
    }

    public function getAdminDataPlans(Request $request) {

        $network = $request->input('network_id');
        $dataType = $request->input('dataType');

        // Initialize the query builder
        $query = DataPlans::query()->with(['network', 'dataType', 'mainDataType']);

        // Check if both network_id and dataType are provided
        if ($network && $dataType) {
            // Add conditions to filter by network_id and dataType
            $query->where('network_id', $network)->where('type', $dataType);

            $dataPlans = $query->orderBy('price', 'asc')->get();

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

    public function handleManualResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, $provider) {

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

    public function handleFailedResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $apiResponse, $phone, $plan_id, $provider) {

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

    public function autopilotApi($networkId, $planDetails, $phone, $reference, $dataType) {

        $autopilotApiKey = Env::get('AUTOPILOT_API_KEY');

        $baseUrl = 'https://autopilotng.com/api/live';

        $body = [
            'networkId' => $networkId,
            'dataType' => $dataType,
            'planId' => $planDetails->plan_id,
            'phone' => $phone,
            'reference' => $reference,
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $autopilotApiKey,
        ];

        $response = Http::withHeaders($headers)->timeout(38001)->post("$baseUrl/v1/data", $body);

        return $response->json();
    }

    public function justpayApi($planDetails, $phone) {

        $apiKey = Env::get('JUSTPAY_API_KEY');
        $callbackUrl = "https://moevan.com";

        $payload = [
            'api_key' => $apiKey,
            'product_code' => $planDetails->product_code,
            'phone' => $phone,
            'callback' => $callbackUrl,
        ];

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $response = Http::withHeaders($headers)->timeout(38001)
            ->post('https://justpayng.com/api/v2/datashare/index.php', $payload);

        $justpay = $response->json();

        return $justpay;
    }

    public function manualStatusCheck($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $dataType, $phone, $plan_id, $agent, $reseller, $manual, $main_switch) {

        if ($manual->status === 1) {
            return $this->handleSuccessfulTransaction($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $phone, $plan_id);
        }

        if ($main_switch->active === 0) {
            return 'error: This Bundle is currently not available at the moment. Use other types of bundles.';
        }

        //Agent Special Data
        if ($user->account_type == '3' && $planDetails->network_id == '1' && $dataType == '7') {
            if ($agent->status === 1) {
                return $this->handleSuccessfulTransaction($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $phone, $plan_id);
            }
            if ($agent->active === 0) {
                return 'error: This Bundle is currently not available at the moment. Use other types of bundles.';
            }
        }

        //Reseller Special Data
        if ($user->account_type == '2' && $planDetails->network_id == '1' && $dataType == '7') {
            if ($reseller->status === 1) {
                return $this->handleSuccessfulTransaction($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $phone, $plan_id);
            }
            if ($reseller->active === 0) {
                return 'error: This Bundle is currently not available at the moment. Use other types of bundles.';
            }
        }
        return 'continue';
    }

    function handleSuccessfulTransaction($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, $phone, $plan_id) {
        $this->handleManualResponse($user, $amount, $commission, $trx, $networkName, $reference, $planDetails, 'Data purchase transaction initiated!', $phone, $plan_id, 'MOEVAN');
        return 'success: Your Data Purchase was successful. Enjoy premium services with Moevan!';
    }

}

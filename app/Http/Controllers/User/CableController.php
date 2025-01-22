<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CableTransactions;
use App\Models\Providers;
use App\Models\ProvidersSubscription;
use App\Models\Transaction;
use App\Models\GeneralSwitch;
use App\Models\User;
use App\Rules\ReCaptchaV3;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CableController extends Controller
{
    public function performTransaction(Request $request)
    {
        // Validate request data
        $validate = $request->validate([
            'billersCode' => 'required',
            'cableName' => 'required',
            'amount' => 'required|numeric',
            'phone' => 'required|numeric',
            'customer' => 'required|string',
            'variation_code' => 'required'
        ]);
    
        $user = User::find(auth()->guard('web')->id());
        $amount = $validate['amount'];
    
        // Check user balance
        if ($amount > $user->balance) {
            return redirect()->route('user.trx.error')->with('error', 'Insufficient balance.');
        }
    
        // Initialize transaction data
        $billersCode = $validate['billersCode'];
        $serviceID = $validate['cableName'];
        $phone = $validate['phone'];
        $customer = $validate['customer'];
        $variationCode = $validate['variation_code'];
        $requestID = $this->generateRequestId();
        $trx = getTrx();
        $service_id = Providers::where('name', $serviceID)->first();
    
        // Check general switches
        $manual = GeneralSwitch::where('id', 2)->first();
        $main = GeneralSwitch::where('id', 2)->first();
    
        // Handle manual processing
        if ($manual->status == '1') {
            $price = $this->calculateDiscountedPrice($user->account_type, $amount);
            $this->handleSuccessfulResponse($requestID, $user, $service_id, $variationCode, $phone, $amount, $trx, $billersCode);
            return redirect()->route('user.trx.success')->with('success', 'TV Subscription purchase successful!');
        }
    
        // Handle main switch status
        if ($main->active == '0') {
            return redirect()->route('user.trx.error')->with('error', 'An error occurred while processing your request.');
        }
        
        $this->handleSuccessfulResponse($requestID, $user, $service_id, $variationCode, $phone, $amount, $trx, $billersCode);
    
        try {
            // Your Autopilot API key
            $autopilotApiKey = env('AUTOPILOT_API_KEY');
    
            // Base URL of the API
            $baseUrl = 'https://autopilotng.com/api/live';
                
            // Prepare API request
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.$autopilotApiKey,
            ];
            
            $body = [
                'cableType' => $serviceID,
                'planId' => $variationCode,
                'paymentTypes' => 'FULL_PAYMENT',
                'customerName' => $customer,
                'smartCardNo' => $billersCode,
                'reference' => $requestID
            ];
        
            // Send API request
            $response = Http::withHeaders($headers)
                            ->timeout(38000)
                            ->post($baseUrl. '/v1/cable', $body);
    
            $responseData = $response->json();
    
            if (isset($responseData['code']) && in_array($responseData['code'], ['200', '201', '500'])) {
                // Handle successful transaction
                $this->handleSuccessfulResponse($requestID, $user, $service_id, $variationCode, $phone, $amount, $trx, $billersCode);
                return redirect()->route('user.trx.success')->with('success', 'TV Subscription Purchase successful! ' . $responseData['data']['message']);
            } else {
                // Handle error
                $error = $responseData['response_description'] ?? 'An unknown error occurred';
                return redirect()->route('user.trx.error')->with('error', $error);
            }
        } catch (\Exception $e) {
            
            Log::error('An error occurred while processing the transaction: ' . $e->getMessage());
            return redirect()->route('user.trx.error')->with('error', 'An error occurred while processing your request.');
        }
    }
    
    /**
     * Calculates the discounted price based on account type.
     */
    protected function calculateDiscountedPrice($accountType, $amount)
    {
        switch ($accountType) {
            case '1':
                $discount = $amount * 0.005;  // 0.5% discount
                break;
            case '2':
            case '3':
                $discount = $amount * 0.01;   // 1% discount
                break;
            default:
                $discount = 0;
        }
    
        // Return the discounted price as a float
        return $amount - $discount;
    }
    
    /**
     * Processes and logs the transaction details.
     */
    protected function processTransaction($user, $price, $trx, $requestID, $billersCode, $service_id, $phone, $type, $variationCode)
    {
        $user->balance -= $price;
        $user->save();
    
        // Log Cable Transaction
        $cableTransaction = new CableTransactions;
        $cableTransaction->amount = $price;
        $cableTransaction->user_id = $user->id;
        $cableTransaction->trx = $trx;
        $cableTransaction->cable_trx = $requestID;
        $cableTransaction->billers_code = $billersCode;
        $cableTransaction->service_id = $service_id->id;
        $cableTransaction->type = 'Bouquet Purchase';
        $cableTransaction->package = $variationCode;
        $cableTransaction->phone = $phone;
        $cableTransaction->status = '1';
        $cableTransaction->save();
    
        // Log main transaction
        $transaction = new Transaction;
        $transaction->user_id = $user->id;
        $transaction->amount = $price;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type = '-';
        $transaction->details = 'Purchased ' . strtoupper($service_id->name) . ' Subscription for ' . $billersCode;
        $transaction->status = '2';
        $transaction->trx = $trx;
        $transaction->save();
    }
    
    /**
     * Handles a successful response by checking for existing transactions and updating or creating records.
     */
    protected function handleSuccessfulResponse($requestID, $user, $service_id, $variationCode, $phone, $amount, $trx, $billersCode)
    {
        DB::beginTransaction();
    
        try {
            $price = round($this->calculateDiscountedPrice($user->account_type, $amount), 2);
    
            // Check for existing transaction
            $existingTransaction = CableTransactions::where('cable_trx', $requestID)
                ->orWhere('trx', $trx)
                ->first();
    
            if ($existingTransaction) {
                $this->updateExistingTransaction($existingTransaction, $user, $amount, $phone, $requestID);
            } else {
                $this->createNewTransaction($user, $requestID, $trx, $phone, $price, $variationCode, $amount, $service_id, $billersCode);
            }
    
            $this->updateTransactionTable($trx, $user, $price, $service_id, $amount, $billersCode);
    
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Updates an existing transaction.
     */
    protected function updateExistingTransaction($existingTransaction, $user, $amount, $phone, $requestID)
    {
        $existingTransaction->type = 'Bouquet Purchase';
        $existingTransaction->post_bal = $user->balance;
        $existingTransaction->status = '1';
        $existingTransaction->save();
    
        DB::table('processed_notifications')->insert([
            'reference_number' => $requestID,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Creates a new transaction and updates user balance.
     */
    protected function createNewTransaction($user, $requestID, $trx, $phone, $price, $variationCode, $amount, $service_id, $billersCode)
    {
        $preBal = $user->balance;
        $user->balance -= $price;
        $user->save();
    
        $transaction = new CableTransactions;
        $transaction->amount = $price;
        $transaction->pre_bal = $preBal;
        $transaction->post_bal = $user->balance;
        $transaction->user_id = $user->id;
        $transaction->trx = $trx;
        $transaction->cable_trx = $requestID;
        $transaction->billers_code = $billersCode;
        $transaction->service_id = $service_id->id;
        $transaction->type = 'Bouquet Purchase';
        $transaction->package = $variationCode;
        $transaction->phone = $phone;
        $transaction->status = '2';
        $transaction->save();
    }
    
    /**
     * Updates the Transaction table or creates a new record.
     */
    protected function updateTransactionTable($trx, $user, $price, $service_id, $amount, $billersCode)
    {
        $transaction = Transaction::where('trx', $trx)->first();
    
        if ($transaction) {
            $transaction->post_balance = $user->balance;
            $transaction->status = '1';
            $transaction->details = 'Initiated '. 'purchase ' . strtoupper($service_id->name) . ' Subscription for ' . $billersCode;
            $transaction->save();
        } else {
            $transaction = new Transaction;
            $transaction->user_id = $user->id;
            $transaction->amount = $price;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type = '-';
            $transaction->status = '2';
            $transaction->details = 'Purchased ' . strtoupper($service_id->name) . ' Subscription for ' . $billersCode;
            $transaction->trx = $trx;
            $transaction->save();
        }
    }


    public function getSubscriptionPlans($cableName) {
        $provider = Providers::where('name', $cableName)->first();

        if (!$provider) {
            return response()->json(['error' => 'Invalid cable name'], 404);
        }

        $plans = $provider->plans()
            ->orderBy('amount')
            ->get(['variation_code', 'name', 'amount'])
            ->toArray();

        return response()->json(['plans' => $plans]);
    }

    public function generateRequestId()
    {
        // Set the timezone to GMT+1
        $timezone = new DateTimeZone('Europe/Berlin'); // GMT+1

        // Get the current date and time
        $now = new DateTime('now', $timezone);

        // Format the date and time as YYYYMMDDHHII
        $formattedDateTime = $now->format('YmdHi');

        // Generate a random alphanumeric string (you can adjust the length as needed)
        $randomString = Str::random(17); // Adjust the length as needed

        // Concatenate the formatted date and time with the random string
        $requestId = $formattedDateTime . $randomString;

        // Use $requestId as needed
        return $requestId;
    }
}

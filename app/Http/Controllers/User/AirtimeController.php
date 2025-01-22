<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AirtimeTransactions;
use App\Models\Networks;
use App\Models\Transaction;
use App\Models\GeneralSwitch;
use App\Models\User;
use App\Rules\ReCaptchaV3;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AirtimeController extends Controller
{
    public function processTopUp(Request $request) {
        // Validate the form data
        $validatedData = $request->validate([
            'phone' => 'required|numeric',
            'network_id' => 'required|numeric',
            'amount' => 'required|numeric|min:50',
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

        // Get form data
        $phone = $validatedData['phone'];
        $network_id = $validatedData['network_id'];
        $id = auth()->guard('web')->user()->id;

        $network = Networks::findOrFail($network_id);
        $networkName = $network->name;

        $requestID = $this->generateRequestId();

        $manual = GeneralSwitch::where('id', 1)->first();
        $main = GeneralSwitch::where('id', 1)->first();

         if($manual->status == '1') {

                $trx = getTrx(); // Ensure $trx is defined

                $this->handleSuccessfulResponse($requestID, $user, $network_id, $phone, $networkName, $amount, $trx);

                return redirect()->route('user.trx.success')->with('success', 'Top-up successful! Enjoy premium services with Moevan!');
        }

        if($main->active == '0') {

            return redirect()->route('user.trx.error')->with('error', 'An error occurred while processing your request.');
        }

        $trx = getTrx(); 
        
        $this->handleSuccessfulResponse($requestID, $user, $network_id, $phone, $networkName, $amount, $trx);

        try {
            // Your Autopilot API key
            $autopilotApiKey = env('AUTOPILOT_API_KEY');

            // Base URL of the API
            $baseUrl = 'https://autopilotng.com/api/live';

            // Ensure $reference is defined
            $reference = $requestID;

            $body = [
                'networkId' => $network->networkId,
                'airtimeType' => 'VTU',
                'amount' => $amount,
                'phone' => $phone,
                'reference' => $reference
            ];

            $head = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $autopilotApiKey,
            ];

            // Make the HTTP request
            $responses = Http::withHeaders($head)->timeout(38001)->post($baseUrl . '/v1/airtime', $body);

            $responseData = $responses->json();

            // Check response status and content
            if (isset($responseData['code']) && in_array($responseData['code'], ['200', '201', '500'])) {

                $this->handleSuccessfulResponse($requestID, $user, $network_id, $phone, $networkName, $amount, $trx);

                $message = $responseData['data']['message'];

                return redirect()->route('user.trx.success')->with('success', 'Top-up successful! '. $message);
            } else {
                $message = $responseData['data']['message'];

                return redirect()->route('user.trx.error')->with('error', 'Transaction Failed! ' . $message);
            }
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            Log::error('An error occurred while processing the request: ' . $e->getMessage());
            return redirect()->route('user.trx.error')->with('error', 'An error occurred while processing your request.');
        }
    }

    private function applyDiscount($accountType, $amount) {
        switch ($accountType) {
            case '1':
                $discount = $amount * 0.01;
                break;
            case '2':
                $discount = $amount * 0.02;
                break;
            case '3':
                $discount = $amount * 0.025;
                break;
            default:
                $discount = 0;
        }
        return $amount - $discount;
    }

    public function handleSuccessfulResponse($requestID, $user, $network_id, $phone, $networkName, $amount, $trx) {
        
        // Apply discount to determine the final price for the airtime
        $price = round($this->applyDiscount($user->account_type, $amount), 2);
    
        DB::beginTransaction();
    
        try {
            // Check if transaction with the same request ID or trx exists
            $existingTransaction = AirtimeTransactions::where('request_id', $requestID)
                ->orWhere('trx', $trx)
                ->first();
    
            if ($existingTransaction) {
                // Update existing transaction record
                $this->updateExistingTransaction($existingTransaction, $user, $networkName, $amount, $phone, $requestID);
            } else {
                // Create a new transaction if none exists
                $this->createNewTransaction($user, $requestID, $trx, $network_id, $phone, $price, $networkName, $amount);
            }
    
            // Update or create transaction record in the Transaction table
            $this->updateTransactionTable($trx, $user, $price, $networkName, $amount, $phone);
    
            // Commit the transaction after all updates
            DB::commit();
        } catch (\Exception $e) {
            // Rollback in case of any failure
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Updates an existing AirtimeTransaction record.
     */
    protected function updateExistingTransaction($existingTransaction, $user, $networkName, $amount, $phone, $requestID) {
        
        $existingTransaction->type = 'VTU';
        $existingTransaction->post_bal = $user->balance;
        $existingTransaction->details = 'Bought '.$networkName.' ₦'.$amount.' Airtime VTU for '.$phone;
        $existingTransaction->status = '1';
        $existingTransaction->save();
    
        // Log processed notification for tracking
        DB::table('processed_notifications')->insert([
            'reference_number' => $requestID,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    /**
     * Creates a new AirtimeTransaction and updates the user's balance.
     */
    protected function createNewTransaction($user, $requestID, $trx, $network_id, $phone, $price, $networkName, $amount) {
        
        $preBal = $user->balance;
        $user->balance -= number_format($price);
        $user->save();
    
        $transaction = new AirtimeTransactions;
        $transaction->request_id = $requestID;
        $transaction->pre_bal = $preBal;
        $transaction->amount = $price;
        $transaction->post_bal = $user->balance;
        $transaction->user_id = $user->id;
        $transaction->trx = $trx;
        $transaction->airtime_trx = $requestID;
        $transaction->network_id = $network_id;
        $transaction->phone = $phone;
        $transaction->type = 'VTU';
        $transaction->status = '1';
        $transaction->details = 'Initiated Airtime Transactions';
        $transaction->save();
    }
    
    /**
     * Updates the Transaction table or creates a new record if none exists.
     */
    protected function updateTransactionTable($trx, $user, $price, $networkName, $amount, $phone){
        
        $transaction = Transaction::where('trx', $trx)->first();
    
        if ($transaction) {
            $transaction->post_balance = $user->balance;
            $transaction->status = '1';
            $transaction->details = 'Initiated Airtime Transactions';
            $transaction->save();
        } else {
            $transaction = new Transaction;
            $transaction->user_id = $user->id;
            $transaction->amount = $price;
            $transaction->post_balance = $user->balance;
            $transaction->trx_type = '-';
            $transaction->status = '1';
            $transaction->details = 'Bought '.$networkName.' ₦'.$amount.' Airtime VTU for '.$phone;
            $transaction->trx = $trx;
            $transaction->save();
        }
    }


    public function generateRequestId() {

        // Set the timezone to GMT+1
        $timezone = new DateTimeZone('Europe/Berlin'); // GMT+1

        // Get the current date and time
        $now = new DateTime('now', $timezone);

        // Format the date and time as YYYYMMDDHHII
        $formattedDateTime = $now->format('YmdHi');

        // Generate a random alphanumeric string (you can adjust the length as needed)
        $randomString = Str::random(15); // Adjust the length as needed

        // Concatenate the formatted date and time with the random string
        $requestId = $formattedDateTime . $randomString;

        // Use $requestId as needed
        return $requestId;
    }

    public function search(Request $request) {

        $searchTerm = $request->input('search');

        $keywords = explode(' ', $searchTerm);

        $query = AirtimeTransactions::query();

        foreach ($keywords as $keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('amount', $keyword)
                      ->orWhere('trx', 'like', '%' . $keyword . '%')
                      ->orWhere('network_id', $keyword)
                      ->orWhere('phone', 'like', '%' . $keyword . '%');
            });
        }

        $transactions = $query->get();

        return view('transactions.index', compact('transactions'));
    }
}

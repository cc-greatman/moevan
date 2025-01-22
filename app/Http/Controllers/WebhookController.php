<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Referral;
use App\Models\WalletTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
    public function handle(Request $request) {
        
        // IP Whitelist - Verify IP address against Monnify IP 35.242.133.146
       $ip = $request->ip();
       if ($ip !== "35.242.133.146") {
           return response()->json(['error' => 'Invalid IP'], 403);
       }

       // Get raw JSON request string
       $raw_request = $request->getContent();

       // Your Secret Key found in your Monnify dashboard, developer menu
       $SECRET_KEY = config('app.monnify_secret_key');

       // Next, we need to compute and compare the hash sent via the header as "monnify-signature".
       $signature = $request->header('monnify-signature');
       $computedHash = hash_hmac('sha512', $raw_request, $SECRET_KEY);
       if ($computedHash !== $signature) {
           return response()->json(['error' => 'Invalid Hash'], 403);
       }

       // Parse request to array
       $request_array = json_decode($raw_request, true);

       // Check for duplicate notifications
       $processedNotification = DB::table('processed_notifications')
           ->where('reference_number', $request_array['eventData']['transactionReference'])
           ->first();

       if ($processedNotification) {
           // Notification already processed, return success response
           return response()->json(['message' => 'Notification already processed'], 200);
       }

       // Validate the payload
       $request->validate([
           'eventType' => 'required|string|in:SUCCESSFUL_TRANSACTION',
           'eventData' => 'required|array',
           'eventData.amountPaid' => 'required|numeric',
           'eventData.customer.email' => 'required|email',
           'eventData.transactionReference' => 'required|string', // Add validation for transactionReference
       ]);

       try {
           // Start a database transaction
           DB::beginTransaction();

           // Find the user by email
           $user = User::where('email', $request_array['eventData']['customer']['email'])->first();

           if (!$user) {
               // Handle user not found
               return response()->json(['error' => 'User not found'], 404);
           }

           $preBal = $user->balance;
           
           if($user->account_type === 1) {
               
                $depositAmount = $request_array['eventData']['amountPaid'];
           } else {
               
                $depositAmount = $request_array['eventData']['settlementAmount'];
           }
           
           
           // Generate transaction
           $trx = getTrx();

           // Create WalletTransactions record
           $walletTransaction = new WalletTransactions();
           $walletTransaction->user_id = $user->id;
           $walletTransaction->pre_bal = $preBal;

           // Update user balance
           $user->balance += $depositAmount;
           $user->save();


           $walletTransaction->amount = $depositAmount;
           $walletTransaction->post_bal = $user->balance;
           $walletTransaction->reference_number = $request_array['eventData']['transactionReference'];
           $walletTransaction->mode = "Monnify TopUp";
           $walletTransaction->details = '₦' . number_format($depositAmount, 2) . ' has been added to your balance';
           $walletTransaction->status = '1';
           $walletTransaction->type = "+";
           $walletTransaction->trx = $trx;
           $walletTransaction->save();

           // Create Transaction record
           $transaction = new Transaction;
           $transaction->user_id = $user->id;
           $transaction->amount = $depositAmount;
           $transaction->post_balance = $user->balance;
           $transaction->trx_type = '+';
           $transaction->details = '₦' . number_format($depositAmount, 2) . ' has been added to your balance';
           $transaction->status = '1';
           $transaction->trx = $trx;
           $transaction->save();

           $referral = Referral::where('user_id', $user->id)->first();

            if ($referral) {
                $referrer = User::find($referral->referrer_id);
                if ($referrer) {
                    if($referrer->account_type == '1') {
                        $commission = $depositAmount * 0.01;

                        $preBal = $referrer->referral_balance;

                        $update = new WalletTransactions;

                        $update->user_id = $referrer->id;
                        $update->pre_bal = $preBal;

                        // Credit referral bonus to the referrer
                        $referrer->referral_balance += number_format($commission,2);
                        $referrer->save();

                        $update->amount = number_format($commission,2);
                        $update->post_bal = $referrer->referral_balance;
                        $update->trx = getTrx();
                        $update->reference_number = null;
                        $update->mode = "Referral Bonus";
                        $update->details = '₦'.number_format($commission,2). ' has been added to your balance';
                        $update->status = '1';
                        $update->type = "+";
                        $update->save();

                        // Create a transaction record for the referral bonus
                        $credit = new Transaction;
                        $credit->user_id = $referrer->id;
                        $credit->amount = $commission; // Adjust the amount as needed
                        $credit->post_balance = $referrer->balance;
                        $credit->trx_type = '+';
                        $credit->status = '1';
                        $credit->details = 'Received referral bonus for Data Purchase';
                        $credit->trx = getTrx();
                        $credit->save();

                    } elseif($referrer->account_type == '2') {
                        $commission = $depositAmount * 0.005;

                        $preBal = $referrer->referral_balance;

                        $update = new WalletTransactions;

                        $update->user_id = $referrer->id;
                        $update->pre_bal = $preBal;

                        // Credit referral bonus to the referrer
                        $referrer->referral_balance += number_format($commission,2);
                        $referrer->save();

                        $update->amount = number_format($commission,2);
                        $update->post_bal = $referrer->referral_balance;
                        $update->trx = getTrx();
                        $update->reference_number = null;
                        $update->mode = "Referral Bonus";
                        $update->details = '₦'.number_format($commission,2). ' has been added to your balance';
                        $update->status = '1';
                        $update->type = "+";
                        $update->save();

                        // Create a transaction record for the referral bonus
                        $credit = new Transaction;
                        $credit->user_id = $referrer->id;
                        $credit->amount = $commission; // Adjust the amount as needed
                        $credit->post_balance = $referrer->balance;
                        $credit->trx_type = '+';
                        $credit->status = '1';
                        $credit->details = 'Received referral bonus for Data Purchase';
                        $credit->trx = getTrx();
                        $credit->save();
                    } elseif($referrer->account_type == '3') {
                        $commission = $depositAmount * 0.0025;

                        $preBal = $referrer->referral_balance;

                        $update = new WalletTransactions;

                        $update->user_id = $referrer->id;
                        $update->pre_bal = $preBal;

                        // Credit referral bonus to the referrer
                        $referrer->referral_balance += number_format($commission,2);
                        $referrer->save();

                        $update->amount = number_format($commission,2);
                        $update->post_bal = $referrer->referral_balance;
                        $update->trx = getTrx();
                        $update->reference_number = null;
                        $update->mode = "Referral Bonus";
                        $update->details = '₦'.number_format($commission,2). ' has been added to your balance';
                        $update->status = '1';
                        $update->type = "+";
                        $update->save();

                        // Create a transaction record for the referral bonus
                        $credit = new Transaction;
                        $credit->user_id = $referrer->id;
                        $credit->amount = $commission; // Adjust the amount as needed
                        $credit->post_balance = $referrer->balance;
                        $credit->trx_type = '+';
                        $credit->status = '1';
                        $credit->details = 'Received referral bonus for Data Purchase';
                        $credit->trx = getTrx();
                        $credit->save();
                    }
                }
            }

           // Commit the transaction
           DB::commit();

           // Save processed notification to the database
           DB::table('processed_notifications')->insert([
               'reference_number' => $request_array['eventData']['transactionReference'],
               'created_at' => now(),
               'updated_at' => now(),
           ]);

           // Return success response
           return response()->json(['message' => 'User balance has been updated successfully'], 200);
       } catch (\Exception $e) {
           // Rollback the transaction on error
           DB::rollBack();

           // Log the error for further investigation
           \Illuminate\Support\Facades\Log::error('Transaction failed: ' . $e->getMessage());

           // Return error response
           return response()->json(['error' => 'Transaction failed'], 500);
       }
    }
    
    public function handleData(Request $request) {
        
        // Log the incoming request for debugging
        Log::info('Incoming webhook request', $request->all());

        try {
            // Get raw JSON request string
            $raw_request = $request->getContent();

            // Parse request to array
            $request_array = json_decode($raw_request, true);

            // Check if request is empty or unable to decode
            if (empty($request_array)) {
                Log::error('Empty or invalid JSON payload received');
                return response()->json(['message' => 'Invalid payload'], 400);
            }

            // Validate incoming request
            $validator = Validator::make($request_array, [
                'status' => 'required|boolean',
                'code' => 'required|integer',
                'data' => 'required|array',
                'data.type' => 'required|string',
                'data.message' => 'required|string',
                'data.yourReference' => 'required|string',
                'data.ourReference' => 'required|string',
                'time' => 'required|date_format:Y-m-d H:i:s',
            ]);

            if ($validator->fails()) {
                Log::error('Validation failed for webhook request', $validator->errors()->toArray());
                return response()->json(['message' => 'Invalid payload'], 422);
            }

            // Extract necessary data from the webhook payload
            $status = $request_array['status'];
            $code = $request_array['code'];
            $message = $request_array['data']['message'];
            $yourReference = $request_array['data']['yourReference'];
            $ourReference = $request_array['data']['ourReference'];
            $type = $request_array['data']['type'];

            // Check for duplicate notifications
            $processedNotification = DB::table('processed_notifications')
                ->where('reference_number', $yourReference)
                ->first();

            if ($processedNotification) {
                // Notification already processed, return success response
                return response()->json(['message' => 'Notification already processed'], 200);
            }

            // Process the webhook based on code and type
            if ($code == 200) {
                if ($type === 'Data') {
                    DB::table('data_transactions')->where('reference', $yourReference)->update([
                        'response' => $message,
                        'provider' => "AUTO-PILOT",
                        'status' => 1,
                    ]);
                } elseif ($type === 'Airtime') {
                    DB::table('top_up_transactions')->where('reference', $yourReference)->update([
                        'status' => 1,
                    ]);
                }
            } elseif ($code == 424) {
                if ($type === 'Data') {
                    DB::table('data_transactions')->where('reference', $yourReference)->update([
                        'response' => "Purchase Failed",
                        'provider' => "AUTO-PILOT",
                        'status' => 0,
                    ]);
                } elseif ($type === 'Airtime') {
                    DB::table('top_up_transactions')->where('reference', $yourReference)->update([
                        'details' => "Purchase Failed",
                        'status' => 0,
                    ]);
                }
            }

            // Store processed notification to prevent duplicates
            DB::table('processed_notifications')->insert([
                'reference_number' => $yourReference,
                'processed_at' => now(),
            ]);

            // Return success response to webhook
            return response()->json(['message' => 'Webhook processed successfully'], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error processing webhook', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }
}
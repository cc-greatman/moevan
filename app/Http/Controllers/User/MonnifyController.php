<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MonnifyToken;
use App\Models\MonnifyAccounts;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
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
        $request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric',
            'receipt' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], [
            'receipt.max' => 'Receipt image size exceeded 10mb limit'
        ]);
        
        $user = auth()->guard('web')->user();
        
        // Store uploaded image
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }
        
        // Generate transaction reference
        $trx = getTrx(); // Assuming getTrx() generates a transaction reference
    
        // Pre-balance before transaction
        $preBal = $user->balance;
    
        // Create WalletTransactions record with status '2' (pending)
        $walletTransaction = new WalletTransactions();
        $walletTransaction->user_id = $user->id;
        $walletTransaction->sender_name = $request->name;
        $walletTransaction->pre_bal = $preBal;
        $walletTransaction->amount = $request->amount;
        $walletTransaction->post_bal = $user->balance;
        $walletTransaction->reference_number = $trx;
        $walletTransaction->mode = "Manual Funding";
        $walletTransaction->details = 'Pending approval';
        $walletTransaction->status = '2'; // Pending status
        $walletTransaction->type = "+";
        $walletTransaction->receipt_path = $receiptPath;
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
    
        // Redirect with success message or other response
        return redirect()->route('user.trx.success')->with('success', 'Funding request is pending approval.');
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
}

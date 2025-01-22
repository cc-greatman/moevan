<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AgentDataPlanPrice;
use App\Models\DataPlans;
use App\Models\MonnifyToken;
use App\Models\Referral;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletTransactions;
use App\Rules\ReCaptchaV3;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;


class ProfileController extends Controller
{
    public function updateProfilePicture(Request $request) {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:10240',
        ],
        [
            'profile_picture.mimes' => 'Accepted formats are JPEG, PNG, JPG',
            'profile_picture.max'  => 'Max upload size is 10MB'
        ]);

        if ($request->hasFile('profile_picture')) {
            $id = Auth::user()->id;
            $user = User::findOrFail($id);

            $file = $request->file('profile_picture');
            $filename = time() . '_' . $user->username . $file->getClientOriginalName();
            $file->storeAs('profile_uploads', $filename, 'public');

            $user->profile_picture = $filename;
            $user->save();

            $message = "Profile Picture has been updated successfully";

            return redirect()->route('user.trx.success')->with('success', $message);
        }

        $message = 'No image Selected';

        return redirect()->route('user.trx.error')->with('error', $message);
    }

    public function updateProfileInfo(Request $request) {
        $user = Auth::user()->id;

        $this->profileForm($request, $user);

        $message = "Profile details have been updated successfully";

        return redirect()->route('user.trx.success')->with('success', $message);
    }

    public function updateUserPassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password'
        ],[
            'current_password.required' => "Please enter your current password",
        ]);

        $user = Auth::user();

        if (Hash::check($request->current_password, $user->password)) {
            // The passwords match...
            User::where('id', $user->id)->update(['password' => Hash::make($request->new_password)]);

            return redirect()->route('user.trx.success')->with('success', 'Password updated successfully');
        } else {

            return redirect()->route('user.trx.error')->with('error', 'The inputted current password is incorrect');
        }
    }

    public function updateUserStatus(Request $request) {

        $request->validate([
            'user' => 'required|numeric',
        ]);

        $user = User::findOrFail($request->input('user'));
        $commission = 500;

        if ($user->account_type == "1") {

            $amount = 2000;

            if ($amount > $user->balance) {
                return redirect()->route('user.trx.error')->with('error', 'Insufficient balance.');
            }

            // Handle successful response, update user balance, etc.
            $preBal = $user->balance;

            $trx =getTrx();

            $update = new WalletTransactions;

            $update->user_id = $user->id;
            $update->pre_bal = $preBal;

            $user->balance -= $amount;
            $user->account_type = 2;
            $user->save();

            $update->amount = $amount;
            $update->post_bal = $user->balance;
            $update->trx = $trx;
            $update->reference_number = null;
            $update->mode = "Account Upgrade";
            $update->details = 'Account Upgrade Successful';
            $update->status = '1';
            $update->type = "-";
            $update->save();

            //Update Transaction Table
            $update = new Transaction;
            $update->user_id = $user->id;
            $update->amount = $amount;
            $update->post_balance = $user->balance;
            $update->trx_type = '-';
            $update->status = '1';
            $update->details = 'Upgraded User Type to Reseller';
            $update->trx = $trx;
            $update->save();

            $referral = Referral::where('user_id', $user->id)->first();

            if ($referral) {
                $referrer = User::find($referral->referrer_id);
                if ($referrer) {
                    // Credit referral bonus to the referrer
                    $preBal = $referrer->balance;

                    $update = new WalletTransactions;

                    $update->user_id = $referrer->id;
                    $update->pre_bal = $preBal;

                    $referrer->balance += $commission;
                    $referrer->save();

                    $update->amount = $commission;
                    $update->post_bal = $referrer->balance;
                    $update->trx = getTrx();
                    $update->reference_number = null;
                    $update->mode = "Referral Bonus";
                    $update->details = '₦'.$commission. ' has been added to your balance';
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
                    $credit->details = 'Received referral bonus for account upgrade';
                    $credit->trx = getTrx();
                    $credit->save();
                }
            }
            return redirect()->back()->with('success', 'Account Upgrade Successful');
        }

        if ($user->account_type == "2") {

            $amount = 5000;

            if ($amount > $user->balance) {
                return redirect()->route('user.trx.error')->with('error', 'Insufficient balance.');
            }

            // Handle successful response, update user balance, etc.
            $preBal = $user->balance;

            $trx =getTrx();

            $update = new WalletTransactions;

            $update->user_id = $user->id;
            $update->pre_bal = $preBal;

            $user->balance -= $amount;
            $user->account_type = 3;
            $user->save();

            $update->amount = $amount;
            $update->post_bal = $user->balance;
            $update->trx = $trx;
            $update->reference_number = null;
            $update->mode = "Account Upgrade";
            $update->details = 'Account Upgrade Successful';
            $update->status = '1';
            $update->type = "-";
            $update->save();

            //Update Transaction Table
            $update = new Transaction;
            $update->user_id = $user->id;
            $update->amount = $amount;
            $update->post_balance = $user->balance;
            $update->trx_type = '-';
            $update->status = '1';
            $update->details = 'Upgraded User Type to Agent';
            $update->trx = $trx;
            $update->save();

            $referral = Referral::where('user_id', $user->id)->first();

            if ($referral) {
                $referrer = User::find($referral->referrer_id);
                if ($referrer) {
                    // Credit referral bonus to the referrer
                    $preBal = $referrer->balance;

                    $update = new WalletTransactions;

                    $update->user_id = $referrer->id;
                    $update->pre_bal = $preBal;

                    $referrer->balance += $commission;
                    $referrer->save();

                    $update->amount = $commission;
                    $update->post_bal = $referrer->balance;
                    $update->trx = getTrx();
                    $update->reference_number = null;
                    $update->mode = "Referral Bonus";
                    $update->details = '₦'.$commission. ' has been added to your balance';
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
                    $credit->details = 'Received referral bonus for account upgrade';
                    $credit->trx = getTrx();
                    $credit->save();
                }
            }

            // Populate the agent_data_plan_prices table for the newly upgraded agent
            $dataPlans = DataPlans::all();
            foreach ($dataPlans as $dataPlan) {
                AgentDataPlanPrice::updateOrCreate(
                    ['agent_id' => $user->id, 'data_id' => $dataPlan->data_id],
                    ['price' => $dataPlan->a_price]
                );
            }

            return redirect()->back()->with('success', 'Account Upgrade Successful');
        }

    }

    public function withdrawBonus(Request $request) {

        $request->validate([
            'user'    => 'required|numeric',
            'amount'  => 'required|numeric'
        ]);

        $user = User::findOrFail($request->input('user'));
        $amount = $request->input('amount');
        if($amount > $user->referral_balance) {
            return redirect()->route('user.trx.error')->with('error', 'Insufficient balance.');
        }

        $preBal = $user->balance;

        $trx = getTrx();

        $update = new WalletTransactions;

        $update->user_id = $user->id;
        $update->pre_bal = $preBal;

        $user->balance          += $request->amount;
        $user->referral_balance -= $request->amount;
        $user->save();

        $update->amount = $request->amount;
        $update->post_bal = $user->balance;
        $update->trx = $trx;
        $update->reference_number = null;
        $update->mode = "Bonus Withdrawal";
        $update->details = '₦'.$request->amount. ' has been added to your balance';
        $update->status = '1';
        $update->type = "+";
        $update->save();

        //Update Transaction Table
        $update = new Transaction();
        $update->user_id = $user->id;
        $update->amount = $request->amount;
        $update->post_balance = $user->balance;
        $update->trx_type = '+';
        $update->details = '₦'.$request->amount. ' has been added to your balance';
        $update->status = '1';
        $update->trx = $trx;
        $update->save();

        return redirect()->route('user.trx.success')->with('success', 'You have successfully withdrawn your bonus. Please Keep referring people to continue enjoying more benefits!');
    }

    function profileForm($request, $user) {

        $update = User::findOrFail($user);

        $update->first_name = $request->first_name;
        $update->middle_name = $request->middle_name;
        $update->last_name = $request->last_name;
        $update->phone = $request->phone_number;
        $update->dob = $request->dob;
        $update->address = $request->address;
        $update->save();
        
        return redirect()->route('user.trx.success')->with('success', 'Your Profile Details have been updated Successfully');
    }

    public function kycVerification(Request $request) {
        // Validate the incoming request
        $request->validate([
            'id_type' => 'required|string',
        ]);

        // Ensure the user is authenticated
        $id = Auth::guard('web')->user()->id;

        $user = User::findOrFail($id);

        if ($request->input('id_type') === 'nin') {

            // Validate the incoming request
            $request->validate([
                'id_number' => [
                    'required',
                    'string',
                    'size:11',
                    Rule::unique('users', 'nin'),
                ],
            ], [
                'id_number.unique' => 'This NIN number already exists in our records.',
            ]);

            // Prepare the request payload
            $payload = [
                'nin' => $request->input('id_number'),
            ];

            // Retrieve the stored access token
            $accessToken = MonnifyToken::first();
            $yourAccessToken = $accessToken->access_token;

            // Make a POST request to the Monnify API endpoint
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $yourAccessToken,
            ])->timeout(380001)->post('https://api.monnify.com/api/v1/vas/nin-details', $payload);
            
            $responseData = $response->json();
            
            // Check if the API request was successful
            if ($response->successful()) {
                
                if($responseData['requestSuccessful'] === true) {

                    // Compare details: first name, last name, and date of birth
                    $matches = strtolower(trim($user->first_name)) === strtolower(trim($responseData['responseBody']['firstName'])) &&
                                strtolower(trim($user->last_name)) === strtolower(trim($responseData['responseBody']['lastName'])) &&
                                \Carbon\Carbon::parse($user->dob)->format('Y-m-d') === \Carbon\Carbon::parse($responseData['responseBody']['dateOfBirth'])->format('Y-m-d');
    
                    if ($matches) {
    
                        $user->is_kyc_verified = 1;
                        $user->nin = $responseData['responseBody']['nin'];
                        $user->save();
                        
                        session()->put('kyc_verification', true);
    
                        return redirect()->route('user.trx.success')->with('success', 'You have successfully verified your identity');
                    } else {
                        
                        session()->put('kyc_verification', true);
                        
                        return redirect()->route('user.trx.error')->with('error', 'Your Profile Details do not Match with that in the Provided NIN');
                    }
                } else {
                    $message = $responseData['responseMessage'];
                    
                    session()->put('kyc_verification', true);
                    
                    return redirect()->route('user.trx.error')->with('error', $message);
                }
            } else {
                
                session()->put('kyc_verification', true);
                
                return redirect()->route('user.trx.error')->with('error', 'Incomplete or Wrong NIN🤦‍♀️. Please try inputtng the correct one');
            }
            
        } elseif ($request->input('id_type') === 'bvn') {

            // Validate the incoming request
            $request->validate([
                'id_number' => [
                    'required',
                    'string',
                    'size:11',
                    Rule::unique('users', 'bvn'),
                ],
            ], [
                'id_number.unique' => 'This BVN number already exists in our records.',
            ]);

            $bvn = $request->input('id_number');
            $name = $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name;
            $formattedDob = Carbon::createFromFormat('Y-m-d', $user->dob)->format('d-M-Y');

            // Prepare the request payload
            $payload = [
                'bvn' => $bvn,
                'name' => $name,
                'dateOfBirth' => $formattedDob,
                'mobileNo' => $user->phone
            ];

            // Retrieve the stored access token
            $accessToken = MonnifyToken::first();
            $yourAccessToken = $accessToken->access_token;

            // Make a POST request to the Monnify API endpoint
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $yourAccessToken,
            ])->timeout(380001)->post('https://api.monnify.com/api/v1/vas/bvn-details-match', $payload);

            $responseData = $response->json();
            
             // Check if the API request was successful
            if ($response->successful()) {
            
                // Check if request was successful (boolean check)
                if($responseData['requestSuccessful'] === true) {
            
                    // Compare details: first name, last name, and date of birth
                    $matches = $responseData['responseBody']['name']['matchPercentage'];
            
                    if ($matches == 100) {
            
                        // Update user details if it's a full match
                        $user->is_kyc_verified = 1;
                        $user->bvn = $responseData['responseBody']['bvn'];
                        $user->save();
                        
                        session()->put('kyc_verification', true);
                        
                        return redirect()->route('user.trx.success')->with('success', 'You have successfully verified your identity');
                    } else {
                        // Handle partial or no match
                        session()->put('kyc_verification', true);
                        
                        return redirect()->route('user.trx.error')->with('error', 'Your Personal Details do not Match with that in the Provided BVN');
                    }
                } else {
                    // Handle case when API response indicates failure
                    $message = $responseData['responseMessage'];
                    
                    session()->put('kyc_verification', true);
                    
                    return redirect()->route('user.trx.error')->with('error', $message);
                }
            } else {
                
                session()->put('kyc_verification', true);
                
                return redirect()->route('user.trx.error')->with('error', 'Incomplete or Wrong NIN🤦‍♀️. Please try inputtng the correct one');
            }

        }
    }
}

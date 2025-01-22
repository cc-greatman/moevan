<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ElectricTransaction;
use App\Models\Transaction;
use App\Models\GeneralSwitch;
use App\Models\User;
use App\Rules\ReCaptchaV3;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ElectricityController extends Controller
{
    public function processRecharge(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'phone' => 'required|numeric',
            'meter-number' => 'required|numeric',
            'meter-type' => 'required|string',
            'disco-name' => 'required',
            'amount' => 'required|numeric|min:50'
        ], [
            'amount.min' => 'The minimum possible amount is ₦50'
        ]);

        // Get form data
        $phone = $validatedData['phone'];
        $meter_number = $validatedData['meter-number'];
        $variation_code = $validatedData['meter-type'];
        $serviceID =$validatedData['disco-name'];
        $amount =  $validatedData['amount'];
        $id = auth()->guard('web')->user()->id;

        // Check user's balance
        $user = User::findOrFail($id); // Assuming the user is logged in
        if ($amount > $user->balance) {
            return redirect()->route('user.trx.error')->with('error', 'Insufficient balance.');
        }

        $requestID = $this->generateRequestId();

        $trx = getTrx();

        $manual = GeneralSwitch::where('id', 3)->first();
        $main = GeneralSwitch::where('id', 3)->first();

        if($manual->status == '1') {

            if ($user->account_type == '1') {
                // Apply 1% discount for account type 1
                $discount = $amount * 0.005;
                $price = $amount - $discount; // Subtract the discount from the original amount

            } else {
                // Apply 2% discount for account type 2
                $discount = $amount * 0.01;
                $price = $amount - $discount; // Subtract the discount from the original amount
            }

            $preBal = $user->balance;

            // The 'code' is '000', handle success logic here
            //Update Electric Transaction table
            $update = new ElectricTransaction;

            $update->pre_bal = $preBal;

            // Handle successful response, update user balance, etc.
            $user->balance -= $price;
            $user->save();

            $update->amount = $price;
            $update->post_bal = $user->balance;
            $update->user_id = $user->id;
            $update->trx = $trx;
            $update->electric_trx = $requestID;
            $update->meter_no = $meter_number;
            $update->meter_type = $variation_code;
            $update->token = $responseData['token'] ?? $responseData['purchased_code']  ?? $responseData['mainToken'] ?? null;
            $update->units = $responseData['units'] ?? $responseData['MainTokenUnits'] ?? null;
            $update->phone = $phone;
            $update->status = '1';
            $update->save();

            //Update Transaction Table
            $update = new Transaction;
            $update->user_id = $user->id;
            $update->amount = $price;
            $update->post_balance = $user->balance;
            $update->trx_type = '-';
            $update->status = '1';
            $update->details = 'Paid ₦'.$amount. ' for ' .$meter_number;
            $update->trx = $trx;
            $update->save();

             return redirect()->route('user.trx.success')->with('success', 'Transaction Successful! Your payment has been processed');

        }

        if($main->active == '0') {

            return redirect()->route('user.trx.error')->with('error', 'An error occurred while processing your request.');
        }

        // Make API request
        $headers = [
            'api-key' => env('VTPASS_API_KEY'),
            'secret-key' => env('VTPASS_SECRET_KEY'),
            'Content-Type' => 'application/json',
        ];

        $body = [
            'request_id' => $requestID,
            'amount' => $amount,
            'phone' => $phone,
            'serviceID' => $serviceID,
            'variation_code' => $variation_code,
            'billersCode' => $meter_number,
        ];

        try {

            $response = Http::withHeaders($headers)
            ->timeout(38000)
                ->post('https://api-service.vtpass.com/api/pay', $body);

                    $responseData = $response->json();

                    if (isset($responseData['code']) && $responseData['code'] === '000') {

                        if ($user->account_type == '1') {
                            // Apply 1% discount for account type 1
                            $discount = $amount * 0.005;
                            $price = $amount - $discount; // Subtract the discount from the original amount
                            $price = number_format($price, 2); // Format the discounted price with 2 decimal places
                        } else {
                            // Apply 2% discount for account type 2
                            $discount = $amount * 0.01;
                            $price = $amount - $discount; // Subtract the discount from the original amount
                            $price = number_format($price, 2);
                        }
                        $preBal = $user->balance;

                        // The 'code' is '000', handle success logic here
                        //Update Electric Transaction table
                        $update = new ElectricTransaction;

                        $update->pre_bal = $preBal;

                        // Handle successful response, update user balance, etc.
                        $user->balance -= $price;
                        $user->save();

                        $update->amount = $price;
                        $update->post_bal = $user->balance;
                        $update->user_id = $user->id;
                        $update->trx = $trx;
                        $update->electric_trx = $requestID;
                        $update->meter_no = $meter_number;
                        $update->meter_type = $variation_code;
                        $update->token = $responseData['token'] ?? $responseData['purchased_code']  ?? $responseData['mainToken'] ?? null;
                        $update->units = $responseData['units'] ?? $responseData['MainTokenUnits'] ?? null;
                        $update->phone = $phone;
                        $update->status = '1';
                        $update->save();

                        //Update Transaction Table
                        $update = new Transaction;
                        $update->user_id = $user->id;
                        $update->amount = $price;
                        $update->post_balance = $user->balance;
                        $update->trx_type = '-';
                        $update->status = '1';
                        if ($variation_code === 'prepaid' && isset($responseData['units']) ?? $responseData['mainTokenUnits']) {
                            $update->details = 'Bought '.$responseData['units'].'units for '.$meter_number.'with ₦'.$amount;
                        } else {
                            $update->details = 'Paid ₦'.$amount. ' for ' .$meter_number;
                        }
                        $update->trx = $trx;
                        $update->save();



                        if ($variation_code === 'prepaid') {

                            $data = [
                                'meterNumber' => $meter_number,
                                'meterType' => $variation_code,
                                'amount' => $amount,
                                'phone' => $phone,
                            ];

                            if ($variation_code === 'prepaid' && isset($responseData['purchased_code'])) {
                                $data['token'] = $responseData['token'] ?? $responseData['purchased_code']  ?? $responseData['mainToken'] ?? null;
                                $data['units'] = $responseData['units'] ?? $responseData['MainTokenUnits'] ?? null;
                            }


                            $pageTitle = "Customer Token - Moevan";

                            return view('user.electric.token', compact('data','pageTitle'))->with('success', 'Transaction successful!');
                        } else {

                            return redirect()->route('user.trx.success')->with('success', 'Transaction Successful! Your payment has been processed');
                        }


                    } elseif (isset($responseData['code']) && $responseData['code'] === '099') {

                        if ($user->account_type == '1') {
                            // Apply 1% discount for account type 1
                            $discount = $amount * 0.01;
                            $price = $amount - $discount; // Subtract the discount from the original amount
                            $price = number_format($price, 2); // Format the discounted price with 2 decimal places
                        } elseif ($user->account_type == '2') {
                            // Apply 2% discount for account type 2
                            $discount = $amount * 0.02;
                            $price = $amount - $discount; // Subtract the discount from the original amount
                            $price = number_format($price, 2);
                        }
                        $preBal = $user->balance;

                        // The 'code' is '000', handle success logic here
                        //Update Electric Transaction table
                        $update = new ElectricTransaction;

                        $update->pre_bal = $preBal;

                        // Handle successful response, update user balance, etc.
                        $user->balance -= $price;
                        $user->save();

                        $update->amount = $price;
                        $update->post_bal = $user->balance;
                        $update->user_id = $user->id;
                        $update->trx = $trx;
                        $update->electric_trx = $requestID;
                        $update->meter_no = $validatedData['meter-number'];
                        $update->meter_type = $validatedData['meter-type'];
                        $update->phone = $phone;
                        $update->status = '2';
                        $update->save();

                        //Update Transaction Table
                        $update = new Transaction;
                        $update->user_id = $user->id;
                        $update->amount = $price;
                        $update->post_balance = $user->balance;
                        $update->trx_type = '-';
                        $update->status = '2';
                        $update->details = 'Paid ₦'.$amount. ' for ' .$meter_number;
                        $update->trx = $trx;
                        $update->save();

                        return redirect()->route('user.trx.error')->with('warning', 'Transaction Processing');
                    } else {

                            $data = $requestID;

                            $validate = Http::withHeaders($headers)
                                            ->timeout(38000)
                                            ->post('https://api-service.vtpass.com/api/requery', $data);

                            $validated = $validate->json();

                            if (isset($validated['code']) && $validated['code'] === '000' && $validated['content']['transactions']['status'] === 'delivered') {

                                    if ($user->account_type == '1') {
                            // Apply 1% discount for account type 1
                            $discount = $amount * 0.01;
                            $price = $amount - $discount; // Subtract the discount from the original amount
                            $price = number_format($price, 2); // Format the discounted price with 2 decimal places
                        } elseif ($user->account_type == '2') {
                            // Apply 2% discount for account type 2
                            $discount = $amount * 0.02;
                            $price = $amount - $discount; // Subtract the discount from the original amount
                            $price = number_format($price, 2);
                        }
                        $preBal = $user->balance;

                        // The 'code' is '000', handle success logic here
                        //Update Electric Transaction table
                        $update = new ElectricTransaction;

                        $update->pre_bal = $preBal;

                        // Handle successful response, update user balance, etc.
                        $user->balance -= $price;
                        $user->save();

                        $update->amount = $price;
                        $update->post_bal = $user->balance;
                        $update->user_id = $user->id;
                        $update->trx = $trx;
                        $update->electric_trx = $requestID;
                        $update->meter_no = $validatedData['meter-number'];
                        $update->meter_type = $validatedData['meter-type'];
                        $update->phone = $phone;
                        $update->status = '2';
                        $update->save();

                        //Update Transaction Table
                        $update = new Transaction;
                        $update->user_id = $user->id;
                        $update->amount = $price;
                        $update->post_balance = $user->balance;
                        $update->trx_type = '-';
                        $update->status = '2';
                        $update->details = 'Paid ₦'.$amount. ' for ' .$meter_number;
                        $update->trx = $trx;
                        $update->save();

                        return redirect()->route('user.trx.error')->with('warning', 'Transaction Processing');
                            } else {

                                $info = $requestID;

                                $check = Http::withHeaders($headers)
                                                ->timeout(38000)
                                                ->post('https://api-service.vtpass.com/api/requery', $info);

                                $checked = $check->json();

                                if (isset($validated['code']) && $validated['code'] === '000' && $validated['content']['transactions']['status'] === 'delivered') {

                                    if ($user->account_type == '1') {
                            // Apply 1% discount for account type 1
                            $discount = $amount * 0.01;
                            $price = $amount - $discount; // Subtract the discount from the original amount
                            $price = number_format($price, 2); // Format the discounted price with 2 decimal places
                        } elseif ($user->account_type == '2') {
                            // Apply 2% discount for account type 2
                            $discount = $amount * 0.02;
                            $price = $amount - $discount; // Subtract the discount from the original amount
                            $price = number_format($price, 2);
                        }
                        $preBal = $user->balance;

                        // The 'code' is '000', handle success logic here
                        //Update Electric Transaction table
                        $update = new ElectricTransaction;

                        $update->pre_bal = $preBal;

                        // Handle successful response, update user balance, etc.
                        $user->balance -= $price;
                        $user->save();

                        $update->amount = $price;
                        $update->post_bal = $user->balance;
                        $update->user_id = $user->id;
                        $update->trx = $trx;
                        $update->electric_trx = $requestID;
                        $update->meter_no = $validatedData['meter-number'];
                        $update->meter_type = $validatedData['meter-type'];
                        $update->phone = $phone;
                        $update->status = '2';
                        $update->save();

                        //Update Transaction Table
                        $update = new Transaction;
                        $update->user_id = $user->id;
                        $update->amount = $price;
                        $update->post_balance = $user->balance;
                        $update->trx_type = '-';
                        $update->status = '2';
                        $update->details = 'Paid ₦'.$amount. ' for ' .$meter_number;
                        $update->trx = $trx;
                        $update->save();

                        return redirect()->route('user.trx.error')->with('warning', 'Transaction Processing');
                                } else {

                                    // The 'code' is not '000', handle error logic here
                                     Log::error('An error occurred while processing the successful response: ' . $checked['response_description']);
                                    $error = $checked['response_description'] . '! An error occurred while processing your request.';

                                    return redirect()->route('user.trx.error')->with('error', $error);
                                }
                            }
                        // The 'code' is not '000', handle error logic here
                         Log::error('An error occurred while processing the successful response: ' . $responseData['response_description']);
                        $error = $responseData['response_description'];

                        return redirect()->route('user.trx.error')->with('error', $error);
                    }

        } catch (\Exception $e) {
            // Log the error or handle it as needed
             Log::error('An error occurred while processing the successful response: ' . $e->getMessage());
            return redirect()->route('user.trx.error')->with('error', 'An error occurred while processing your request.');
        }
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
        $randomString = Str::random(4); // Adjust the length as needed

        // Concatenate the formatted date and time with the random string
        $requestId = $formattedDateTime . $randomString;

        // Use $requestId as needed
        return $requestId;
    }

    public function showToken() {
        $pageTitle = "Token - Moevan";

        return view('user.electric.token', compact('pageTitle'));
    }
}

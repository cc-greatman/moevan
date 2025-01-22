<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use App\Models\DataType;
use App\Models\Networks;
use App\Models\WalletTransactions;
use App\Models\AirtimeTransactions;
use App\Models\CableTransactions;
use App\Models\ElectricTransaction;
use App\Models\DataTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use DateTime;
use Exception;

class UserController extends Controller
{
    public function userLogin($id){

    try {
        $user = User::findOrFail($id);

        session()->regenerate();

        $user->session_id = session()->getId();
        $user->save();

        Auth::guard('web')->login($user);

        return redirect()->route('user.dashboard');
    } catch (\Exception $e) {
        // Log the exception message
        Log::error('Error during user login process: ' . $e->getMessage());

        // Optionally, log more details about the exception
        Log::error('Exception details:', [
            'exception' => $e
        ]);

        // Debugging message to ensure this part of code is executed
        Log::info('Catch block executed, returning error response.');

        // Return a response indicating an error occurred
        return response()->json(['error' => 'An error occurred while logging in.'], 500);
    }
    }

    public function deleteUser($id) {

        $user = User::where('id', $id);
        $user->delete();

        return redirect()->back()->with('success', 'User has been deleted successfully');
    }

    public function refundUser($id) {

        $data = DataTransactions::where('id', $id)->first();
        $user = User::findOrFail($data->user_id);

        $preBal = $user->balance;

        $trx = getTrx();

        $update = new WalletTransactions;

        $update->user_id = $data->user_id;
        $update->pre_bal = $preBal;

        $user->balance += $data->amount;
        $user->save();

        $update->amount = $data->amount;
        $update->post_bal = $user->balance;
        $update->trx = $trx;
        $update->reference_number = null;
        $update->mode = "Refund";
        $update->details = '₦'.$data->amount. ' has been refunded to your balance';
        $update->status = '1';
        $update->type = "+";
        $update->save();

        $data->response = "Data Transaction Failed and amount spent has been succesfully Refunded";
        $data->save();

        return redirect()->route('admin.transactions.data.view')->with('success', 'User Balance Has been Updated Successfully');
    }

    public function userFund(Request $request, $id) {

        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $user = User::findOrFail($id);
        $preBal = $user->balance;

        $trx = getTrx();

        $update = new WalletTransactions;

        $update->user_id = $id;
        $update->pre_bal = $preBal;

        $user->balance += $request->amount;
        $user->save();

        $update->amount = $request->amount;
        $update->post_bal = $user->balance;
        $update->trx = $trx;
        $update->reference_number = null;
        $update->mode = "Manual TopUp";
        $update->details = '₦'.$request->amount. ' has been added to your balance';
        $update->status = '1';
        $update->type = "+";
        $update->save();

        //Update Transaction Table
        $update = new Transaction();
        $update->user_id = $id;
        $update->amount = $request->amount;
        $update->post_balance = $user->balance;
        $update->trx_type = '+';
        $update->details = '₦'.$request->amount. ' has been added to your balance';
        $update->status = '1';
        $update->trx = $trx;
        $update->save();

        return redirect()->route('admin.user.topup')->with('success', 'User Balance Has been Updated Successfully');
    }

    public function userDebit(Request $request, $id) {

        $request->validate([
            'amount' => 'required|numeric'
        ]);

        $user = User::findOrFail($id);
        $preBal = $user->balance;

        $trx = getTrx();

        $update = new WalletTransactions;

        $update->user_id = $id;
        $update->pre_bal = $preBal;

        $user->balance -= $request->amount;
        $user->save();

        $update->amount = $request->amount;
        $update->post_bal = $user->balance;
        $update->trx = $trx;
        $update->reference_number = null;
        $update->mode = "Manual TopUp";
        $update->details = '₦'.$request->amount. ' has been debited from your balance';
        $update->status = '1';
        $update->type = "-";
        $update->save();

        //Update Transaction Table
        $update = new Transaction();
        $update->user_id = $id;
        $update->amount = $request->amount;
        $update->post_balance = $user->balance;
        $update->trx_type = '-';
        $update->details = '₦'.$request->amount. ' has been debited from your balance';
        $update->status = '1';
        $update->trx = $trx;
        $update->save();

        return redirect()->route('admin.user.debit')->with('success', 'User Balance Has been Updated Successfully');
    }
    
    public function approveManualTransactions(Request $request, WalletTransactions $transaction) {
        
        // Optionally update user balance
        $user = $transaction->user;
        
        $transaction->status = 1;
        $transaction->pre_bal = $user->balance;
        $transaction->details = '₦' . number_format($transaction->amount, 2) . ' has been added to your balance';
        
        $user->balance += $transaction->amount;
        $user->save();
        
        $transaction->post_bal = $user->balance;
        $transaction->save();

        return redirect()->route('admin.transactions.manual.transactions')->with('success', 'Transaction approved and user funded.');
    }
    
    public function disapproveManualTransactions(Request $request, WalletTransactions $transaction) {
        
         $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);
    
        // Update transaction status to '3' (rejected) and add rejection reason to details
        $transaction->status = '3'; // Rejected status
        $transaction->details = 'Rejected: ' . $request->rejection_reason;
        $transaction->save();
    
        return redirect()->route('admin.transactions.manual.transactions')->with('success', 'Transaction rejected successfully.');
    }

    public function searchTopup(Request $request) {
        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:username,email',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        // Search for the user based on the selected search type
        $user = User::where($searchType, $query)->first();

        // If user not found, return empty result or handle the case as per your requirement
        if (!$user) {
            return view('admin.search.topup', ['user' => null, 'query' => $query, 'pageTitle' => $query .' Search - Moevan']);
        }

        // Pass the search results to the view
        return view('admin.search.topup', ['user' => $user, 'query' => $query, 'pageTitle' => $query .' Search - Moevan']);
    }

    public function searchDebit(Request $request) {
        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:username,email',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        // Search for the user based on the selected search type
        $user = User::where($searchType, $query)->first();

        // If user not found, return empty result or handle the case as per your requirement
        if (!$user) {
            return view('admin.search.debit', ['user' => null, 'query' => $query, 'pageTitle' => $query .' Search - Moevan']);
        }

        // Pass the search results to the view
        return view('admin.search.debit', ['user' => $user, 'query' => $query, 'pageTitle' => $query .' Search - Moevan']);
    }

    public function searchReferral(Request $request) {
        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:username,email',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        // Search for the user based on the selected search type
        $user = User::where($searchType, $query)->first();

        // If user not found, return empty result or handle the case as per your requirement
        if (!$user) {
            return view('admin.search.referrals', ['user' => null, 'query' => $query, 'pageTitle' => $query .' Search - Moevan']);
        }

        // Pass the search results to the view
        return view('admin.search.referrals', ['user' => $user, 'query' => $query, 'pageTitle' => $query .' Search - Moevan']);
    }

    public function searchAirtime(Request $request) {

        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:phone,date,amount,trx,network,username',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        // Perform the search based on the selected search type
        $transactionsQuery = AirtimeTransactions::query()
            ->where(function ($q) use ($searchType, $query) {
                if ($searchType === 'phone') {
                    $q->where('phone', 'LIKE', "%$query%");
                } elseif ($searchType === 'username') {
                    $user = User::where($searchType, $query)->first();
                    if ($user) {
                        $q->where('user_id', $user->id);
                    } else {
                        $q->where('user_id', null);
                    }
                } elseif ($searchType === 'date') {
                    try {
                        // Assuming 'created_at' is the date field to search
                        $userInputDate = $query; // The user input date in d/m/Y format

                        // Check if the input is in the format d/m/Y
                        $date = DateTime::createFromFormat('d/m/Y', $userInputDate);
                        if (!$date) {
                            // If not, check if the input is in the format Y-m-d H:i:s
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $userInputDate);
                        }
                        if ($date) {
                            // Convert to Y-m-d format
                            $formattedDate = $date->format('Y-m-d');
                            $q->whereDate('created_at', $formattedDate);
                        } else {
                            // Handle invalid date format
                            throw new Exception('Invalid date format.');
                        }
                    } catch (Exception $e) {
                        // Handle the exception
                        // Log the error or return an appropriate response
                         Log::error('An error occurred while processing the successful response: ' . $e->getMessage());
                    }
                } elseif ($searchType === 'amount') {
                    $q->where('amount', 'like', "%$query%");
                } elseif ($searchType === 'trx') {
                    $q->where('trx', 'LIKE', "%$query%");
                } elseif ($searchType === 'network') {
                    // Lookup the network ID based on the input value
                    $network = Networks::where('name', 'LIKE', "%$query%")->first();
                    if ($network) {
                        $q->where('network_id', $network->network_id);
                    } else {
                        // If network name not found, return empty result
                        $q->where('network_id', null);
                    }
                }
            })
            ->orderBy('created_at', 'desc');

            // Paginate the search results
            $transactions = $transactionsQuery->paginate(10)->appends(['query' => $query, 'search_type' => $searchType]);

        // Pass the search results to the view
        return view('admin.search.airtime', ['transactions' => $transactions, 'query' => $query, 'pageTitle' => 'Airtime Search History - Moevan']);
    }

    public function searchData(Request $request) {
        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:phone,date,amount,trx,network,status,username,provider,datatype',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        // Perform the search based on the selected search type
        $transactionsQuery = DataTransactions::query()
            ->where(function ($q) use ($searchType, $query) {
                switch ($searchType) {
                    case 'phone':
                        $q->where('phone', 'LIKE', "%$query%");
                        break;
                    case 'username':
                        $user = User::where($searchType, $query)->first();
                        if ($user) {
                            $q->where('user_id', $user->id);
                        } else {
                            $q->whereNull('user_id');
                        }
                        break;
                    case 'provider':
                        $q->where('provider', 'LIKE', "%$query%");
                        break;
                    case 'datatype':
                        $dataType = DataType::where('name', 'LIKE', "%$query%")->first();
                        if ($dataType) {
                            $q->whereHas('dataPlan', function ($query) use ($dataType) {
                                $query->where('type', $dataType->id);
                            });
                        } else {
                            $q->whereDoesntHave('dataPlan');
                        }
                        break;
                    case 'date':
                        try {
                            $date = DateTime::createFromFormat('d/m/Y', $query)
                                    ?: DateTime::createFromFormat('Y-m-d H:i:s', $query);
                            if ($date) {
                                $q->whereDate('created_at', $date->format('Y-m-d'));
                            } else {
                                throw new Exception('Invalid date format.');
                            }
                        } catch (Exception $e) {
                            Log::error('Error processing date format: ' . $e->getMessage());
                        }
                        break;
                    case 'amount':
                        $q->where('amount', 'like', "%$query%");
                        break;
                    case 'trx':
                        $q->where('trx', 'LIKE', "%$query%");
                        break;
                    case 'network':
                        $network = Networks::where('name', 'LIKE', "%$query%")->first();
                        if ($network) {
                            $q->whereHas('dataPlan', function ($query) use ($network) {
                                $query->where('network_id', $network->network_id);
                            });
                        } else {
                            $q->whereDoesntHave('dataPlan');
                        }
                        break;
                    case 'status':
                        $status = strtolower($query) === 'successful' ? '1' : (strtolower($query) === 'failed' ? '0' : null);
                        $q->where('status', $status);
                        break;
                }
            })
            ->orderBy('created_at', 'desc');

        // Paginate the search results
        $transactions = $transactionsQuery->paginate(10)
            ->appends(['query' => $query, 'search_type' => $searchType]);

        return view('admin.search.data', ['transactions' => $transactions, 'query' => $query, 'pageTitle' => 'Data Transaction History - Moevan']);
    }



    public function searchCable(Request $request) {

        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:date,amount,trx,username',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        // Perform the search based on the selected search type
        $transactionsQuery = CableTransactions::query()
            ->where(function ($q) use ($searchType, $query) {
                if ($searchType === 'date') {
                    try {
                        // Assuming 'created_at' is the date field to search
                        $userInputDate = $query; // The user input date in d/m/Y format

                        // Check if the input is in the format d/m/Y
                        $date = DateTime::createFromFormat('d/m/Y', $userInputDate);
                        if (!$date) {
                            // If not, check if the input is in the format Y-m-d H:i:s
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $userInputDate);
                        }
                        if ($date) {
                            // Convert to Y-m-d format
                            $formattedDate = $date->format('Y-m-d');
                            $q->whereDate('created_at', $formattedDate);
                        } else {
                            // Handle invalid date format
                            throw new Exception('Invalid date format.');
                        }
                    } catch (Exception $e) {
                        // Handle the exception
                        // Log the error or return an appropriate response
                         Log::error('An error occurred while processing the successful response: ' . $e->getMessage());
                    }
                } elseif ($searchType === 'username') {
                    $user = User::where($searchType, $query)->first();
                    if ($user) {
                        $q->where('user_id', $user->id);
                    } else {
                        $q->where('user_id', null);
                    }
                } elseif ($searchType === 'amount') {
                    $q->where('amount', 'like', "%$query%");
                } elseif ($searchType === 'trx') {
                    $q->where('trx', 'LIKE', "%$query%");
                }
            })
            ->orderBy('created_at', 'desc');

            // Paginate the search results
            $transactions = $transactionsQuery->paginate(10)->appends(['query' => $query, 'search_type' => $searchType]);

        return view('admin.search.cable', ['transactions' => $transactions, 'query' => $query, 'pageTitle' => 'Cable Search History - Moevan']);
    }

    public function searchElectric(Request $request) {
        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:date,amount,trx,meter_no,meter_type,units,phone,username',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        // Perform the search based on the selected search type
        $transactionsQuery = ElectricTransaction::query()
            ->where(function ($q) use ($searchType, $query) {
                if ($searchType === 'date') {
                    try {
                        // Assuming 'created_at' is the date field to search
                        $userInputDate = $query; // The user input date in d/m/Y format

                        // Check if the input is in the format d/m/Y
                        $date = DateTime::createFromFormat('d/m/Y', $userInputDate);
                        if (!$date) {
                            // If not, check if the input is in the format Y-m-d H:i:s
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $userInputDate);
                        }
                        if ($date) {
                            // Convert to Y-m-d format
                            $formattedDate = $date->format('Y-m-d');
                            $q->whereDate('created_at', $formattedDate);
                        } else {
                            // Handle invalid date format
                            throw new Exception('Invalid date format.');
                        }
                    } catch (Exception $e) {
                        // Handle the exception
                        // Log the error or return an appropriate response
                         Log::error('An error occurred while processing the successful response: ' . $e->getMessage());
                    }
                } elseif ($searchType === 'username') {
                    $user = User::where($searchType, $query)->first();
                    if ($user) {
                        $q->where('user_id', $user->id);
                    } else {
                        $q->where('user_id', null);
                    }
                } elseif ($searchType === 'amount') {
                    $q->where('amount', 'like', "%$query%");
                } elseif ($searchType === 'trx') {
                    $q->where('trx', 'LIKE', "%$query%");
                } elseif ($searchType === 'meter_no') {
                    $q->where('meter_no', 'like', "%$query%");
                } elseif ($searchType === 'meter_type') {
                    $q->where('meter_type', 'LIKE', "%$query%");
                } elseif ($searchType === 'units') {
                    $q->where('units', 'like', "%$query%");
                } elseif ($searchType === 'phone') {
                    $q->where('phone', 'like', "%$query%");
                }
            })
            ->orderBy('created_at', 'desc');

            // Paginate the search results
            $transactions = $transactionsQuery->paginate(10)->appends(['query' => $query, 'search_type' => $searchType]);

        return view('admin.search.electric', ['transactions' => $transactions, 'query' => $query, 'pageTitle' => 'Electric Transaction History - Moevan']);
    }

    public function searchWallet(Request $request) {

        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:mode,date,amount,trx,type,username',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        // Perform the search based on the selected search type
        $transactionsQuery = WalletTransactions::query()
            ->where(function ($q) use ($searchType, $query) {
                if ($searchType === 'type') {
                    if(strtolower($query) === 'credit') {
                        $q->where('type','+');
                    }elseif (strtolower($query) === 'debit') {
                        $q->where('type','-');
                    }else {
                        $q->where('type', null);
                    }
                } elseif ($searchType === 'username') {
                    $user = User::where($searchType, $query)->first();
                        if ($user) {
                            $q->where('user_id', $user->id);
                        } else {
                            $q->whereNull('user_id');
                        }
                } elseif ($searchType === 'date') {
                    try {
                        // Assuming 'created_at' is the date field to search
                        $userInputDate = $query; // The user input date in d/m/Y format

                        // Check if the input is in the format d/m/Y
                        $date = DateTime::createFromFormat('d/m/Y', $userInputDate);
                        if (!$date) {
                            // If not, check if the input is in the format Y-m-d H:i:s
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $userInputDate);
                        }
                        if ($date) {
                            // Convert to Y-m-d format
                            $formattedDate = $date->format('Y-m-d');
                            $q->whereDate('created_at', $formattedDate);
                        } else {
                            // Handle invalid date format
                            throw new Exception('Invalid date format.');
                        }
                    } catch (Exception $e) {
                        // Handle the exception
                        // Log the error or return an appropriate response
                         Log::error('An error occurred while processing the successful response: ' . $e->getMessage());
                    }
                } elseif ($searchType === 'amount') {
                    $q->where('amount', 'like', "%$query%");
                } elseif ($searchType === 'trx') {
                    $q->where('trx', 'LIKE', "%$query%");
                } elseif ($searchType === 'mode') {
                    $q->where('mode', 'LIKE', "%$query%");
                }
            })
            ->orderBy('created_at', 'desc');

            // Paginate the search results
            $transactions = $transactionsQuery->paginate(10)->appends(['query' => $query, 'search_type' => $searchType]);

        return view('admin.search.wallet', ['transactions' => $transactions, 'query' => $query, 'pageTitle' => 'Wallet Search History - Moevan']);
    }
}

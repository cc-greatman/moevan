<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AirtimeTransactions;
use App\Models\CableTransactions;
use App\Models\DataTransactions;
use App\Models\ElectricTransaction;
use App\Models\Networks;
use App\Models\DataPlans;
use App\Models\Providers;
use App\Models\WalletTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{
    public function main() {

        $pageTitle = 'Transaction History - Moevan';

        $user = Auth::user();

        $wallet = WalletTransactions::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        $data = DataTransactions::where('user_id', $user->id)
                                    ->with('dataPlan')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        $voice = AirtimeTransactions::where('user_id', $user->id)
                                    ->with('network')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        $electric = ElectricTransaction::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        $cable = CableTransactions::where('user_id', $user->id)
                                    ->with('providers')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        return redirect()->route('user.transactions.wallet.show');
    }

    public function data() {

        $pageTitle = 'Data Transaction History - Moevan';

        $user = Auth::user();

        $data = DataTransactions::where('user_id', $user->id)
                                    ->with('dataPlan')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        return view('user.transactions.data', compact('pageTitle', 'data'));
    }

    public function viewReceipt($id) {

        $pageTitle = 'Data Transaction Receipt - Moevan';

        $user = Auth::guard('web')->user();

        $data = DataTransactions::where('user_id', $user->id)
                                    ->where('id', $id)
                                    ->with('dataPlan')
                                    ->first();

        return view('user.receipts.data', compact('pageTitle', 'data', 'user'));
    }

    public function viewElectricReceipt($id) {

        $pageTitle = 'Electric Transaction Receipt - Moevan';

        $user = Auth::guard('web')->user();

        $electric = ElectricTransaction::where('user_id', $user->id)
                                    ->where('id', $id)
                                    ->first();

        return view('user.receipts.electric', compact('pageTitle', 'electric', 'user'));
    }

    public function airtime() {

        $pageTitle = 'Airtime Transaction History - Moevan';

        $user = Auth::user();

        $voice = AirtimeTransactions::where('user_id', $user->id)
                                    ->with('network')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        return view('user.transactions.airtime', compact('pageTitle', 'voice'));
    }

    public function wallet() {

        $pageTitle = 'Wallet Transaction History - Moevan';

        $user = Auth::user();

        $wallet = WalletTransactions::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        return view('user.transactions.wallet', compact('pageTitle', 'wallet'));
    }

    public function electric() {

        $pageTitle = 'Electricity Transaction History - Moevan';

        $user = Auth::user();

        $electric = ElectricTransaction::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        return view('user.transactions.electric', compact('pageTitle', 'electric'));
    }

    public function cable() {

        $pageTitle = 'Tv Subscription Transaction History - Moevan';

        $user = Auth::user();

        $cable = CableTransactions::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

        return view('user.transactions.cable', compact('pageTitle', 'cable'));
    }

    public function searchAirtime(Request $request) {

        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:phone,date,amount,trx,network',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        $user = Auth::user();

        // Perform the search based on the selected search type
        $transactionsQuery = $user->airtimeTransactions()
            ->where(function ($q) use ($searchType, $query) {
                if ($searchType === 'phone') {
                    $q->where('phone', 'LIKE', "%$query%");
                } elseif ($searchType === 'date') {
                    // Assuming 'created_at' is the date field to search
                    $q->whereDate('created_at', $query);
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
        return view('user.search.voice', ['transactions' => $transactions, 'query' => $query, 'pageTitle' => 'Airtime Search History - Moevan']);
    }

    public function searchData(Request $request) {
        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:phone,date,amount,trx,network,status',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        $user = Auth::user();

        // Perform the search based on the selected search type
        $transactionsQuery = $user->dataTransactions()
            ->where(function ($q) use ($searchType, $query) {
                if ($searchType === 'phone') {
                    $q->where('phone', 'LIKE', "%$query%");
                } elseif ($searchType === 'date') {
                    // Assuming 'created_at' is the date field to search
                    $q->whereDate('created_at', $query);
                } elseif ($searchType === 'amount') {
                    $q->where('amount', 'like', "%$query%");
                } elseif ($searchType === 'trx') {
                    $q->where('trx', 'LIKE', "%$query%");
                } elseif ($searchType === 'network') {
                    // Lookup the network ID based on the input value
                    $network = Networks::where('name', 'LIKE', "%$query%")->first();
                    if ($network) {
                         $q->whereHas('dataPlan', function ($query) use ($network) {
                            $query->where('network_id', $network->network_id);
                        });
                    } else {
                        // If network name not found, return empty result
                        $q->whereDoesntHave('dataPlans');
                    }
                } elseif ($searchType === 'status') {
                     if(strtolower($query) === 'successful') {
                            $q->where('status','1');
                    }   elseif (strtolower($query) === 'failed') {
                            $q->where('status','0');
                    }   else {
                            $q->where('type', null);
                    }
                }
            })
            ->orderBy('created_at', 'desc');

            // Paginate the search results
            $transactions = $transactionsQuery->paginate(10)->appends(['query' => $query, 'search_type' => $searchType]);

        return view('user.search.data', ['transactions' => $transactions, 'query' => $query, 'pageTitle' => 'Data Transaction History - Moevan']);
    }

    public function searchCable(Request $request) {

        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:date,amount,trx',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        $user = Auth::user();

        // Perform the search based on the selected search type
        $transactionsQuery = $user->cableTransactions()
            ->where(function ($q) use ($searchType, $query) {
                if ($searchType === 'date') {
                    // Assuming 'created_at' is the date field to search
                    $q->whereDate('created_at', $query);
                } elseif ($searchType === 'amount') {
                    $q->where('amount', 'like', "%$query%");
                } elseif ($searchType === 'trx') {
                    $q->where('trx', 'LIKE', "%$query%");
                }
            })
            ->orderBy('created_at', 'desc');

            // Paginate the search results
            $transactions = $transactionsQuery->paginate(10)->appends(['query' => $query, 'search_type' => $searchType]);

        return view('user.search.cable', ['transactions' => $transactions, 'query' => $query, 'pageTitle' => 'Cable Search History - Moevan']);
    }

    public function searchElectric(Request $request) {
        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:date,amount,trx,meter_no,meter_type,units,phone',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

        $user = Auth::user();

        // Perform the search based on the selected search type
        $transactionsQuery = $user->electricTransactions()
            ->where(function ($q) use ($searchType, $query) {
                if ($searchType === 'date') {
                    // Assuming 'created_at' is the date field to search
                    $q->whereDate('created_at', $query);
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

        return view('user.search.electric', ['transactions' => $transactions, 'query' => $query, 'pageTitle' => 'Electric Transaction History - Moevan']);
    }

    public function searchWallet(Request $request) {

        // Validate the search request
        $request->validate([
            'search_type' => 'required|string|in:mode,date,amount,trx,type',
            'query' => 'required|string',
        ]);

        // Extract search parameters from the request
        $searchType = $request->input('search_type');
        $query = $request->input('query');

         $user = Auth::user();

        // Perform the search based on the selected search type
        $transactionsQuery = $user->walletTransactions()
            ->where(function ($q) use ($searchType, $query) {
                if ($searchType === 'type') {
                    if(strtolower($query) === 'credit') {
                        $q->where('type','+');
                    }elseif (strtolower($query) === 'debit') {
                        $q->where('type','-');
                    }else {
                        $q->where('type', null);
                    }
                } elseif ($searchType === 'date') {
                    // Assuming 'created_at' is the date field to search
                    $q->whereDate('created_at', $query);
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

        return view('user.search.wallet', ['transactions' => $transactions, 'query' => $query, 'searchType' => $searchType, 'pageTitle' => 'Wallet Search History - Moevan']);
    }
}

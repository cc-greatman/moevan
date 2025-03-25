<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirtimeTransactions;
use App\Models\CableTransactions;
use App\Models\DataTransactions;
use App\Models\DataType;
use App\Models\ElectricTransaction;
use App\Models\Networks;
use App\Models\Providers;
use App\Models\DataControls;
use App\Models\GeneralSwitch;
use App\Models\Settings;
use App\Models\User;
use App\Models\WalletTransactions;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ViewController extends Controller
{
    public function dashboard() {

        $pageTitle = 'Admin Dashboard - Moevan';

        // Get the total number of users
        $totalUsers = User::count();

        // Get the current month
        $currentMonth = Carbon::now()->format('m');

        // Get the sum of the 'amount' column for transactions with type '+'
        $totalCredit = WalletTransactions::where('type', '+')
            ->whereMonth('created_at', $currentMonth)
            ->sum('amount');

        // Get the sum of the 'amount' column for transactions with type '-'
        $totalData = DataTransactions::whereMonth('created_at', $currentMonth)
                                        ->sum('amount');

        // Get the sum of the 'amount' column for transactions with type '-'
        $totalAirtime = AirtimeTransactions::whereMonth('created_at', $currentMonth)
                                        ->sum('amount');
              // Get the sum of the 'amount' column for transactions with type '-'
        $totalElectric = ElectricTransaction::whereMonth('created_at', $currentMonth)
                                        ->sum('amount');

        // Get the sum of the 'amount' column for transactions with type '-'
        $totalCable = CableTransactions::whereMonth('created_at', $currentMonth)
                                        ->sum('amount');

        $totalDebit = $totalCable + $totalElectric + $totalAirtime + $totalData;

         $totalRem = $totalCredit - $totalDebit;

         $specialData = DataType::where('id', 6)->first();

        return view('admin.dashboard', compact('pageTitle', 'totalUsers', 'totalCredit', 'totalDebit', 'totalRem', 'specialData'));
    }

    public function viewTopup () {

        $pageTitle = 'Manual Funding - Moevan';

        $users = User::orderBy('balance', 'desc')->paginate(15);

        return view('admin.users.topup', compact('pageTitle', 'users'));
    }

    public function viewRolling () {

        $pageTitle = 'Site Settings - Moevan';

        $settings= Settings::all();

        return view('admin.settings.rolling', compact('pageTitle', 'settings'));
    }

    public function viewData () {

        $pageTitle = 'Site Settings - Moevan';

        $reseller = DataType::where('id', 22)->get();

        $agent = DataControls::where('id', 23)->get();

        $mtn = DataControls::where('network', '1')
                            ->whereNotIn('id', [22, 23])
                            ->get();

        $glo = DataControls::where('network', '2')->get();

        $etisalat = DataControls::where('network', '3')->get();

        $airtel = DataControls::where('network', '4')->get();

        return view('admin.settings.data', compact('pageTitle', 'mtn', 'glo', 'etisalat', 'airtel', 'reseller', 'agent'));
    }

    public function viewGeneral() {

        $pageTitle = "Site Settings - Moevan";

        $popup = GeneralSwitch::where('name', 'Popup')->get();

        $airtime = GeneralSwitch::where('name', 'Airtime')->get();

        $cable = GeneralSwitch::where('name', 'Cable')->get();

        $electricity = GeneralSwitch::where('name', 'Electricity')->get();

        return view('admin.settings.general', compact('pageTitle', 'airtime', 'cable', 'electricity', 'popup'));
    }

    public function messageEdit ($id) {

        $pageTitle = 'Site Settings - Moevan';

        $settings= Settings::findOrFail($id);

        return view('admin.settings.rollingUpdate', compact('pageTitle', 'settings'));
    }

    public function viewReferrals () {

        $pageTitle = 'Monitor number of Referral - Moevan';

       $users = User::leftJoin('referrals', 'users.id', '=', 'referrals.referrer_id')
        ->select(
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.username',
            'users.email',
            'users.created_at',
            'users.updated_at',
            DB::raw('COUNT(referrals.id) as referral_count')
        )
        ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.username', 'users.email', 'users.created_at', 'users.updated_at')
        ->orderBy('referral_count', 'desc')
        ->paginate(15);

        return view('admin.users.referrals', compact('pageTitle', 'users'));
    }

    public function viewDebit () {

        $pageTitle = 'Manual Debit - Moevan';

        $users = User::orderBy('balance', 'desc')->paginate(15);

        return view('admin.users.debit', compact('pageTitle', 'users'));
    }

    public function viewPerformTopup ($id) {

        $user = User::findOrFail($id);

        $pageTitle = strtoupper($user->username) . '\'s Account TopUp - Moevan';

        return view('admin.users.performTopup', compact('pageTitle', 'user'));
    }

    public function viewPerformDebit ($id) {

        $user = User::findOrFail($id);

        $pageTitle = strtoupper($user->username) . '\'s Account Debit - Moevan';

        return view('admin.users.performDebit', compact('pageTitle', 'user'));
    }

    public function viewWalletTransactions() {

        $pageTitle = 'Wallet Transactions - Moevan';

        $wallet = WalletTransactions::with('user')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(20);

        return view('admin.transactions.wallet', compact('wallet', 'pageTitle'));
    }

    public function viewManualTransactions() {

        $pageTitle = 'Manual Wallet Transactions - Moevan';

        $wallet = WalletTransactions::with('user')
                                    ->where('mode', 'Manual Funding')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(20);

        return view('admin.transactions.manual', compact('wallet', 'pageTitle'));
    }

    public function showManualTransactions(WalletTransactions $transaction) {

        $pageTitle = 'View Manual Transaction Details - Moevan';

        return view('admin.transactions.details', compact('transaction', 'pageTitle'));
    }

    public function viewAirtimeTransactions() {

        $pageTitle = 'Airtime Transactions - Moevan';

        $voice = AirtimeTransactions::with('user', 'network')
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(20);

        return view('admin.transactions.airtime', compact('voice', 'pageTitle'));
    }

    public function viewDataTransactions() {

        $pageTitle = 'Data Transactions - Moevan';

        $data = DataTransactions::with('dataPlan', 'user')
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(20);

        return view('admin.transactions.data', compact('data', 'pageTitle'));
    }

    public function viewCableTransactions() {

        $pageTitle = 'Cable Transactions - Moevan';

        $cable = CableTransactions::with('providers', 'user')
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(20);

        return view('admin.transactions.cable', compact('cable', 'pageTitle'));
    }

    public function viewElectricTransactions() {

        $pageTitle = 'Electricity Payment Transactions - Moevan';

        $electric = ElectricTransaction::with('user')
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(20);

        return view('admin.transactions.electric', compact('electric', 'pageTitle'));
    }

    public function viewDataPrices() {

        $pageTitle = 'Data Plans Price Edit - Moevan';

        $networks = Networks::take(4)->get();
        $type = DataType::all();

        return view('admin.price.data', compact('pageTitle', 'networks', 'type'));
    }

    public function viewAgentPrices() {

        $pageTitle = 'General Agent Price Edit - Moevan';

        $networks = Networks::take(4)->get();
        $type = DataType::all();

        return view('admin.price.agent', compact('pageTitle', 'networks', 'type'));
    }

    public function viewAgentPrice() {

        $pageTitle = 'Specific Agent Price Edit - Moevan';

        $networks = Networks::take(4)->get();
        $type = DataType::all();

        return view('admin.price.agents', compact('pageTitle', 'networks', 'type'));
    }

    public function viewResellerPrices() {

        $pageTitle = 'Data Plans Price Edit - Moevan';

        $networks = Networks::take(4)->get();
        $type = DataType::all();

        return view('admin.price.reseller', compact('pageTitle', 'networks', 'type'));
    }

    public function viewCablePrices() {

        $pageTitle = 'Cable Plans Price Edit - Moevan';

        $providers = Providers::all();

        return view('admin.price.cable', compact('pageTitle', 'providers'));
    }
}

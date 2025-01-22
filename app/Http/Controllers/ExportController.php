<?php

namespace App\Http\Controllers;

use App\Exports\Airtime;
use App\Exports\Cable;
use App\Exports\Data;
use App\Exports\Electric;
use App\Exports\Wallet;
use App\Models\WalletTransactions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportWallet($filename) {

        return Excel::download(new Wallet, $this->generateFilename($filename), \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportAirtime($filename) {

        return Excel::download(new Airtime, $this->generateFilename($filename), \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportData($filename) {

        return Excel::download(new Data, $this->generateFilename($filename), \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportElectric($filename) {

        return Excel::download(new Electric, $this->generateFilename($filename), \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportCable($filename) {

        return Excel::download(new Cable, $this->generateFilename($filename), \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function generateFilename($filename)
    {
        return $filename .'.csv';
    }
}

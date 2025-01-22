<?php

namespace App\Exports;

use App\Models\WalletTransactions;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class Wallet implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Get the wallet transactions with the username from the users table
        $transactions = WalletTransactions::join('users', 'wallet_transactions.user_id', '=', 'users.id')
            ->select('wallet_transactions.*', 'users.username')
             ->orderBy('wallet_transactions.created_at', 'desc')
            ->get();

        // Transform the collection
        $transformed = $transactions->map(function ($transaction) {
            return [
                'trx' => $transaction->trx,
                'username' => $transaction->username,
                'amount' => "₦".$transaction->amount,
                'type' => $transaction->type === '+' ? 'credit' : 'debit',
                'mode' => $transaction->mode,
                'reference_number' => $transaction->reference_number,
                'status' => $transaction->status == '1' ? 'completed' : 'pending' ,
                'created_at' => Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s'), 
            ];
        });

        return new Collection($transformed);
    }

    /**
     * Get the column names for the wallet_transactions table.
     *
     * @return array
     */
    public function headings(): array
    {
        // Manually define the headings to match the transformed output
        return [
            'Transaction ID',
            'Username',
            'Amount',
            'Type',
            'Mode',
            'Reference Number (Monnify)',
            'Status',
            'Date',
        ];
    }
}

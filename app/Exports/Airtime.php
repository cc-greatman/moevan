<?php

namespace App\Exports;

use App\Models\AirtimeTransactions;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class Airtime implements FromCollection, WithHeadings
{
    /**
     * Fetch all records from the top_up_transactions table and modify the output as required.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get the wallet transactions with the username from the users table
        $transactions = AirtimeTransactions::join('users', 'top_up_transactions.user_id', '=', 'users.id')
            ->join('networks', 'top_up_transactions.network_id', '=', 'networks.network_id')
            ->select('top_up_transactions.*', 'users.username', 'networks.name')
            ->orderBy('top_up_transactions.created_at', 'desc')
            ->get();

        // Transform the collection
        $transformed = $transactions->map(function ($transaction) {
            return [
                'trx' => $transaction->trx,
                'network' => $transaction->name,
                'username' => $transaction->username,
                'amount' => "₦" . $transaction->amount,
                'phone' => $transaction->phone,
                'created_at' => Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s'),
                'status' => $transaction->status == '1' ? 'completed' : 'pending',
            ];
        });

        return new Collection($transformed);
    }

    /**
     * Get the column names for the top_up_transactions table.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Transaction ID',
            'Network',
            'Username',
            'Amount',
            'Phone',
            'Date',
            'Status',
        ];
    }
}

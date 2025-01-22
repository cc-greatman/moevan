<?php

namespace App\Exports;

use App\Models\ElectricTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class Electric implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Get the data transactions with the username and other related data
        $transactions = ElectricTransaction::join('users', 'electric_transactions.user_id', '=', 'users.id')
            ->select('electric_transactions.*','users.username')
            ->orderBy('electric_transactions.created_at', 'desc')
            ->get();

        // Transform the collection
        $transformed = $transactions->map(function ($transaction) {
            return [
                'trx' => $transaction->trx,
                'username' => $transaction->username,
                'meter_number' => $transaction->meter_no,
                'meter_type' => $transaction->meter_type,
                'token' => $transaction->token ?? null,
                'units' => $transaction->units,
                'amount' => "₦" . $transaction->amount,
                'phone' => $transaction->phone,
                'created_at' => Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s'),
                'status' => $transaction->status == '1' ? 'completed' : 'pending',
            ];
        });

        return new Collection($transformed);
    }

    public function headings(): array
    {
        return [
            'Transaction ID',
            'Username',
            'Meter Number',
            'Meter Type',
            'Token',
            'Units',
            'Amount',
            'Customer Phone',
            'Date',
            'Status',
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\DataTransactions;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class Data implements FromCollection, WithHeadings
{
    /**
     * Fetch all records from the data_transactions table and modify the output as required.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get the data transactions with the username and other related data
        $transactions = DataTransactions::join('users', 'data_transactions.user_id', '=', 'users.id')
            ->join('data_plans', 'data_transactions.plan_id', '=', 'data_plans.data_id')
            ->join('networks', 'data_plans.network_id', '=', 'networks.network_id')
            ->join('data_types', 'data_types.id', '=', 'data_plans.type')
            ->select(
                'data_transactions.*',
                'users.username',
                'networks.name as network_name',
                'data_plans.size as plan_size',
                'data_plans.validity as plan_validity',
                'data_types.name as data_type'
            )
            ->orderBy('data_transactions.created_at', 'desc')
            ->get();

        // Transform the collection
        $transformed = $transactions->map(function ($transaction) {
            return [
                'trx' => $transaction->trx,
                'username' => $transaction->username,
                'provider' => $transaction->provider,
                'network' => $transaction->network_name,
                'datatype' => $transaction->data_type,
                'plan' => $transaction->plan_size . " @ " . $transaction->plan_validity . ' Days',
                'amount' => "₦" . $transaction->amount,
                'phone' => $transaction->phone,
                'created_at' => Carbon::parse($transaction->created_at)->format('Y-m-d H:i:s'),
                'status' => $transaction->status == '1' ? 'completed' : 'pending',
            ];
        });

        return new Collection($transformed);
    }

    /**
     * Get the column names for the data_transactions export.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Transaction ID',
            'Username',
            'Provider',
            'Network',
            'Data Type',
            'Plan',
            'Plan Amount',
            'Phone',
            'Date',
            'Status',
        ];
    }
}

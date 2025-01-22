<?php

namespace App\Exports;

use App\Models\CableTransactions;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;

class Cable implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CableTransactions::all();
    }

    public function headings(): array
    {
        return Schema::getColumnListing('cable_transactions');
    }
}

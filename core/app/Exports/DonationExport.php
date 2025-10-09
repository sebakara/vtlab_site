<?php

namespace App\Exports;

use App\BasicExtra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DonationExport implements FromCollection, WithHeadings, WithMapping
{
    public $donations;

    public function __construct($donations)
    {
        $this->donations = $donations;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->donations;
    }

    public function map($donation): array
    {
        $bex = BasicExtra::firstOrFail();

        return [
            $donation->transaction_id,
            $donation->name ? $donation->name : '-',
            $donation->email ? $donation->email : '-',
            $donation->phone ? $donation->phone : '-',
            !empty($donation->event) ? $donation->event->title : '-',
            ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $donation->amount . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''),
            $donation->payment_method,
            $donation->status,
            $donation->created_at
        ];
    }

    public function headings(): array
    {
        return [
            'Donation ID', 'Name', 'Email', 'Phone', 'Event', 'Amount', 'Gateway', 'Payment Status', 'Date'
        ];
    }
}

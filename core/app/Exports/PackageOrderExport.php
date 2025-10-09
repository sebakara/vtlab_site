<?php

namespace App\Exports;

use App\BasicExtra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PackageOrderExport implements FromCollection, WithHeadings, WithMapping
{
    public $pos;
    public $bex;

    public function __construct($pos)
    {
        $this->pos = $pos;
        $this->bex = BasicExtra::firstOrFail();
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->pos;
    }

    public function map($po): array
    {
        if ($po->status == 0) {
            $status = 'Pending';
        } elseif ($po->status == 1) {
            $status = 'Processing';
        } elseif ($po->status == 2) {
            $status = 'Completed';
        } elseif ($po->status == 3) {
            $status = 'Rejected';
        }
        if ($po->payment_status == 0) {
            $paymentStatus = 'Pending';
        } elseif ($po->payment_status == 1) {
            $paymentStatus = 'Completed';
        }

        return [
            $po->order_number,
            $po->name,
            $po->email,
            $po->package_title,
            ($this->bex->base_currency_symbol_position == 'left' ? $this->bex->base_currency_symbol : '') . $po->package_price . ($this->bex->base_currency_symbol_position == 'right' ? $this->bex->base_currency_symbol : ''),
            $po->payment_method,
            $status,
            $paymentStatus,
            $po->created_at
        ];
    }

    public function headings(): array
    {
        return [
            'Order Number', 'Name', 'Email', 'Package', 'Amount', 'Gateway', 'Order Status', 'Payment Status', 'Date'
        ];
    }
}

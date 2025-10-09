<?php

namespace App\Exports;

use App\BasicExtra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EnrollExport implements FromCollection, WithHeadings, WithMapping
{
    public $enrolls;

    public function __construct($enrolls)
    {
        $this->enrolls = $enrolls;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->enrolls;
    }

    public function map($enroll): array
    {
        $bex = BasicExtra::firstOrFail();

        return [
            $enroll->order_number,
            $enroll->user ? $enroll->user->username : '-',
            $enroll->first_name ? $enroll->first_name : '-',
            $enroll->email,
            !empty($enroll->course) ? $enroll->course->title : '-',
            ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $enroll->current_price . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''),
            $enroll->payment_method,
            $enroll->payment_status,
            $enroll->created_at
        ];
    }

    public function headings(): array
    {
        return [
            'Order Number', 'Username', 'Name', 'Email', 'Course', 'Price', 'Gateway', 'Payment Status', 'Date'
        ];
    }
}

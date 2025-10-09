<?php

namespace App\Exports;

use App\BasicExtra;
use App\ProductOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PorductOrderExport implements FromCollection, WithHeadings, WithMapping
{
    public $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->orders;
    }

    public function map($order): array
    {
        $bex = BasicExtra::firstOrFail();

        return [
            $order->order_number,
            $order->billing_fname,
            $order->billing_email,
            $order->billing_number,
            $order->billing_city,
            $order->billing_country,
            $order->shpping_fname,
            $order->shpping_email,
            $order->shpping_number,
            $order->shpping_city,
            $order->shpping_country,
            $order->method,
            !empty($order->shipping_method) ? $order->shipping_method : '-',
            $order->payment_status,
            $order->order_status,
            ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $order->cart_total . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''),
            ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $order->discount . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''),
            ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $order->tax . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''),
            ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $order->shipping_charge . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''),
            ($bex->base_currency_symbol_position == 'left' ? $bex->base_currency_symbol : '') . $order->total . ($bex->base_currency_symbol_position == 'right' ? $bex->base_currency_symbol : ''),
            $order->created_at
        ];
    }

    public function headings(): array
    {
        return [
            'Order Number', 'Billing Name', 'Billing Email', 'Billing Phone', 'Billing City', 'Billing Country', 'Shipping Name', 'Shipping Email', 'Shipping Phone', 'Shipping City', 'Shipping Country', 'Gateway', 'Shipping Method', 'Payment Status', 'Order Status', 'Cart Total', 'Discount', 'Tax', 'Shipping Charge', 'Total', 'Date'
        ];
    }
}

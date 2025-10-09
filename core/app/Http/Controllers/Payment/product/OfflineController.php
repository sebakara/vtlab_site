<?php

namespace App\Http\Controllers\Payment\product;

use App\Http\Controllers\Payment\product\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OfflineController extends PaymentController
{
    public function store(Request $request, $gid) {
        if (!Session::has('cart')) {
            return view('errors.404');
        }

        $success_url = action('Payment\product\PaymentController@payreturn');

        if($this->orderValidation($request, 'offline')) {
            return $this->orderValidation($request, 'offline');
        }


        $txnId = 'txn_' . \Str::random(8) . time();
        $chargeId = 'ch_' . \Str::random(9) . time();
        $order = $this->saveOrder($request, $txnId, $chargeId, 'Pending', 'offline');
        $order_id = $order->id;

        $this->saveOrderedItems($order_id);


        // Send Mail to Buyer
        $this->sendMails($order);

        return redirect($success_url);
    }
}

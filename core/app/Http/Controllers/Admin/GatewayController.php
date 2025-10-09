<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Language;
use App\OfflineGateway;
use App\PaymentGateway;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class GatewayController extends Controller
{
    public function index()
    {
        $data['paypal'] = PaymentGateway::where('keyword', 'paypal')->first();
        $data['stripe'] = PaymentGateway::where('keyword', 'stripe')->first();
        $data['paystack'] = PaymentGateway::where('keyword', 'paystack')->first();
        $data['paytm'] = PaymentGateway::where('keyword', 'paytm')->first();
        $data['flutterwave'] = PaymentGateway::where('keyword', 'flutterwave')->first();
        $data['instamojo'] = PaymentGateway::where('keyword', 'instamojo')->first();
        $data['mollie'] = PaymentGateway::where('keyword', 'mollie')->first();
        $data['razorpay'] = PaymentGateway::where('keyword', 'razorpay')->first();
        $data['mercadopago'] = PaymentGateway::where('keyword', 'mercadopago')->first();
        $data['payumoney'] = PaymentGateway::where('keyword', 'payumoney')->first();
        $data['phonepe'] = PaymentGateway::where('keyword', 'phonepe')->first();
        $data['perfect_money'] = PaymentGateway::where('keyword', 'perfect_money')->first();
        $data['xendit'] = PaymentGateway::where('keyword', 'xendit')->first();
        $data['myfatoorah'] = PaymentGateway::where('keyword', 'myfatoorah')->first();
        $data['yoco'] = PaymentGateway::where('keyword', 'yoco')->first();
        $data['toyyibpay'] = PaymentGateway::where('keyword', 'toyyibpay')->first();
        $data['paytabs'] = PaymentGateway::where('keyword', 'paytabs')->first();
        $data['iyzico'] = PaymentGateway::where('keyword', 'iyzico')->first();
        $data['midtrans'] = PaymentGateway::where('keyword', 'midtrans')->first();

        return view('admin.gateways.index', $data);
    }

    public function paypalUpdate(Request $request)
    {
        $paypal = PaymentGateway::where('keyword', 'paypal')->first();
        $paypal->status = $request->status;

        $information = [];
        $information['client_id'] = $request->client_id;
        $information['client_secret'] = $request->client_secret;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your PayPal account.";

        $paypal->information = json_encode($information);

        $paypal->save();

        $request->session()->flash('success', "Paypal informations updated successfully!");

        return back();
    }

    public function stripeUpdate(Request $request)
    {
        $stripe = PaymentGateway::where('keyword', 'stripe')->first();
        $stripe->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['secret'] = $request->secret;
        $information['text'] = "Pay via your Credit account.";

        $stripe->information = json_encode($information);

        $stripe->save();

        $request->session()->flash('success', "Stripe informations updated successfully!");

        return back();
    }

    public function paystackUpdate(Request $request)
    {
        $paystack = PaymentGateway::where('keyword', 'paystack')->first();
        $paystack->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['secret_key'] = $request->secret_key;
        $information['email'] = $request->email;
        $information['text'] = "Pay via your Paystack account.";

        $paystack->information = json_encode($information);

        $paystack->save();

        $request->session()->flash('success', "Paystack informations updated successfully!");

        return back();
    }

    public function paytmUpdate(Request $request)
    {
        $paytm = PaymentGateway::where('keyword', 'paytm')->first();
        $paytm->status = $request->status;

        $information = [];
        $information['merchant'] = $request->merchant;
        $information['secret'] = $request->secret;
        $information['website'] = $request->website;
        $information['industry'] = $request->industry;
        $information['text'] = "Pay via your paytm account.";

        $paytm->information = json_encode($information);

        $paytm->save();

        $request->session()->flash('success', "Paytm informations updated successfully!");

        return back();
    }

    public function flutterwaveUpdate(Request $request)
    {
        $flutterwave = PaymentGateway::where('keyword', 'flutterwave')->first();
        $flutterwave->status = $request->status;

        $information = [];
        $information['public_key'] = $request->public_key;
        $information['secret_key'] = $request->secret_key;
        $information['text'] = "Pay via your Flutterwave account.";

        $flutterwave->information = json_encode($information);

        $flutterwave->save();

        $request->session()->flash('success', "Flutterwave informations updated successfully!");

        return back();
    }

    public function instamojoUpdate(Request $request)
    {
        $instamojo = PaymentGateway::where('keyword', 'instamojo')->first();
        $instamojo->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['token'] = $request->token;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your Instamojo account.";

        $instamojo->information = json_encode($information);

        $instamojo->save();

        $request->session()->flash('success', "Instamojo informations updated successfully!");

        return back();
    }

    public function mollieUpdate(Request $request)
    {
        $mollie = PaymentGateway::where('keyword', 'mollie')->first();
        $mollie->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['text'] = "Pay via your Mollie Payment account.";

        $mollie->information = json_encode($information);

        $mollie->save();

        $arr = ['MOLLIE_KEY' => $request->key];
        setEnvironmentValue($arr);
        \Artisan::call('config:clear');

        $request->session()->flash('success', "Mollie Payment informations updated successfully!");

        return back();
    }

    public function razorpayUpdate(Request $request)
    {
        $razorpay = PaymentGateway::where('keyword', 'razorpay')->first();
        $razorpay->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['secret'] = $request->secret;
        $information['text'] = "Pay via your Razorpay account.";

        $razorpay->information = json_encode($information);

        $razorpay->save();

        $request->session()->flash('success', "Razorpay informations updated successfully!");

        return back();
    }

    public function mercadopagoUpdate(Request $request)
    {
        $mercadopago = PaymentGateway::where('keyword', 'mercadopago')->first();
        $mercadopago->status = $request->status;

        $information = [];
        $information['token'] = $request->token;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your Mercado Pago account.";

        $mercadopago->information = json_encode($information);

        $mercadopago->save();

        $request->session()->flash('success', "Mercado Pago informations updated successfully!");

        return back();
    }

    public function payumoneyUpdate(Request $request)
    {
        $payumoney = PaymentGateway::where('keyword', 'payumoney')->first();
        $payumoney = PaymentGateway::where('keyword', 'payumoney')->first();
        $payumoney->status = $request->status;

        $information = [];
        $information['key'] = $request->key;
        $information['salt'] = $request->salt;
        $information['text'] = "Pay via your PayUmoney account.";
        $information['sandbox_check'] = $request->sandbox_check;

        $payumoney->information = json_encode($information);

        $payumoney->save();

        $arr = ['INDIPAY_MERCHANT_KEY' => $request->key, 'INDIPAY_SALT' => $request->salt];
        setEnvironmentValue($arr);
        \Artisan::call('config:clear');

        $request->session()->flash('success', "PayUmoney informations updated successfully!");

        return back();
    }

    public function yocoUpdate(Request $request)
    {
        $yoco = PaymentGateway::where('keyword', 'yoco')->first();
        $yoco->status = $request->status;

        $information = [];
        $information['secret_key'] = $request->secret_key;
        $yoco->information = json_encode($information);

        $yoco->save();
        $request->session()->flash('success', "Yoco informations updated successfully!");
        return back();
    }

    public function xenditUpdate(Request $request)
    {
        $xendit = PaymentGateway::where('keyword', 'xendit')->first();
        $xendit->status = $request->status;

        $information = [];
        $information['secret_key'] = $request->secret_key;
        $xendit->information = json_encode($information);

        $xendit->save();
        $array = [
            'XENDIT_SECRET_KEY' => $request->secret_key
        ];

        setEnvironmentValue($array);
        Artisan::call('config:clear');

        $request->session()->flash('success', "Xendit informations updated successfully!");
        return back();
    }

    public function perfect_moneyUpdate(Request $request)
    {
        $perfect_money = PaymentGateway::where('keyword', 'perfect_money')->first();
        $perfect_money->status = $request->status;

        $information = [];
        $information['perfect_money_wallet_id'] = $request->perfect_money_wallet_id;
        $perfect_money->information = json_encode($information);

        $perfect_money->save();
        $request->session()->flash('success', "Perfect Money informations updated successfully!");
        return back();
    }

    public function midtransUpdate(Request $request)
    {
        $information['is_production'] = $request->is_production;
        $information['server_key'] = $request->server_key;

        $data = PaymentGateway::where('keyword', 'midtrans')->first();

        $data->update([
            'information' => json_encode($information),
            'status' => $request->status
        ]);

        Session::flash('success', 'Updated Midtrans Information Successfully');

        return redirect()->back();
    }

    public function myfatoorahUpdate(Request $request)
    {
        $information = [
            'token' => $request->token,
            'sandbox_status' => $request->sandbox_status
        ];

        $data = PaymentGateway::where('keyword', 'myfatoorah')->first();

        $data->update([
            'information' => json_encode($information),
            'status' => $request->status
        ]);

        $array = [
            'MYFATOORAH_TOKEN' => $request->token,
            'MYFATOORAH_CALLBACK_URL' => route('myfatoorah.success'),
            'MYFATOORAH_ERROR_URL' => route('myfatoorah.cancel'),
        ];

        setEnvironmentValue($array);
        Artisan::call('config:clear');

        Session::flash('success', 'Updated Myfatoorah Information Successfully');
        return back();
    }

    public function iyzicoUpdate(Request $request)
    {
        $information['sandbox_status'] = $request->sandbox_status;
        $information['api_key'] = $request->api_key;
        $information['secret_key'] = $request->secret_key;

        $data = PaymentGateway::where('keyword', 'iyzico')->first();

        $data->update([
            'information' => json_encode($information),
            'status' => $request->status
        ]);

        Session::flash('success', 'Updated Iyzico Information Successfully');

        return redirect()->back();
    }

    public function toyyibpayUpdate(Request $request)
    {
        $information['sandbox_status'] = $request->sandbox_status;
        $information['secret_key'] = $request->secret_key;
        $information['category_code'] = $request->category_code;

        $data = PaymentGateway::where('keyword', 'toyyibpay')->first();
        $data->update([
            'information' => json_encode($information),
            'status' => $request->status
        ]);

        Session::flash('success', 'Updated Toyyibpay Information Successfully');
        return redirect()->back();
    }

    public function paytabsUpdate(Request $request)
    {
        $information['server_key'] = $request->server_key;
        $information['profile_id'] = $request->profile_id;
        $information['country'] = $request->country;
        $information['api_endpoint'] = $request->api_endpoint;

        $data = PaymentGateway::where('keyword', 'paytabs')->first();

        $data->update([
            'information' => json_encode($information),
            'status' => $request->status
        ]);

        Session::flash('success', 'Updated Paytabs Information Successfully');

        return redirect()->back();
    }

    public function phonepeUpdate(Request $request)
    {
        $phonepe = PaymentGateway::where('keyword', 'phonepe')->first();
        $phonepe->status = $request->status;

        $information = [];
        $information['merchant_id'] = $request->merchant_id;
        $information['salt_key'] = $request->salt_key;
        $information['salt_index'] = $request->salt_index;
        $information['sandbox_check'] = $request->sandbox_check;
        $information['text'] = "Pay via your PhonePe account.";

        $phonepe->information = json_encode($information);

        $phonepe->save();

        $request->session()->flash('success', "PhonePe informations updated successfully!");

        return back();
    }

    public function offline(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();

        $lang_id = $lang->id;
        $data['ogateways'] = OfflineGateway::where('language_id', $lang_id)->orderBy('id', 'DESC')->paginate(10);
        $data['lang_id'] = $lang_id;

        return view('admin.gateways.offline.index', $data);
    }

    public function store(Request $request)
    {
        $messages = [
            'language_id.required' => 'The language field is required',
        ];

        $rules = [
            'language_id' => 'required',
            'name' => 'required|max:100',
            'short_description' => 'nullable',
            'serial_number' => 'required|integer',
            'is_receipt' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $in = $request->all();
        $in['instructions'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->instructions);

        OfflineGateway::create($in);

        Session::flash('success', 'Gateway added successfully!');
        return "success";
    }

    public function update(Request $request)
    {

        $rules = [
            'name' => 'required|max:100',
            'short_description' => 'nullable',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $in = $request->except('_token', 'ogateway_id');
        $in['instructions'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->instructions);

        OfflineGateway::where('id', $request->ogateway_id)->update($in);

        Session::flash('success', 'Gateway updated successfully!');
        return "success";
    }

    public function status(Request $request)
    {
        $og = OfflineGateway::find($request->ogateway_id);
        if (!empty($request->type) && $request->type == 'product') {
            $og->product_checkout_status = $request->product_checkout_status;
        } elseif (!empty($request->type) && $request->type == 'package') {
            $og->package_order_status = $request->package_order_status;
        } elseif (!empty($request->type) && $request->type == 'course') {
            $og->course_checkout_status = $request->course_checkout_status;
        } elseif (!empty($request->type) && $request->type == 'donation') {
            $og->donation_checkout_status = $request->donation_checkout_status;
        } elseif (!empty($request->type) && $request->type == 'event') {
            $og->event_checkout_status = $request->event_checkout_status;
        }
        $og->save();

        Session::flash('success', 'Gateway status changed successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $ogateway = OfflineGateway::findOrFail($request->ogateway_id);
        $ogateway->delete();

        Session::flash('success', 'Gateway deleted successfully!');
        return back();
    }
}

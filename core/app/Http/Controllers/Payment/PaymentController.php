<?php

namespace App\Http\Controllers\Payment;

use App\BasicExtra;
use App\BasicSetting;
use App\Http\Controllers\Controller;
use App\Http\Helpers\KreativMailer;
use PHPMailer\PHPMailer\PHPMailer;
use App\OfflineGateway;
use App\Package;
use App\PackageOrder;
use App\PaymentGateway;
use App\Subscription;
use Auth;
use Carbon\Carbon;
use PDF;

class PaymentController extends Controller
{
    public function paycancle($packageid)
    {
        return redirect()->route('front.packageorder.index', $packageid)->with('error', __('Payment Cancelled.'));
    }

    public function orderValidation($request, $package_inputs, $gtype = 'online')
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'package_id' => 'required',
            'phone_number' => $request['method'] == 'Iyzico' ? 'required' : '',
            'identity_number' => $request['method'] == 'Iyzico' ? 'required' : '',
            'city' => $request['method'] == 'Iyzico' ? 'required' : '',
            'country' => $request['method'] == 'Iyzico' ? 'required' : '',
            'address' => $request['method'] == 'Iyzico' ? 'required' : '',
            'zip_code' => $request['method'] == 'Iyzico' ? 'required' : '',
        ];

        if ($gtype == 'offline') {
            $gateway = OfflineGateway::find($request['method']);

            if ($gateway->is_receipt == 1) {
                $rules['receipt'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $ext = $request->file('receipt')->getClientOriginalExtension();
                        if (!in_array($ext, array('jpg', 'png', 'jpeg'))) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    },
                ];
            }
        }

        $allowedExts = array('zip');
        foreach ($package_inputs as $input) {
            if ($input->required == 1) {
                $rules["$input->name"][] = 'required';
            }
            // check if input type is 5, then check for zip extension
            if ($input->type == 5) {
                $rules["$input->name"][] = function ($attribute, $value, $fail) use ($request, $input, $allowedExts) {
                    if ($request->hasFile("$input->name")) {
                        $ext = $request->file("$input->name")->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only zip file is allowed");
                        }
                    }
                };
            }
        }

        $conline  = PaymentGateway::whereStatus(1)->whereType('automatic')->count();
        $coffline  = OfflineGateway::wherePackageOrderStatus(1)->count();
        if ($conline + $coffline > 0) {
            $rules["method"] = 'required';
        }

        $request->validate($rules);
    }

    public function saveOrder($request, $package_inputs, $paymentStatus, $gtype = 'online')
    {

        $fields = [];
        foreach ($package_inputs as $key => $input) {
            $in_name = $input->name;
            // if the input is file, then move it to 'files' folder
            if ($input->type == 5) {
                if ($request->hasFile("$in_name")) {
                    $fileName = uniqid() . '.' . $request->file("$in_name")->getClientOriginalExtension();
                    $directory = 'assets/front/files/';
                    @mkdir($directory, 0775, true);
                    $request->file("$in_name")->move($directory, $fileName);

                    $fields["$in_name"]['value'] = $fileName;
                    $fields["$in_name"]['type'] = $input->type;
                }
            } else {
                if ($request["$in_name"]) {
                    $fields["$in_name"]['value'] = $request["$in_name"];
                    $fields["$in_name"]['type'] = $input->type;
                }
            }
        }
        $jsonfields = json_encode($fields);
        $jsonfields = str_replace("\/", "/", $jsonfields);

        $bex = BasicExtra::first();
        $package = Package::findOrFail($request["package_id"]);


        if ($bex->recurring_billing == 1) {
            $sub = Subscription::where('user_id', Auth::user()->id);
            $activeSub = Subscription::where('user_id', Auth::user()->id)->where('status', 1);

            if ($sub->count() > 0) {
                $sub = $sub->first();
            } else {
                $sub = new Subscription;
            }
            $sub->name = $request->name;
            $sub->email = $request->email;
            $sub->user_id = Auth::check() ? Auth::user()->id : NULL;

            $sub->fields = $jsonfields;
            $sub->gateway_type = $gtype;

            if ($gtype == 'online') {
                $method = $request['method'];
            } elseif ($gtype == 'offline') {
                $gt = OfflineGateway::findOrFail($request['method']);
                $method = $gt->name;

                // unlink prvious receipt for this subscription (if any)
                @unlink('assets/front/receipt/' . $sub->receipt);

                if ($request->hasFile('receipt')) {
                    // store the receipt in folder & database
                    $receipt = uniqid() . '.' . $request->file('receipt')->getClientOriginalExtension();
                    $request->file('receipt')->move('assets/front/receipt/', $receipt);
                    $sub->receipt = $receipt;
                } else {
                    $sub->receipt = NULL;
                }

                $sub->pending_package_id = $package->id;
                $sub->pending_package_id = $package->id;
            }
            if ($activeSub->count() == 0 && $gtype == 'online') {
                $sub->current_payment_method = $method;
            } elseif ($activeSub->count() > 0 && $gtype == 'online') {
                $sub->next_payment_method = $method;
            } elseif ($gtype == 'offline') {
                $sub->pending_payment_method = $method;
            }

            $sub->conversation_id = $request->conversation_id;
            $sub->save();

            // if payment completed, then
            if ($paymentStatus == 1) {
                return $this->subFinalize($sub, $package);
            } else {
                return $sub;
            }
        } else {

            $in['name'] = $request["name"];
            $in['email'] = $request["email"];
            $in['fields'] = $jsonfields;



            $in['user_id'] = Auth::check() ? Auth::user()->id : NULL;
            $in['package_id'] = $package->id;
            $in['package_title'] = $package->title;
            $in['package_price'] = $package->price;
            $in['package_description'] = $package->description;
            if ($gtype == 'online') {
                $in['method'] = $request['method'];
            } elseif ($gtype == 'offline') {
                $gt = OfflineGateway::findOrFail($request['method']);
                $in['method'] = $gt->name;

                if ($request->hasFile('receipt')) {
                    $receipt = uniqid() . '.' . $request->file('receipt')->getClientOriginalExtension();
                    $request->file('receipt')->move('assets/front/receipt/', $receipt);
                    $in['receipt'] = $receipt;
                }
            }
            $in['payment_status'] = $paymentStatus;
            $in['gateway_type'] = $gtype;
            $po = PackageOrder::create($in);


            $po->order_number = $po->id + 1000000000;
            $po->conversation_id = $request->conversation_id;
            $po->save();

            return $po;
        }
    }


    public function subFinalize($sub, $package)
    {
        $activeSub = Subscription::where('user_id', Auth::user()->id)->where('status', 1);

        if ($package->duration == 'monthly') {
            $days = 30;
        } elseif ($package->duration == 'yearly') {
            $days = 365;
        }

        // if active subscription doest not exist for this user
        if ($activeSub->count() == 0) {
            $sub->current_package_id = $package->id;
            $sub->next_package_id = NULL;
            $sub->expire_date = Carbon::now()->addDays($days);
            $sub->status = 1; // make the subscription active
        } else {
            $sub->next_package_id = $package->id;
        }

        $sub->save();

        return $sub;
    }



    public function sendMails($po, $be, $bex)
    {
        $bs = BasicSetting::first();
        if ($bex->recurring_billing == 1) {
            $po = Subscription::findOrFail($po->id);
        }
        // saving invoice in DB
        $fileName = \Str::random(4) . time() . '.pdf';
        $po->invoice = $fileName;
        $po->save();

        // sending datas to view to make invoice PDF
        $fields = json_decode($po->fields, true);
        $data['packageOrder'] = $po;
        $data['fields'] = $fields;

        if ($bex->recurring_billing == 1) {
            // if online gateway
            if ($po->gateway_type == 'online') {
                // if the subscription was already active,
                if (!empty($po->next_package_id)) {
                    $package = $po->next_package;
                    if ($package->duration == 'monthly') {
                        $days = 30;
                    } else {
                        $days = 365;
                    }
                    $activationDate = Carbon::parse($po->expire_date);
                    $expireDate = Carbon::parse($po->expire_date)->addDays($days);
                } else {
                    $package = $po->current_package;
                    if ($package->duration == 'monthly') {
                        $days = 30;
                    } else {
                        $days = 365;
                    }
                    $activationDate = Carbon::now();
                    $expireDate = Carbon::now()->addDays($days);
                }
            }
            // if offline gateway
            else {
                $package = $po->pending_package;
                $activationDate = "Activation Date will be notified via mail once Admin accepts the subscription request";
                $expireDate = "Expire Date will be notified via mail once Admin accepts the subscription request";
            }

            $data['package'] = $package;
            $data['activationDate'] = $activationDate;
            $data['expireDate'] = $expireDate;

            // generate pdf from view using dynamic datas
            PDF::loadView('pdf.subscription', $data)->save('assets/front/invoices/' . $fileName);

            // send mail to Buyer
            $mailer = new KreativMailer;
            $data = [
                'toMail' => $po->email,
                'toName' => $po->billing_fname,
                'attachment' => $fileName,
                'customer_name' => $po->billing_fname,
                'package_name' => $package->title,
                'website_title' => $bs->website_title,
                'activation_date' => $po->gateway_type == 'online' ? $activationDate->toFormattedDateString() : $activationDate,
                'expire_date' => $po->gateway_type == 'online' ? $expireDate->toFormattedDateString() : $expireDate,
                'templateType' => 'package_subscription',
                'type' => 'packageSubscription'
            ];

            $mailer->mailFromAdmin($data);
        } else {
            // generate pdf from view using dynamic datas
            PDF::loadView('pdf.package', $data)->save('assets/front/invoices/' . $fileName);

            // send mail to Buyer
            $mailer = new KreativMailer;
            $data = [
                'toMail' => $po->email,
                'toName' => $po->name,
                'attachment' => $fileName,
                'customer_name' => $po->name,
                'package_name' => $po->package_title,
                'order_number' => $po->order_number,
                'order_link' => !empty($po->user_id) ? "<strong>Order Details:</strong> <a href='" . route('user-package-order-details', $po->id) . "'>" . route('user-package-order-details', $po->id) . "</a>" : "",
                'website_title' => $bs->website_title,
                'templateType' => 'package_order',
                'type' => 'packageOrder'
            ];

            $mailer->mailFromAdmin($data);
        }

        // send mail to Admin
        try {

            $mail = new PHPMailer(true);
            $mail->setFrom($po->email, $po->name);
            $mail->addAddress($be->from_mail);     // Add a recipient

            // Attachments
            $mail->addAttachment('assets/front/invoices/' . $fileName);         // Add attachments

            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = "Order placed for " . $po->package_title;
            $mail->Body    = 'A new order has been placed.<br/><strong>Order Number: </strong>' . $po->order_number;

            $mail->send();
        } catch (\Exception $e) {
            // die($e->getMessage());
        }

        if ($bex->recurring_billing == 1) {
            @unlink('assets/front/invoices/' . $fileName);
        }
    }
}

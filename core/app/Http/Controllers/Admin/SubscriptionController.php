<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtended;
use App\Exports\SubscriptionExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OfflineGateway;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Package;
use App\PaymentGateway;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class SubscriptionController extends Controller
{
    public function subscriptions(Request $request)
    {
        $data['packages'] = Package::all();

        $type = $request->type;
        $term = $request->term;
        $package = $request->package;

        $subscriptions = Subscription::when($type, function($query, $type) {
            if ($type == 'all') {
                return $query->where('status', '<>', 3);
            } elseif ($type == 'active') {
                return $query->where('status', 1);
            } elseif ($type == 'expired') {
                return $query->where('status', 0);
            } elseif ($type == 'request') {
                return $query->whereNotNull('pending_package_id');
            }
        })->when($term, function($query, $term) {
            $query->where('name', 'like', '%' . $term . '%');
        })->when($package, function($query, $package) {
            $query->where('current_package_id', $package);
        })->orderBy('id', 'DESC')->paginate(10);

        $data['subscriptions'] = $subscriptions;
        return view('admin.package.subscriptions', $data);
    }

    public function subDelete(Request $request) {
        $sub = Subscription::findOrFail($request->subscription_id);
        $sub->delete();

        $request->session()->flash('success', 'Subscription deleted successfully');
        return back();
    }

    public function status(Request $request) {
        $sub = Subscription::findOrFail($request->subscription_id);
        $be = BasicExtended::first();
        $pendingPackage = $sub->pending_package->title;

        // if accepted
        if ($request->status == 'accept') {



            // if active subscription does not exist
            if ($sub->status != 1) {
                // current package will be pending package
                $sub->current_package_id = $sub->pending_package_id;
                $sub->current_payment_method = $sub->pending_payment_method;
                $sub->pending_package_id = NULL;
                $sub->pending_payment_method = NULL;
                $sub->next_package_id = NULL;
                $sub->next_payment_method = NULL;
                $sub->status = 1;
                $sub->save();


                $activationDate = Carbon::now();
                // calc new expire date & save in database
                $duration = $sub->current_package->duration;
                if ($duration == 'monthly') {
                    $days = 30;
                } else {
                    $days = 365;
                }
                $expiryDate = Carbon::now()->addDays($days);
                $sub->expire_date = $expiryDate;
                $sub->save();
            }
            // if active subscription exists
            else {
                // next package will be pending package
                $sub->next_package_id = $sub->pending_package_id;
                $sub->next_payment_method = $sub->pending_payment_method;
                $sub->pending_package_id = NULL;
                $sub->pending_payment_method = NULL;
                $sub->save();

                $activationDate = Carbon::parse($sub->expire_date);
                // calc new expire date & save in database
                $duration = $sub->current_package->duration;
                if ($duration == 'monthly') {
                    $days = 30;
                } else {
                    $days = 365;
                }
                $expiryDate = Carbon::parse($sub->expire_date)->addDays($days);
            }

            // send mail mentioning activation date & expire date
            $subject = "Subscription Request Accepted";
            $body = "Hello <strong>$sub->name</strong>,<br>Your subscription request of <strong>" . $pendingPackage . "</strong> has been accepted.<br><strong>Activation Date:</strong>" . $activationDate->toFormattedDateString() . ".<br><strong>Expire Date:</strong>" . $expiryDate->toFormattedDateString() . ".<br>Thank you.";

        }
        // if rejected
        elseif ($request->status == 'reject') {
            $sub->pending_package_id = NULL;
            $sub->pending_payment_method = NULL;
            $sub->save();

            // send mail notification about rejection
            $subject = "Subscription Request Rejected";
            $body = "Hello <strong>$sub->name</strong>,<br>Your subscription request of <strong>$pendingPackage</strong> has been rejected.<br>Thank you.";
        }

        // unlink previous receipt image
        @unlink('assets/front/receipt/' . $sub->receipt);

         // Send Mail to Buyer
         $mail = new PHPMailer(true);
         if ($be->is_smtp == 1) {
             try {
                 $mail->isSMTP();
                 $mail->Host       = $be->smtp_host;
                 $mail->SMTPAuth   = true;
                 $mail->Username   = $be->smtp_username;
                 $mail->Password   = $be->smtp_password;
                 $mail->SMTPSecure = $be->encryption;
                 $mail->Port       = $be->smtp_port;

                 //Recipients
                 $mail->setFrom($be->from_mail, $be->from_name);
                 $mail->addAddress($sub->email, $sub->name);

                 // Content
                 $mail->isHTML(true);
                 $mail->Subject = $subject;
                 $mail->Body    = $body;
                 $mail->send();
             } catch (Exception $e) {
                 // die($e->getMessage());
             }
         } else {
             try {

                 //Recipients
                 $mail->setFrom($be->from_mail, $be->from_name);
                 $mail->addAddress($sub->email, $sub->name);


                 // Content
                 $mail->isHTML(true);
                 $mail->Subject = $subject;
                 $mail->Body    = $body;

                 $mail->send();
             } catch (Exception $e) {
                 // die($e->getMessage());
             }
         }

        $request->session()->flash('success', 'Status updated successfully');
        return back();
    }


    public function mail(Request $request)
    {
        $rules = [
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $be = BasicExtended::first();
        $from = $be->from_mail;

        $sub = $request->subject;
        $msg = $request->message;
        $to = $request->email;

        // Send Mail
        $mail = new PHPMailer(true);

        if ($be->is_smtp == 1) {
            try {
                $mail->isSMTP();
                $mail->Host       = $be->smtp_host;
                $mail->SMTPAuth   = true;
                $mail->Username   = $be->smtp_username;
                $mail->Password   = $be->smtp_password;
                $mail->SMTPSecure = $be->encryption;
                $mail->Port       = $be->smtp_port;

                //Recipients
                $mail->setFrom($from);
                $mail->addAddress($to);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $sub;
                $mail->Body    = $msg;

                $mail->send();
            } catch (Exception $e) {

            }
        } else {
            try {

                //Recipients
                $mail->setFrom($from);
                $mail->addAddress($to);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $sub;
                $mail->Body    = $msg;

                $mail->send();
            } catch (Exception $e) {

            }
        }

        Session::flash('success', 'Mail sent successfully!');
        return "success";
    }

    public function bulkSubDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $sub = Subscription::findOrFail($id);
            @unlink('assets/front/receipt/'.$sub->receipt);
            $sub->delete();
        }

        Session::flash('success', 'Subscription deleted successfully!');
        return "success";
    }
}

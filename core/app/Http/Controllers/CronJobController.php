<?php

namespace App\Http\Controllers;

use App\BasicExtra;
use App\CoursePurchase;
use App\DonationDetail;
use App\EventDetail;
use App\Http\Controllers\Payment\Course\MailController;
use App\PaymentGateway;
use App\Subscription;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\product\PaymentController as ProductPaymentController;
use App\Http\Controllers\Front\CausesController;
use App\Http\Controllers\Front\EventController;
use App\Language;
use App\PackageOrder;
use App\ProductOrder;
use PDF;

class CronJobController extends Controller
{
    public function index()
    {
        //check iyzico payment for subscription
        $subscriptions = Subscription::where([['current_payment_method', 'Iyzico'], ['status', 3]])->get();
        foreach ($subscriptions as $subscription) {
            if (!is_null($subscription->conversation_id)) {
                $result = $this->IyzicoPaymentStatus($subscription->conversation_id);
                if ($result == 'success') {
                    $this->updateIyzicoPendingSubscription($subscription->id);
                }
            }
        }
        //check iyzico payment for package order
        $package_orders = PackageOrder::where([['method', 'Iyzico'], ['payment_status', 0]])->get();
        foreach ($package_orders as $package_order) {
            if (!is_null($package_order->conversation_id)) {
                $result = $this->IyzicoPaymentStatus($package_order->conversation_id);
                if ($result == 'success') {
                    $this->updateIyzicoPendingPackageOrder($package_order->id);
                }
            }
        }
        //check iyzico payment for product order
        $product_orders = ProductOrder::where([['method', 'iyzico'], ['payment_status', 'Pending']])->get();
        foreach ($product_orders as $product_order) {
            if (!is_null($product_order->conversation_id)) {
                $result = $this->IyzicoPaymentStatus($product_order->conversation_id);
                if ($result == 'success') {
                    $this->updateIyzicoPendingProductOrder($product_order->id);
                }
            }
        }
        //check iyzico payment for course enroll
        $course_enrolls = CoursePurchase::where([['payment_method', 'iyzico'], ['payment_status', 'Pending']])->get();
        foreach ($course_enrolls as $course_enroll) {
            if (!is_null($course_enroll->conversation_id)) {
                $result = $this->IyzicoPaymentStatus($course_enroll->conversation_id);
                if ($result == 'success') {
                    $this->updateIyzicoPendingCourse($course_enroll->id);
                }
            }
        }
        //check iyzico payment for donations
        $donations = DonationDetail::where([['payment_method', 'Iyzico'], ['status', 'pending']])->get();
        foreach ($donations as $donation) {
            if (!is_null($donation->conversation_id)) {
                $result = $this->IyzicoPaymentStatus($donation->conversation_id);
                if ($result == 'success') {
                    $this->updateIyzicoPendingDonation($donation->id);
                }
            }
        }

        //check iyzico payment for event booking
        $bookings = EventDetail::where([['payment_method', 'Iyzico'], ['status', 'pending']])->get();
        foreach ($bookings as $booking) {
            if (!is_null($booking->conversation_id)) {
                $result = $this->IyzicoPaymentStatus($booking->conversation_id);
                if ($result == 'success') {
                    $this->updateIyzicoPendingEventBooking($booking->id);
                }
            }
        }
    }

    /*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    ----------- Get iyzico payment status from iyzico server ---------
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
    private function IyzicoPaymentStatus($conversation_id)
    {
        $paymentMethod = PaymentGateway::where('keyword', 'iyzico')->first();
        $paydata = $paymentMethod->convertAutoData();

        $options = new \Iyzipay\Options();
        $options->setApiKey($paydata['api_key']);
        $options->setSecretKey($paydata['secret_key']);
        if ($paydata['sandbox_status'] == 1) {
            $options->setBaseUrl("https://sandbox-api.iyzipay.com");
        } else {
            $options->setBaseUrl("https://api.iyzipay.com"); // production mode
        }

        $request = new \Iyzipay\Request\ReportingPaymentDetailRequest();
        $request->setPaymentConversationId($conversation_id);

        $paymentResponse = \Iyzipay\Model\ReportingPaymentDetail::create($request, $options);
        $result = (array) $paymentResponse;

        foreach ($result as $key => $data) {
            $data = json_decode($data, true);
            if ($data['status'] == 'success' && !empty($data['payments'])) {
                if (is_array($data['payments'])) {
                    if ($data['payments'][0]['paymentStatus'] == 1) {
                        return 'success';
                    } else {
                        return 'not found';
                    }
                } else {
                    return 'not found';
                }
            } else {
                return 'not found';
            }
        }
        return 'not found';
    }

    private function updateIyzicoPendingSubscription($id)
    {
        $currentLang = Language::where('is_default', 1)->first();
        $be = $currentLang->basic_extended;
        $bex = BasicExtra::first();
        $subscription = Subscription::where('id', $id)->first();
        if ($subscription) {
            $subscription->status = 1;
            $subscription->save();
            $payment_controller = new PaymentController();
            $payment_controller->sendMails($subscription, $be, $bex);
        }
        return;
    }
    private function updateIyzicoPendingPackageOrder($id)
    {
        $currentLang = Language::where('is_default', 1)->first();
        $be = $currentLang->basic_extended;
        $bex = BasicExtra::first();
        $po = PackageOrder::where('id', $id)->first();
        if ($po) {
            $po->payment_status = 1; //for cronjob make it pending payment
            $po->save();
            $payment_controller = new PaymentController();
            $payment_controller->sendMails($po, $be, $bex);
        }
        return;
    }

    private function updateIyzicoPendingProductOrder($id)
    {
        $po = ProductOrder::where('id', $id)->first();
        $po->payment_status = "Completed";
        $po->save();

        // Send Mail to Buyer
        $data = new ProductPaymentController();
        $data->sendMails($po);
        return;
    }

    private function updateIyzicoPendingCourse($id)
    {
        $currentLang = Language::where('is_default', 1)->first();

        $bs = $currentLang->basic_setting;
        $logo = $bs->logo;
        $bse = $currentLang->basic_extra;

        $course_purchase = CoursePurchase::where('id', $id)->first();
        $course_purchase->update([
            'payment_status' => 'Completed'
        ]);

        // generate an invoice in pdf format
        $fileName = $course_purchase->order_number . '.pdf';
        $directory = 'assets/front/invoices/course/';
        @mkdir($directory, 0775, true);
        $fileLocated = $directory . $fileName;
        $order_info = $course_purchase;
        PDF::loadView('pdf.course', compact('order_info', 'logo', 'bse'))
            ->setPaper('a4', 'landscape')->save($fileLocated);

        // store invoice in database
        $course_purchase->update([
            'invoice' => $fileName
        ]);

        // send a mail to the buyer
        MailController::sendMail($course_purchase);
        return;
    }

    private function updateIyzicoPendingDonation($id)
    {
        $currentLang = Language::where('is_default', 1)->first();
        $be = $currentLang->basic_extended;

        $donation = DonationDetail::where('id', $id)->first();

        if ($donation) {
            $donation->status = 'Success';
            $donation->save();

            $cause = new CausesController;
            $file_name = $cause->makeInvoice($donation);
            $requestData = [
                'donation_id' => $donation->donation_id,
                'email' => $donation->email,
                'name' => $donation->name,
            ];
            $cause->sendMailPHPMailer($requestData, $file_name, $be);
        }
        return;
    }

    private function updateIyzicoPendingEventBooking($id)
    {
        $currentLang = Language::where('is_default', 1)->first();
        $be = $currentLang->basic_extended;

        $event_details = EventDetail::where('id', $id)->first();
        if ($event_details) {
            $event_details->status = 'Success';
            $event_details->save();

            $event = new EventController;
            $file_name = $event->makeInvoice($event_details);
            $requestData = [
                'event_id' => $event_details->event_id,
                'email' => $event_details->email,
                'name' => $event_details->name,
            ];
            $event->sendMailPHPMailer($requestData, $file_name, $be);
        }
        return;
    }
}

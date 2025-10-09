<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtra;
use App\Donation;
use App\DonationDetail;
use App\Exports\DonationExport;
use App\Http\Requests\Donation\DonationStoreRequest;
use App\Http\Requests\Donation\DonationUpdateRequest;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Megamenu;
use App\OfflineGateway;
use App\PaymentGateway;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Validator;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();
        $lang_id = $lang->id;
        $data['lang_id'] = $lang_id;
        $data['abx'] = $lang->basic_extra;
        $donations = Donation::where('lang_id', $lang_id)->orderBy('id', 'DESC')->get();
        $donations->map(function ($donation) {
            $raised_amount = DonationDetail::query()
                ->where('donation_id', '=', $donation->id)
                ->where('status', '=', "Success")
                ->sum('amount');
            $donation['raised_amount'] = $raised_amount > 0 ? round($raised_amount, 2) : 0;
        });
        $data['donations'] = $donations;
        return view('admin.donation.donation.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $slug = make_slug($request->title);

        $image = $request->image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $messages = [
            'title.required' => 'The title field is required',
            'goal_amount.required' => 'The goal amount field is required',
            'min_amount.required' => 'The minimum amount field is required',
            'lang_id.required' => 'The language field is required',
            'image.required' => 'The image field is required',
        ];

        $rules = [
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug) {
                    $causes = Donation::all();
                    foreach ($causes as $key => $cause) {
                        if (strtolower($slug) == strtolower($cause->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'goal_amount' => 'required',
            'min_amount' => 'required',
            'lang_id' => 'required',
            'image' => 'required',
        ];
        if ($request->filled('image')) {
            $rules['image'] = [
                function ($attribute, $value, $fail) use ($extImage, $allowedExts) {
                    if (!in_array($extImage, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                }
            ];
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $in = $request->all();

        if ($request->filled('image')) {
            $filename = uniqid() .'.'. $extImage;
            $directory = "assets/front/img/donations/";
            @mkdir($directory, 0775, true);
            @copy($image, $directory . $filename);
            $in["image"] = $filename;
        }
        $in['slug'] = $slug;
        $in['content'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);

        Donation::create($in);

        Session::flash('success', 'Donation added successfully!');
        return "success";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return
     */
    public function edit($id)
    {
        $data['donation'] = Donation::findOrFail($id);
        $data['abx'] = BasicExtra::select('base_currency_text')->where('language_id', $data['donation']->lang_id)->first();
        return view('admin.donation.donation.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return
     */
    public function update(Request $request)
    {
        $slug = make_slug($request->title);
        $causeId = $request->donation_id;

        $image = $request->image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $messages = [
            'title.required' => 'The title field is required',
            'goal_amount.required' => 'The goal amount field is required',
            'min_amount.required' => 'The minimum amount field is required',
        ];

        $rules = [
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug, $causeId) {
                    $causes = Donation::all();
                    foreach ($causes as $key => $cause) {
                        if ($cause->id != $causeId && strtolower($slug) == strtolower($cause->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'goal_amount' => 'required',
            'min_amount' => 'required',
        ];

        if ($request->filled('image')) {
            $rules['image'] = [
                function ($attribute, $value, $fail) use ($extImage, $allowedExts) {
                    if (!in_array($extImage, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                }
            ];
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $donation = Donation::find($request->donation_id);

        $in = $request->all();
        $in["slug"] = $slug;
        $in["content"] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);

        if ($request->filled('image')) {
            @unlink('assets/front/img/donations/' . $donation->image);
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/donations/' . $filename);
        } else {
            $filename = $donation->image;
        }
        $in["image"] = $filename;

        $donation->fill($in)->save();
        Session::flash('success', 'Donation updated successfully!');
        return "success";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function deleteFromMegaMenu($cause) {
        $megamenu = Megamenu::where('language_id', $cause->lang_id)->where('category', 0)->where('type', 'causes');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            if (is_array($menus)) {
                if (in_array($cause->id, $menus)) {
                    $index = array_search($cause->id, $menus);
                    unset($menus["$index"]);
                    $menus = array_values($menus);
                    $megamenu->menus = json_encode($menus);
                    $megamenu->save();
                }
            }
        }
    }


    public function delete(Request $request)
    {
        $donation_details = DonationDetail::query()->where('donation_id',$request->donation_id)->get();
        foreach ($donation_details as $donation_detail){
            if(!is_null($donation_detail->receipt)){
                $directory = "assets/front/img/donations/receipt/".$donation_detail->receipt;
                if (file_exists($directory)) {
                    @unlink($directory);
                }
            }
            $donation_detail->delete();
        }
        $donation = Donation::findOrFail($request->donation_id);
        if(!is_null($donation->image)){
            $directory = "assets/front/img/donations/".$donation->image;
            if (file_exists($directory)) {
                @unlink($directory);
            }
        }

        $this->deleteFromMegaMenu($donation);

        $donation->delete();
        Session::flash('success', 'Donation deleted successfully!');
        return back();
    }
    public function paymentDelete(Request $request)
    {
        $donation_detail = DonationDetail::findOrFail($request->payment_id);
        if(!is_null($donation_detail->receipt)){
            $directory = "assets/front/img/donations/receipt/".$donation_detail->receipt;
            if (file_exists($directory)) {
                @unlink($directory);
            }
        }
        $donation_detail->delete();
        Session::flash('success', 'Payment deleted successfully!');
        return back();
    }
    public function bulkPaymentDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $donation_detail = DonationDetail::findOrFail($id);
            if(!is_null($donation_detail->receipt)){
                $directory = "assets/front/img/donations/receipt/".$donation_detail->receipt;
                if (file_exists($directory)) {
                    @unlink($directory);
                }
            }
            $donation_detail->delete();
        }

        Session::flash('success', 'Donations deleted successfully!');
        return "success";
    }

    // public function uploadUpdate(Request $request,$id){
    //     $rules = [
    //         'file' => 'required | mimes:jpeg,jpg,png',
    //     ];
    //     $validator = Validator::make($request->all(), $rules);
    //     if ($validator->fails()) {
    //         $validator->getMessageBag()->add('error', 'true');
    //         return response()->json(['errors' => $validator->errors(), 'id' => 'blog']);
    //     }
    //     $img = $request->file('file');
    //     $donation = Donation::findOrFail($id);
    //     if ($request->hasFile('file')) {
    //         $filename = time() . '.' . $img->getClientOriginalExtension();
    //         $request->file('file')->move('assets/front/img/donations/', $filename);
    //         @unlink('assets/front/img/donations/' . $donation->image);
    //         $donation->image = $filename;
    //         $donation->save();
    //     }
    //     return response()->json(['status' => "success", "image" => "Donation Image", 'donation' => $donation]);
    // }
    public function bulkDelete(Request $request)
    {
        return DB::transaction(function() use ($request){
            $ids = $request->ids;
            foreach ($ids as $id) {
                $donation_details = DonationDetail::query()->where('donation_id',$id)->get();
                foreach ($donation_details as $donation_detail){
                    if(!is_null($donation_detail->receipt)){
                        $directory = "assets/front/img/donations/receipt/".$donation_detail->receipt;
                        if (file_exists($directory)) {
                            @unlink($directory);
                        }
                    }
                    $donation_detail->delete();
                }
                $donation = Donation::findOrFail($id);
                if(!is_null($donation->image)){
                    $directory = "assets/front/img/donations/".$donation->image;
                    if (file_exists($directory)) {
                        @unlink($directory);
                    }
                }

                $this->deleteFromMegaMenu($donation);

                $donation->delete();
            }
            Session::flash('success', 'Donation deleted successfully!');
            return "success";
        });
    }

    public function paymentLog(Request $request){
        $search = $request->search;
        $data['donations'] = DonationDetail::when($search, function ($query, $search) {
                                                return $query->where('transaction_id', $search);
                                            })
                                            ->orderBy('id', 'DESC')
                                            ->paginate(10);
        return view('admin.donation.payment.index', $data);
    }
    public function paymentLogUpdate(Request $request){
        $currentLang = session()->has('lang') ?
            (Language::where('code', session()->get('lang'))->first())
            : (Language::where('is_default', 1)->first());
        $be = $currentLang->basic_extended;
        if($request->status == "success"){
            DonationDetail::query()
                ->findOrFail($request->id)
                ->update(['status' => 'Success']);
            $donation = DonationDetail::query()->findOrFail($request->id);
            $fileName = $this->makeInvoice($donation);
            $this->sendMailPHPMailer($donation->name,$donation->email,$fileName,$be);
            Session::flash('success', 'Donation payment updated successfully!');
        }elseif ($request->status == "rejected"){
            DonationDetail::query()
                ->findOrFail($request->id)
                ->update(['status' => 'Rejected']);
            Session::flash('success', 'Donation payment rejected successfully!');
        }else{
            DonationDetail::query()
                ->findOrFail($request->id)
                ->update(['status' => 'Pending']);
            Session::flash('success', 'Donation payment to pending successfully!');
        }

        $donation_detail = DonationDetail::findOrFail($request->id);
        $sub = "Donation Status Changed";
        if (!empty($donation_detail->email) && $donation_detail->email != 'anoymous') {
            // Send Mail to Donor
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
                    $mail->addAddress($donation_detail->email, $donation_detail->name);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = $sub;
                    $mail->Body    = 'Hello <strong>' . $donation_detail->name . '</strong>,<br/><br>Your donation status of <strong>' . $donation_detail->cause->title . '</strong> is <strong>'.ucfirst($request->status).'</strong>.<br/><br>Thank you.';
                    $mail->send();
                } catch (Exception $e) {
                    // die($e->getMessage());
                }
            } else {
                try {

                    //Recipients
                    $mail->setFrom($be->from_mail, $be->from_name);
                    $mail->addAddress($donation_detail->email, $donation_detail->name);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = $sub ;
                    $mail->Body    = 'Hello <strong>' . $donation_detail->name . '</strong>,<br/>Your order status is <strong>'.ucfirst($request->status).'</strong>.<br/>Thank you.';
                    $mail->send();
                } catch (Exception $e) {
                    // die($e->getMessage());
                }
            }
        }
      return redirect()->back();
    }
    public function makeInvoice($donation){
        $file_name = "Donation#".$donation->transaction_id.".pdf";
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('pdf.donation', compact('donation'));
        $output = $pdf->output();
        file_put_contents('assets/front/invoices/'.$file_name, $output);
        return $file_name;
    }

    public function sendMailPHPMailer($name,$email,$file_name,$be){
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
                $mail->setFrom($be->from_mail, $be->from_name);
                $mail->addAddress($email, $name);
                $mail->addAttachment('assets/front/invoices/' . $file_name);
                $mail->isHTML(true);
                $mail->Subject = "You made your donation successful";
                $mail->Body = "You made a donation. This is a confirmation mail from us. Please see the attachment for details. Thank you";
                $mail->send();
                @unlink('assets/front/invoices/'.$file_name);
            } catch (Exception $e) {
                dd($e->getMessage());
            }
        } else {
            try {
                $mail->setFrom($be->from_mail, $be->from_name);
                $mail->addAddress($email, $name);
                $mail->addAttachment('assets/front/invoices/' . $file_name);
                $mail->isHTML(true);
                $mail->Subject = "You made your donation successful";
                $mail->Body = "You made a donation. This is a confirmation mail from us. Please see the attachment for details. Thank you";
                $mail->send();
                @unlink('assets/front/invoices/'.$file_name);
            } catch (Exception $e) {
                dd($e->getMessage());
            }
        }
    }

    public function settings() {
        $data['abex'] = BasicExtra::first();
        return view('admin.donation.settings', $data);
    }

    public function updateSettings(Request $request) {
        $bexs = BasicExtra::all();
        foreach($bexs as $bex) {
            $bex->donation_guest_checkout = $request->donation_guest_checkout;
            $bex->is_donation = $request->is_donation;
            $bex->save();
        }

        $request->session()->flash('success', 'Settings updated successfully!');
        return back();
    }

    public function report(Request $request) {
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $paymentStatus = $request->payment_status;
        $paymentMethod = $request->payment_method;

        if (!empty($fromDate) && !empty($toDate)) {
            $donations = DonationDetail::when($fromDate, function ($query, $fromDate) {
                return $query->whereDate('created_at', '>=', Carbon::parse($fromDate));
            })->when($toDate, function ($query, $toDate) {
                return $query->whereDate('created_at', '<=', Carbon::parse($toDate));
            })->when($paymentMethod, function ($query, $paymentMethod) {
                return $query->where('payment_method', $paymentMethod);
            })->when($paymentStatus, function ($query, $paymentStatus) {
                return $query->where('status', $paymentStatus);
            })->select('transaction_id','donation_id','name','email','phone','amount','payment_method','status','created_at')->orderBy('id', 'DESC');
            Session::put('donation_report', $donations->get());
            $data['donations'] = $donations->paginate(10);
        } else {
            Session::put('donation_report', []);
            $data['donations'] = [];
        }

        $data['onPms'] = PaymentGateway::where('status', 1)->get();
        $data['offPms'] = OfflineGateway::where('donation_checkout_status', 1)->get();


        return view('admin.donation.report', $data);
    }

    public function exportReport() {
        $donations = Session::get('donation_report');
        if (empty($donations) || count($donations) == 0) {
            Session::flash('warning', 'There are no donations to export');
            return back();
        }
        return Excel::download(new DonationExport($donations), 'dontaions.csv');
    }
}

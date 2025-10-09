<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtended;
use App\BasicExtra;
use App\BasicSetting;
use App\Exports\PackageOrderExport;
use App\Http\Controllers\Controller;
use App\Language;
use App\OfflineGateway;
use App\Package;
use App\PackageCategory;
use App\PackageInput;
use App\PackageInputOption;
use App\PackageOrder;
use App\PaymentGateway;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class PackageController extends Controller
{
  public function index(Request $request)
  {
    $lang = Language::where('code', $request->language)->first();

        $lang_id = $lang->id;
        $data['packages'] = Package::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();
        $data['abx'] = $lang->basic_extra;

    $data['lang_id'] = $lang_id;

    $data['categoryInfo'] = BasicExtra::first();

    return view('admin.package.index', $data);
  }

  public function edit(Request $request, $id)
  {
    $lang = Language::where('code', $request->language)->first();

    $data['categories'] = PackageCategory::where('language_id', $lang->id)
      ->where('status', 1)
      ->get();

    $data['categoryInfo'] = BasicExtra::first();

    $data['package'] = Package::findOrFail($id);
    $abe = BasicExtended::where('language_id', $data['package']->language_id)->first();
    $abx = BasicExtra::select('base_currency_text')->where('language_id', $data['package']->language_id)->first();

    $data['abe'] = $abe;
    $data['abx'] = $abx;

    return view('admin.package.edit', $data);
  }

  public function form(Request $request)
  {
    $lang = Language::where('code', $request->language)->firstOrFail();
    $data['lang_id'] = $lang->id;
    $data['abs'] = $lang->basic_setting;
    $data['inputs'] = PackageInput::where('language_id', $data['lang_id'])->get();

    $data['ndaIn'] = PackageInput::find(1);
    return view('admin.package.form', $data);
  }

  public function formstore(Request $request)
  {

    $inname = make_input_name($request->label);
    $inputs = PackageInput::where('language_id', $request->language_id)->get();

    $messages = [
      'options.*.required_if' => 'Options are required if field type is select dropdown/checkbox',
      'placeholder.required_unless' => 'The placeholder field is required unless field type is Checkbox or File'
    ];

    $rules = [
      'label' => [
        'required',
        function ($attribute, $value, $fail) use ($inname, $inputs) {
          foreach ($inputs as $key => $input) {
            if ($input->name == $inname) {
              $fail("Input field already exists.");
            }
          }
        },
      ],
      'placeholder' => 'required_unless:type,3,5',
      'type' => 'required',
      'options.*' => 'required_if:type,2,3'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      $errmsgs = $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $input = new PackageInput;
    $input->language_id = $request->language_id;
    $input->type = $request->type;
    $input->label = $request->label;
    $input->name = $inname;
    $input->placeholder = $request->placeholder;
    $input->required = $request->required;
    $input->save();

    if ($request->type == 2 || $request->type == 3) {
      $options = $request->options;
      foreach ($options as $key => $option) {
        $op = new PackageInputOption;
        $op->package_input_id = $input->id;
        $op->name = $option;
        $op->save();
      }
    }

    Session::flash('success', 'Input field added successfully!');
    return "success";
  }

  public function inputDelete(Request $request)
  {
    $input = PackageInput::find($request->input_id);
    $input->package_input_options()->delete();
    $input->delete();
    Session::flash('success', 'Input field deleted successfully!');
    return back();
  }

  public function inputEdit($id)
  {
    $data['input'] = PackageInput::find($id);
    if (!empty($data['input']->package_input_options)) {
      $options = $data['input']->package_input_options;
      $data['options'] = $options;
      $data['counter'] = count($options);
    }
    return view('admin.package.form-edit', $data);
  }

  public function inputUpdate(Request $request)
  {
    $inname = make_input_name($request->label);
    $input = PackageInput::find($request->input_id);
    $inputs = PackageInput::where('language_id', $input->language_id)->get();

    // return $request->options;
    $messages = [
      'options.required_if' => 'Options are required',
      'placeholder.required_unless' => 'Placeholder is required'
    ];

    $rules = [
      'label' => [
        'required',
        function ($attribute, $value, $fail) use ($inname, $inputs, $input) {
          foreach ($inputs as $key => $in) {
            if ($in->name == $inname && $inname != $input->name) {
              $fail("Input field already exists.");
            }
          }
        },
      ],
      'placeholder' => 'required_unless:type,3,5',
      'options' => [
        'required_if:type,2,3',
        function ($attribute, $value, $fail) use ($request) {
          if ($request->type == 2 || $request->type == 3) {
            foreach ($request->options as $option) {
              if (empty($option)) {
                $fail('All option fields are required.');
              }
            }
          }
        },
      ]
    ];

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      $errmsgs = $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }


    $input->label = $request->label;
    $input->name = $inname;

    // if input is checkbox then placeholder is not required
    if ($request->type != 3 && $request->type != 5) {
      $input->placeholder = $request->placeholder;
    }
    $input->required = $request->required;
    $input->save();

    if ($request->type == 2 || $request->type == 3) {
      $input->package_input_options()->delete();
      $options = $request->options;
      foreach ($options as $key => $option) {
        $op = new PackageInputOption;
        $op->package_input_id = $input->id;
        $op->name = $option;
        $op->save();
      }
    }

    Session::flash('success', 'Input field updated successfully!');
    return "success";
  }

  public function options($id)
  {
    $options = PackageInputOption::where('package_input_id', $id)->get();
    return $options;
  }

  public function getCategories($langId)
  {
    $package_categories = PackageCategory::where('language_id', $langId)
      ->where('status', 1)
      ->get();

    return $package_categories;
  }

  public function store(Request $request)
  {
    $categoryInfo = BasicExtra::first();

    $rules = [
      'language_id' => 'required',
      'title' => 'required|max:40',
      'price' => 'required|numeric',
      'description' => 'required',
      'serial_number' => 'required|integer'
    ];

    if ($categoryInfo->package_category_status == 1) {
      $rules['category_id'] = 'required';
    }

    $bex = BasicExtra::first();
    if ($bex->recurring_billing == 0) {
      $rules['order_status'] = 'required';
      $rules['link'] = 'required_if:order_status,2';
    } else {
      $rules['duration'] = 'required';
    }

    $messages = [
      'language_id.required' => 'The language field is required'
    ];

    if ($categoryInfo->package_category_status == 1) {
      $messages['category_id.required'] = 'The category field is required';
    }

    if ($bex->recurring_billing == 0) {
      $messages['link.required_if'] = 'External link is required';
    }

    $be = BasicExtended::first();
    $version = $be->theme_version;

    if ($version == 'cleaning') {
      $rules['color'] = 'required';
    }

    if ($version == 'lawyer') {
      $image = $request->image;
      $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
      $extImage = pathinfo($image, PATHINFO_EXTENSION);

      $rules['image'] = [
        'required',
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

    $package = new Package;
    $package->language_id = $request->language_id;
    $package->title = $request->title;
    if ($version == 'lawyer') {
      if ($request->filled('image')) {
        $filename = uniqid() . '.' . $extImage;
        @copy($image, 'assets/front/img/packages/' . $filename);
        $package->image = $filename;
      }
    }
    $package->price = $request->price;
    $package->serial_number = $request->serial_number;
    $package->meta_keywords = $request->meta_keywords;
    $package->meta_description = $request->meta_description;
    $package->description = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->description);

    if ($bex->recurring_billing == 0) {
      $package->order_status = $request->order_status;
      if ($request->order_status == 2) {
        $package->link = $request->link;
      }
    } else {
      $package->duration = $request->duration;
    }


    if ($version == 'cleaning') {
      $package->color = $request->color;
    }

    $package->category_id = $request->category_id;
    $package->save();

    Session::flash('success', 'Package added successfully!');
    return "success";
  }

  public function update(Request $request)
  {
    $package = Package::findOrFail($request->package_id);
    $bex = BasicExtra::first();

    $rules = [
      'title' => 'required|max:40',
      'price' => 'required|numeric',
      'description' => 'required',
      'serial_number' => 'required|integer'
    ];

    if ($bex->package_category_status == 1) {
      $rules['category_id'] = 'required';
    }

    if ($bex->recurring_billing == 0) {
      $rules['order_status'] = 'required';
      $rules['link'] = 'required_if:order_status,2';
    } else {
      $rules['duration'] = 'required';
    }

    $messages = [];

    if ($bex->recurring_billing == 0) {
      $messages['link.required_if'] = 'External link is required';
    }

    if ($bex->package_category_status == 1) {
      $message['category_id.required'] = 'The category field is required';
    }

    $be = BasicExtended::first();
    $version = $be->theme_version;

    if ($version == 'lawyer') {
      $image = $request->image;
      $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
      $extImage = pathinfo($image, PATHINFO_EXTENSION);

      if ($request->filled('image')) {
        $rules['image'] = [
          function ($attribute, $value, $fail) use ($extImage, $allowedExts) {
            if (!in_array($extImage, $allowedExts)) {
              return $fail("Only png, jpg, jpeg, svg image is allowed");
            }
          }
        ];
      }
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $errmsgs = $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $package->title = $request->title;

    if ($version == 'cleaning') {
      $package->color = $request->color;
    }

    $package->price = $request->price;
    $package->serial_number = $request->serial_number;
    $package->meta_keywords = $request->meta_keywords;
    $package->meta_description = $request->meta_description;
    $package->description = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->description);

    if ($bex->recurring_billing == 0) {
      $package->order_status = $request->order_status;
      if ($request->order_status == 2) {
        $package->link = $request->link;
      }
    } else {
      $package->duration = $request->duration;
    }

    if ($version == 'lawyer') {
      if ($request->filled('image')) {
        @unlink('assets/front/img/packages/' . $package->image);
        $filename = uniqid() . '.' . $extImage;
        @copy($image, 'assets/front/img/packages/' . $filename);
        $package->image = $filename;
      }
    }

    $package->category_id = $request->category_id;

    $package->save();

    Session::flash('success', 'Package updated successfully!');
    return "success";
  }


  public function delete(Request $request)
  {
    $package = Package::findOrFail($request->package_id);
    // if the package has any currently active subscription / subscription requests, then it cannot be deleted
    if ($package->current_subscriptions()->where('status', 1)->count() > 0 || $package->pending_subscriptions()->count() > 0) {
      Session::flash('warning', 'Please delete the active subscriptions & subscription requests of this package first');
      return back();
    }
    $package->current_subscriptions()->delete();
    $package->next_subscriptions()->delete();
    $package->pending_subscriptions()->delete();

    @unlink('assets/front/img/packages/' . $package->image);

    $package->delete();

    Session::flash('success', 'Package deleted successfully!');
    return back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $package = Package::findOrFail($id);
      // if the package has any currently active subscription / subscription requests, then it cannot be deleted
      if ($package->current_subscriptions()->where('status', 1)->count() > 0 || $package->pending_subscriptions()->count() > 0) {
        Session::flash('warning', 'Please delete the active subscriptions & subscription requests of ' . $package->title . ' package first');
        return "success";
      }

      @unlink('assets/front/img/packages/' . $package->image);

      $package->delete();
    }

    Session::flash('success', 'Packages deleted successfully!');
    return "success";
  }

  public function all(Request $request)
  {
    $term = $request->term;
    $data['orders'] = PackageOrder::when($term, function ($query, $term) {
      return $query->where('order_number', $term);
    })->orderBy('id', 'DESC')->paginate(10);
    return view('admin.package.orders', $data);
  }

  public function pending(Request $request)
  {
    $term = $request->term;
    $data['orders'] = PackageOrder::when($term, function ($query, $term) {
      return $query->where('order_number', $term);
    })->where('status', 0)->orderBy('id', 'DESC')->paginate(10);
    return view('admin.package.orders', $data);
  }

  public function processing(Request $request)
  {
    $term = $request->term;
    $data['orders'] = PackageOrder::when($term, function ($query, $term) {
      return $query->where('order_number', $term);
    })->where('status', 1)->orderBy('id', 'DESC')->paginate(10);
    return view('admin.package.orders', $data);
  }

  public function completed(Request $request)
  {
    $term = $request->term;
    $data['orders'] = PackageOrder::when($term, function ($query, $term) {
      return $query->where('order_number', $term);
    })->where('status', 2)->orderBy('id', 'DESC')->paginate(10);
    return view('admin.package.orders', $data);
  }

  public function rejected(Request $request)
  {
    $term = $request->term;
    $data['orders'] = PackageOrder::when($term, function ($query, $term) {
      return $query->where('order_number', $term);
    })->where('status', 3)->orderBy('id', 'DESC')->paginate(10);
    return view('admin.package.orders', $data);
  }

  public function status(Request $request)
  {
    $po = PackageOrder::find($request->order_id);
    $po->status = $request->status;
    $po->save();

    Session::flash('success', 'Order status changed successfully!');
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

    // Mail::to($to)->send(new ContactMail($from, $sub, $msg));

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

  public function orderDelete(Request $request)
  {
    $order = PackageOrder::findOrFail($request->order_id);
    @unlink('assets/front/ndas/' . $order->nda);
    @unlink('assets/front/receipt/' . $order->receipt);
    $order->delete();

    Session::flash('success', 'Package order deleted successfully!');
    return back();
  }

  public function bulkOrderDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $order = PackageOrder::findOrFail($id);
      @unlink('assets/front/ndas/' . $order->nda);
      @unlink('assets/front/receipt/' . $order->receipt);
      $order->delete();
    }

    Session::flash('success', 'Orders deleted successfully!');
    return "success";
  }

  public function feature(Request $request)
  {
    $package = Package::find($request->package_id);
    $package->feature = $request->feature;
    $package->save();

    if ($request->feature == 1) {
      Session::flash('success', 'Featured successfully!');
    } else {
      Session::flash('success', 'Unfeatured successfully!');
    }

    return back();
  }

  public function background(Request $request)
  {
    $lang = Language::where('code', $request->language)->firstOrFail();
    $data['lang_id'] = $lang->id;
    $data['abe'] = $lang->basic_extended;

    return view('admin.home.package-background', $data);
  }

  public function uploadBackground(Request $request, $langid)
  {
    $image = $request->background_image;
    $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
    $extImage = pathinfo($image, PATHINFO_EXTENSION);

    $rules = [];

    if ($request->filled('background_image')) {
      $rules['background_image'] = [
        function ($attribute, $value, $fail) use ($extImage, $allowedExts) {
          if (!in_array($extImage, $allowedExts)) {
            return $fail("Only png, jpg, jpeg, svg image is allowed");
          }
        }
      ];
    }

    $request->validate($rules);

    if ($request->filled('background_image')) {

      $be = BasicExtended::where('language_id', $langid)->firstOrFail();

      @unlink('assets/front/img/' . $be->package_background);
      $filename = uniqid() . '.' . $extImage;
      @copy($image, 'assets/front/img/' . $filename);

      $be->package_background = $filename;
      $be->save();
    }

    $request->session()->flash('success', 'Package section background');
    return back();
  }

  public function settings()
  {
    $data['abex'] = BasicExtra::first();
    return view('admin.package.settings', $data);
  }

  public function updateSettings(Request $request)
  {
    $bexs = BasicExtra::all();

    foreach ($bexs as $bex) {
      $bex->recurring_billing = $request->recurring_billing;
      $bex->expiration_reminder = $request->expiration_reminder;
      $bex->package_guest_checkout = $request->package_guest_checkout;
      $bex->package_category_status = $request->package_category_status;
      $bex->save();
    }

    $request->session()->flash('success', 'Settings updated successfully!');
    return back();
  }

  public function paymentStatus(Request $request)
  {
    $po = PackageOrder::find($request->order_id);
    $po->payment_status = $request->payment_status;
    $po->save();

    $be = BasicExtended::first();
    $sub = 'Payment Status Updated';

    $to = $po->email;
    $fname = $po->name;

    if ($request->payment_status == 1) {
      $status = 'Completed';
    } elseif ($request->payment_status == 0) {
      $status = 'Pending';
    }

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
        $mail->addAddress($to, $fname);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $sub;
        $mail->Body    = 'Hello <strong>' . $fname . '</strong>,<br/>Your payment status is changed to ' . $status . '.<br/>Thank you.';
        $mail->send();
      } catch (Exception $e) {
        // die($e->getMessage());
      }
    } else {
      try {

        //Recipients
        $mail->setFrom($be->from_mail, $be->from_name);
        $mail->addAddress($to, $fname);


        // Content
        $mail->isHTML(true);
        $mail->Subject = $sub;
        $mail->Body    = 'Hello <strong>' . $fname . '</strong>,<br/>Your payment status is changed to ' . $status . '.<br/>Thank you.';

        $mail->send();
      } catch (Exception $e) {
        // die($e->getMessage());
      }
    }

    Session::flash('success', 'Payment status updated!');
    return back();
  }

  public function report(Request $request) {
      $fromDate = $request->from_date;
      $toDate = $request->to_date;
      $orderStatus = $request->order_status;
      $paymentStatus = $request->payment_status;
      $paymentMethod = $request->payment_method;

      if (!empty($fromDate) && !empty($toDate)) {
          $pos = PackageOrder::when($fromDate, function ($query, $fromDate) {
              return $query->whereDate('created_at', '>=', Carbon::parse($fromDate));
          })->when($toDate, function ($query, $toDate) {
              return $query->whereDate('created_at', '<=', Carbon::parse($toDate));
          })->when($paymentMethod, function ($query, $paymentMethod) {
              return $query->where('method', $paymentMethod);
          })->when(!empty($orderStatus) || $orderStatus == "0", function ($query) use ($request) {
              return $query->where('status', $request->order_status);
          })->when(!empty($paymentStatus) || $paymentStatus == "0", function ($query) use ($request) {
              return $query->where('payment_status', "=", $request->payment_status);
          })->select('order_number','package_title','name','email','package_price','method','status','payment_status','created_at')->orderBy('id', 'DESC');
          Session::put('package_order_report', $pos->get());
          $data['pos'] = $pos->paginate(10);
      } else {
          Session::put('package_order_report', []);
          $data['pos'] = [];
      }

      $data['onPms'] = PaymentGateway::where('status', 1)->get();
      $data['offPms'] = OfflineGateway::where('package_order_status', 1)->get();


      return view('admin.package.report', $data);
  }

  public function exportReport() {
      $pos = Session::get('package_order_report');
      if (empty($pos) || count($pos) == 0) {
          Session::flash('warning', 'There are no package orders to export');
          return back();
      }
      return Excel::download(new PackageOrderExport($pos), 'package-orders.csv');
  }
}

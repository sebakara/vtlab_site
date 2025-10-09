<?php

namespace App\Http\Controllers\Front;

use App\Feedback;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
  public function feedback()
  {
    if (session()->has('lang')) {
      $currentLang = Language::where('code', session()->get('lang'))->first();
    } else {
      $currentLang = Language::where('is_default', 1)->first();
    }

    $data['bse'] = $currentLang->basic_extra;
    $data['currentLang'] = $currentLang;

    $be = $currentLang->basic_extended;
    $version = $be->theme_version;

    if ($version == 'dark') {
      $version = 'default';
    }

    $data['version'] = $version;

    return view('front.client_feedback', $data);
  }

  public function storeFeedback(StoreFeedbackRequest $request)
  {
    Feedback::create($request->all());

    Session::flash('success', 'Your feedback has submitted.');

    return redirect()->back();
  }
}

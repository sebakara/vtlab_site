<?php

namespace App\Http\Controllers\Front;

use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FreeCourseEnrollController extends Controller
{
  public function enroll(Request $request)
  {
    $course = Course::findOrFail($request->course_id);

    if (!Auth::user()) {
      Session::put('link', route('course_details', ['slug' => $course->slug]));
      return redirect()->route('user.login');
    }

    // store the course purchase information in database
    $course_purchase = new CoursePurchase;
    $course_purchase->user_id = Auth::user()->id;
    $course_purchase->order_number = rand(100, 500) . time();
    $course_purchase->first_name = Auth::user()->fname;
    $course_purchase->last_name = Auth::user()->lname;
    $course_purchase->email = Auth::user()->email;
    $course_purchase->course_id = $course->id;
    $course_purchase->currency_code = null;
    $course_purchase->current_price = $course->current_price;
    $course_purchase->previous_price = $course->previous_price;
    $course_purchase->payment_method = 'Free';
    $course_purchase->payment_status = 'Completed';
    $course_purchase->save();

    return redirect()->route('course.enroll.complete');
  }

  public function complete()
  {
    if (session()->has('lang')) {
      $currentLang = Language::where('code', session()->get('lang'))->first();
    } else {
      $currentLang = Language::where('is_default', 1)->first();
    }

    $be = $currentLang->basic_extended;
    $version = $be->theme_version;

    if ($version == 'dark') {
      $version = 'default';
    }

    $data['version'] = $version;

    return view('front.course.enroll_complete', $data);
  }
}

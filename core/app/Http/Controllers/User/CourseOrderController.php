<?php

namespace App\Http\Controllers\User;

use App\BasicExtra;
use App\Course;
use App\CoursePurchase;
use App\Http\Controllers\Controller;
use App\Language;
use App\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseOrderController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    if (session()->has('lang')) {
      $currentLang = Language::where('code', session()->get('lang'))->first();
    } else {
      $currentLang = Language::where('is_default', 1)->first();
    }
    $bex = BasicExtra::first();
    if ($bex->is_course == 0) {
        return back();
    }

    $data['currentLang'] = $currentLang;

    $be = $currentLang->basic_extended;
    $version = $be->theme_version;

    if ($version == 'dark') {
      $version = 'default';
    }

    $data['version'] = $version;

    $data['course_orders'] = CoursePurchase::where('user_id', Auth::user()->id)
      ->where('payment_status', 'completed')
      ->orderBy('id', 'desc')->get();

    return view('user.course_order', $data);
  }

  public function courseLessons($id)
  {
    if (session()->has('lang')) {
      $currentLang = Language::where('code', session()->get('lang'))->first();
    } else {
      $currentLang = Language::where('is_default', 1)->first();
    }

    $bex = BasicExtra::first();
    if ($bex->is_course == 0) {
        return back();
    }

    $data['currentLang'] = $currentLang;

    $be = $currentLang->basic_extended;
    $version = $be->theme_version;

    if ($version == 'dark') {
      $version = 'default';
    }

    $data['version'] = $version;

    $coursePurchase = CoursePurchase::findOrFail($id);
    if (strtolower($coursePurchase->payment_status) != 'completed') {
        return back();
    }
    $courseId = $coursePurchase->course_id;
    $data['course'] = Course::findOrFail($courseId);

    $data['modules'] = Module::where('course_id', $courseId)->get();

    return view('user.course_lessons', $data);
  }
}

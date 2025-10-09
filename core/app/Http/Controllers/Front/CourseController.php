<?php

namespace App\Http\Controllers\Front;

use App\BasicExtra;
use App\Course;
use App\CourseCategory;
use App\CourseReview;
use App\Http\Controllers\Controller;
use App\Language;
use App\Module;
use App\OfflineGateway;
use App\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
  public function courses(Request $request)
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

    $data['featured_courses'] = Course::where('language_id', $currentLang->id)
      ->where('is_featured', 1)
      ->orderBy('id', 'desc')
      ->get();

    $data['course_categories'] = CourseCategory::where('language_id', $currentLang->id)
      ->where('status', 1)
      ->orderBy('id', 'desc')
      ->get();

    $data['courseCount'] = Course::where('language_id', $currentLang->id)->count();

    $searchKey = $request->search;
    $categoryId = $request->category_id;
    $checked = $request->checked_value;
    $rating = $request->rating;
    $minPrice = $request->minValue;
    $maxPrice = $request->maxValue;
    $filterKey = $request->filterValue;

    // this block of code works for both default (all courses) and search functionality
    $data['courses'] = Course::where('language_id', $currentLang->id)
      ->when($searchKey, function ($query, $searchKey) {
        return $query->where('title', 'like', '%' . $searchKey . '%');
      })->when($categoryId, function ($query, $categoryId) {
        return $query->where('course_category_id', $categoryId);
      })->when($checked, function ($query, $checked) {
        if ($checked == 'free') {
          return $query->where('current_price', '=', null);
        } else if ($checked == 'premium') {
          return $query->where('current_price', '!=', null);
        }
      })->when($rating, function ($query, $rating) {
        if (in_array($rating, [1, 2, 3, 4])) {
          return $query->where('average_rating', '>=', $rating);
        } else if ($rating == 5) {
          return $query->where('average_rating', 5);
        }
      })->when($minPrice, function ($query, $minPrice) {
        return $query->where('current_price', '>=', $minPrice);
      })->when($maxPrice, function ($query, $maxPrice) {
        return $query->where('current_price', '<=', $maxPrice);
      })->when($filterKey, function ($query, $filterKey) {
        if ($filterKey == 'new') {
          return $query->orderBy('id', 'desc');
        } else if ($filterKey == 'old') {
          return $query->orderBy('id', 'asc');
        } else if ($filterKey == 'high-to-low') {
          return $query->where('current_price', '!=', null)->orderBy('current_price', 'desc');
        } else if ($filterKey == 'low-to-high') {
          return $query->where('current_price', '!=', null)->orderBy('current_price', 'asc');
        } else if ($filterKey == 'high-to-low-rating') {
          return $query->orderBy('average_rating', 'desc');
        }
      })->when(!$filterKey, function ($query) {
        return $query->orderBy('id', 'desc');
      })->paginate(9);

    $data['bse'] = $currentLang->basic_extra;
    $data['currentLang'] = $currentLang;

    $be = $currentLang->basic_extended;
    $version = $be->theme_version;

    if ($version == 'dark') {
      $version = 'default';
    }

    $data['version'] = $version;

    return view('front.course.courses', $data);
  }

  public function courseDetails($slug)
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

    $data['course_details'] = Course::where('language_id', $currentLang->id)
      ->where('slug', $slug)
      ->firstOrFail();

    $course = $data['course_details'];
    $data['modules'] = Module::where('course_id', $course->id)->get();
    $data['paymentGateways'] = PaymentGateway::where('status', 1)->orderBy('name', 'asc')->get();
    $data['offlineGateways'] = OfflineGateway::where('language_id', $currentLang->id)->where('course_checkout_status', 1)->orderBy('serial_number', 'asc')->get();
    $data['reviews'] = CourseReview::where('course_id', $course->id)
      ->orderBy('id', 'desc')
      ->get();

    $data['bse'] = $currentLang->basic_extra;
    $data['currentLang'] = $currentLang;

    $be = $currentLang->basic_extended;
    $version = $be->theme_version;

    if ($version == 'dark') {
      $version = 'default';
    }
    $stripeData = PaymentGateway::whereKeyword('stripe')->first();
    $stripe = $stripeData->convertAutoData();
    $data['stripe_key'] =  $stripe['key'];

    $data['version'] = $version;

    return view('front.course.course_details', $data);
  }

  public function giveReview(Request $request)
  {
    $rules = [
      'rating' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      return redirect()->back()->with('error', __('The course rating field is required.'));
    }

    $id = $request->course_id;
    $user = Auth::user();

    // get all the purchased courses of this authenticate user
    $orders = $user->courseOrder;

    // set a flag value
    $purchasedCourse = false;

    // checking whether the authenticate user have purchased this course or not
    foreach ($orders as $order) {
      $courseId = $order->course_id;

      if ($courseId == $id) {
        $purchasedCourse = true;
        break;
      } else {
        $purchasedCourse = false;
      }
    }

    if ($purchasedCourse == false) {
      return redirect()->back()->with('error', __('You have not purchased this course yet.'));
    }

    // checking whether there has an existing comment of this user for this course
    $reviewed = CourseReview::where('user_id', $user->id)->where('course_id', $id)->count();

    // if one of these value is null then store a new review in database
    if ($reviewed == 0) {
      $review = new CourseReview;
      $review->user_id = Auth::user()->id;
      $review->course_id = $id;
      $review->comment = $request->comment;
      $review->rating = $request->rating;
      $review->save();
    } else {
      // else update the existing review
      $review = CourseReview::where('user_id', $user->id)
        ->where('course_id', $id)
        ->first();

      $review->update([
        'user_id' => Auth::user()->id,
        'course_id' => $id,
        'comment' => $request->comment,
        'rating' => $request->rating
      ]);
    }

    // get the average rating for this course
    $course_reviews = CourseReview::where('course_id', $id)->get();
    $avg = $course_reviews->avg('rating');
    $avg_val = floatval($avg);
    $value = number_format($avg_val, 1);

    $course = Course::where('id', $id)->first();
    $course->update([
      'average_rating' => $value
    ]);

    return redirect()->back()->with('success', __('Your review has added successfully.'));
  }
}

<?php

namespace App\Http\Controllers\Payment\Course;

use App\Http\Controllers\Controller;
use App\Http\Helpers\KreativMailer;
use App\Language;

class MailController extends Controller
{
	public static function sendMail($course_purchase)
	{
		if (session()->has('lang')) {
			$currentLang = Language::where('code', session()->get('lang'))->first();
		} else {
			$currentLang = Language::where('is_default', 1)->first();
		}

		// bse = basic settings extended
		$bs = $currentLang->basic_setting;

        $mailer = new KreativMailer;
        $data = [
            'toMail' => $course_purchase->email,
            'toName' => $course_purchase->first_name,
            'attachment' => $course_purchase->invoice,
            'customer_name' => $course_purchase->first_name,
            'course_name' => $course_purchase->course->title,
            'order_number' => $course_purchase->order_number,
            'order_link' => !empty($course_purchase->user_id) ? "<strong>Course Lessons:</strong> <a href='" . route('user.course.lessons',$course_purchase->id) . "'>" . route('user.course.lessons',$course_purchase->id) . "</a>" : "",
            'website_title' => $bs->website_title,
            'templateType' => 'course_enroll',
            'type' => 'courseEnroll'
        ];

        $mailer->mailFromAdmin($data);
	}
}

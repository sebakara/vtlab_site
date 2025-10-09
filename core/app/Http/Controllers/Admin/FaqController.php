<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtra;
use App\Faq;
use App\FAQCategory;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
  public function index(Request $request)
  {
    $lang = Language::where('code', $request->language)->first();

    $lang_id = $lang->id;
    $data['faqs'] = Faq::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();

    $data['categories'] = FAQCategory::where('language_id', $lang_id)
      ->where('status', 1)
      ->orderBy('serial_number', 'desc')
      ->get();

    $data['lang_id'] = $lang_id;

    $data['categoryInfo'] = BasicExtra::first();

    return view('admin.home.faq.index', $data);
  }

  public function edit($id)
  {
    $data['faq'] = Faq::findOrFail($id);
    return view('admin.home.faq.edit', $data);
  }

  public function getCategories($langId)
  {
    $faq_categories = FAQCategory::where('language_id', $langId)
      ->where('status', 1)
      ->get();

    return $faq_categories;
  }

  public function store(Request $request)
  {
    $categoryInfo = BasicExtra::first();

    $messages = [
      'language_id.required' => 'The language field is required'
    ];

    if ($categoryInfo->faq_category_status == 1) {
      $messages['category_id.required'] = 'The category field is required';
    }

    $rules = [
      'language_id' => 'required',
      'question' => 'required|max:255',
      'answer' => 'required',
      'serial_number' => 'required|integer'
    ];

    if ($categoryInfo->faq_category_status == 1) {
      $rules['category_id'] = 'required';
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      $errmsgs = $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $faq = new Faq;
    $faq->language_id = $request->language_id;
    $faq->question = $request->question;
    $faq->answer = $request->answer;
    $faq->serial_number = $request->serial_number;
    $faq->category_id = $request->category_id;
    $faq->save();

    Session::flash('success', 'Faq added successfully!');
    return "success";
  }

  public function update(Request $request)
  {
    $categoryInfo = BasicExtra::first();

    $message = [];
    
    if ($categoryInfo->faq_category_status == 1) {
      $message['category_id.required'] = 'The category field is required';
    }

    $rules = [
      'question' => 'required|max:255',
      'answer' => 'required',
      'serial_number' => 'required|integer'
    ];

    if ($categoryInfo->faq_category_status == 1) {
      $rules['category_id'] = 'required';
    }

    $validator = Validator::make($request->all(), $rules, $message);
    
    if ($validator->fails()) {
      $errmsgs = $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $faq = Faq::findOrFail($request->faq_id);
    $faq->question = $request->question;
    $faq->answer = $request->answer;
    $faq->serial_number = $request->serial_number;
    $faq->category_id = $request->category_id;
    $faq->save();

    Session::flash('success', 'Faq updated successfully!');
    return "success";
  }

  public function delete(Request $request)
  {

    $faq = Faq::findOrFail($request->faq_id);
    $faq->delete();

    Session::flash('success', 'Faq deleted successfully!');
    return back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $faq = Faq::findOrFail($id);
      $faq->delete();
    }

    Session::flash('success', 'FAQs deleted successfully!');
    return "success";
  }
}

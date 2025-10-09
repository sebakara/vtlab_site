<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtra;
use App\FAQCategory;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FAQCategoryController extends Controller
{
  public function settings()
  {
    $data['abex'] = BasicExtra::first();

    return view('admin.home.faq.settings', $data);
  }

  public function updateSettings(Request $request)
  {
    $bexs = BasicExtra::all();

    foreach ($bexs as $bex) {
      $bex->update([
        'faq_category_status' => $request->faq_category_status
      ]);
    }

    Session::flash('success', 'Settings updated successfully.');

    return redirect()->back();
  }


  public function index(Request $request)
  {
    $language = Language::where('code', $request->language)->first();

    $categories = FAQCategory::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->paginate(10);

    return view('admin.home.faq.categories', compact('categories'));
  }

  public function store(Request $request)
  {
    $rules = [
      'language_id' => 'required',
      'name' => 'required',
      'status' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');

      return response()->json($validator->errors());
    }

    FAQCategory::create($request->all());

    Session::flash('success', 'New faq category added successfully.');

    return 'success';
  }

  public function update(Request $request)
  {
    $rules = [
      'name' => 'required',
      'status' => 'required',
      'serial_number' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');

      return response()->json($validator->errors());
    }

    FAQCategory::findOrFail($request->categoryId)->update($request->all());

    Session::flash('success', 'FAQ category updated successfully.');

    return 'success';
  }

  public function delete(Request $request)
  {
    $category = FAQCategory::findOrFail($request->categoryId);

    if ($category->frequentlyAskedQuestion->count() > 0) {
      Session::flash('warning', 'First delete all the faqs of this category');

      return redirect()->back();
    }

    $category->delete();

    Session::flash('success', 'FAQ category deleted successfully.');

    return redirect()->back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $category = FAQCategory::findOrFail($id);

      if ($category->frequentlyAskedQuestion->count() > 0) {
        Session::flash('warning', 'First delete all the faqs of those categories');

        return 'success';
      }
    }

    foreach ($ids as $id) {
      $category = FAQCategory::findOrFail($id);

      $category->delete();
    }

    Session::flash('success', 'FAQ categories deleted successfully.');

    return 'success';
  }
}

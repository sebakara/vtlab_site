<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtra;
use App\Gallery;
use App\GalleryCategory;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
  public function index(Request $request)
  {
    $lang = Language::where('code', $request->language)->first();

    $lang_id = $lang->id;
    $data['galleries'] = Gallery::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();

    $data['lang_id'] = $lang_id;

    $data['categoryInfo'] = BasicExtra::first();

    return view('admin.gallery.index', $data);
  }

  public function edit(Request $request, $id)
  {
    $lang = Language::where('code', $request->language)->first();

    $data['categories'] = GalleryCategory::where('language_id', $lang->id)
      ->where('status', 1)
      ->get();

    $data['gallery'] = Gallery::findOrFail($id);

    $data['categoryInfo'] = BasicExtra::first();

    return view('admin.gallery.edit', $data);
  }

  public function getCategories($langId)
  {
    $gallery_categories = GalleryCategory::where('language_id', $langId)
      ->where('status', 1)
      ->get();

    return $gallery_categories;
  }

  public function store(Request $request)
  {
    $categoryInfo = BasicExtra::first();

    $image = $request->image;
    $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
    $extImage = pathinfo($image, PATHINFO_EXTENSION);

    $messages = [
      'language_id.required' => 'The language field is required',
    ];

    if ($categoryInfo->gallery_category_status == 1) {
      $messages['category_id.required'] = 'The category field is required';
    }

    $rules = [
      'language_id' => 'required',
      'image' => 'required',
      'title' => 'required|max:255',
      'serial_number' => 'required|integer',
    ];

    if ($categoryInfo->gallery_category_status == 1) {
      $rules['category_id'] = 'required';
    }

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

    $gallery = new Gallery;
    $gallery->language_id = $request->language_id;
    $gallery->title = $request->title;
    $gallery->serial_number = $request->serial_number;
    $gallery->category_id = $request->category_id;

    if ($request->filled('image')) {
      $filename = uniqid() . '.' . $extImage;
      @mkdir('assets/front/img/gallery', 775, true);
      @copy($image, 'assets/front/img/gallery/' . $filename);
      $gallery->image = $filename;
    }

    $gallery->save();

    Session::flash('success', 'Image added successfully!');
    return "success";
  }

  public function update(Request $request)
  {
    $categoryInfo = BasicExtra::first();

    $message = [];

    if ($categoryInfo->gallery_category_status == 1) {
      $message['category_id.required'] = 'The category field is required';
    }

    $gallery = Gallery::findOrFail($request->gallery_id);
    $image = $request->image;
    $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
    $extImage = pathinfo($image, PATHINFO_EXTENSION);

    $rules = [
      'title' => 'required|max:255',
      'serial_number' => 'required|integer',
    ];

    if ($categoryInfo->gallery_category_status == 1) {
      $rules['category_id'] = 'required';
    }

    if ($request->filled('image')) {
      $rules['image'] = [
        function ($attribute, $value, $fail) use ($extImage, $allowedExts) {
          if (!in_array($extImage, $allowedExts)) {
            return $fail("Only png, jpg, jpeg, svg image is allowed");
          }
        }
      ];
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $errmsgs = $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $gallery = Gallery::findOrFail($request->gallery_id);
    $gallery->title = $request->title;
    $gallery->serial_number = $request->serial_number;
    $gallery->category_id = $request->category_id;

    if ($request->filled('image')) {
      @unlink('assets/front/img/gallery/' . $gallery->image);
      $filename = uniqid() . '.' . $extImage;
      @copy($image, 'assets/front/img/gallery/' . $filename);
      $gallery->image = $filename;
    }

    $gallery->save();

    Session::flash('success', 'Gallery updated successfully!');
    return "success";
  }

  public function delete(Request $request)
  {

    $gallery = Gallery::findOrFail($request->gallery_id);
    @unlink('assets/front/img/gallery/' . $gallery->image);
    $gallery->delete();

    Session::flash('success', 'Image deleted successfully!');
    return back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $gallery = Gallery::findOrFail($id);
      @unlink('assets/front/img/gallery/' . $gallery->image);
      $gallery->delete();
    }

    Session::flash('success', 'Image deleted successfully!');
    return "success";
  }
}

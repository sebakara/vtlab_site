<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtra;
use App\Http\Controllers\Controller;
use App\Language;
use App\PackageCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PackageCategoryController extends Controller
{
  public function index(Request $request)
  {
    $language = Language::where('code', $request->language)->first();

    $categories = PackageCategory::where('language_id', $language->id)
      ->orderBy('id', 'desc')
      ->paginate(10);

    return view('admin.package.categories', compact('categories'));
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

    PackageCategory::create($request->all());

    Session::flash('success', 'New package category added successfully.');

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

    PackageCategory::findOrFail($request->categoryId)->update($request->all());

    Session::flash('success', 'Package category updated successfully.');

    return 'success';
  }

  public function delete(Request $request)
  {
    $category = PackageCategory::findOrFail($request->categoryId);

    if ($category->packageList()->count() > 0) {
      Session::flash('warning', 'First delete all the packages of this category');

      return redirect()->back();
    }

    $category->delete();

    Session::flash('success', 'Package category deleted successfully.');

    return redirect()->back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $category = PackageCategory::findOrFail($id);

      if ($category->packageList()->count() > 0) {
        Session::flash('warning', 'First delete all the packages of those categories');

        return 'success';
      }
    }

    foreach ($ids as $id) {
      $category = PackageCategory::findOrFail($id);

      $category->delete();
    }

    Session::flash('success', 'Package categories deleted successfully.');

    return 'success';
  }
}

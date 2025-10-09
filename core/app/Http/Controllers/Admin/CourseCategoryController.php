<?php

namespace App\Http\Controllers\Admin;

use App\CourseCategory;
use App\Http\Controllers\Controller;
use App\Language;
use App\Megamenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CourseCategoryController extends Controller
{
  public function index(Request $request)
  {
    $language = Language::where('code', $request->language)->first();
    $language_id = $language->id;

    $course_categories = CourseCategory::where('language_id', $language_id)
      ->orderBy('serial_number', 'asc')
      ->paginate(10);

    return view('admin.course.course_category.index', compact('course_categories'));
  }

  public function store(Request $request)
  {
    $rules = [
      'language_id' => 'required',
      'name' => 'required',
      'status' => 'required',
      'serial_number' => 'required|integer'
    ];

    $rules_msg = [
      'language_id.required' => 'The language field is required'
    ];

    $validator = Validator::make($request->all(), $rules, $rules_msg);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $course_category = new CourseCategory;
    $course_category->language_id = $request->language_id;
    $course_category->name = $request->name;
    $course_category->status = $request->status;
    $course_category->serial_number = $request->serial_number;
    $course_category->save();

    Session::flash('success', 'New Course Category Has Added');

    return 'success';
  }

  public function update(Request $request)
  {
    $rules = [
      'name' => 'required|max:255',
      'status' => 'required',
      'serial_number' => 'required|integer'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $course_category = CourseCategory::findOrFail($request->course_category_id);
    $course_category->name = $request->name;
    $course_category->status = $request->status;
    $course_category->serial_number = $request->serial_number;
    $course_category->save();

    Session::flash('success', 'Course Category Has Updated Successfully');

    return 'success';
  }

  public function deleteFromMegaMenu($category) {
      $megamenu = Megamenu::where('language_id', $category->language_id)->where('category', 1)->where('type', 'courses');
      if ($megamenu->count() > 0) {
          $megamenu = $megamenu->first();
          $menus = json_decode($megamenu->menus, true);
          $catId = $category->id;
          if (is_array($menus) && array_key_exists("$catId", $menus)) {
              unset($menus["$catId"]);
              $megamenu->menus = json_encode($menus);
              $megamenu->save();
          }
      }
  }

  public function delete(Request $request)
  {
    $course_category = CourseCategory::findOrFail($request->course_category_id);

    if ($course_category->courses->count() > 0) {
      Session::flash('warning', 'First Delete All The Courses of This Category');

      return back();
    }

    $this->deleteFromMegaMenu($course_category);

    $course_category->delete();

    Session::flash('success', 'Course Category Has Deleted Successfully');

    return back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $course_category = CourseCategory::findOrFail($id);

      if ($course_category->courses->count() > 0) {
        Session::flash('warning', 'First Delete All The Courses of Those Categories');

        return 'success';
      }
    }

    foreach ($ids as $id) {
      $course_category = CourseCategory::findOrFail($id);

      $this->deleteFromMegaMenu($course_category);

      $course_category->delete();
    }

    Session::flash('success', 'Course Categories Has Deleted');

    return 'success';
  }
}

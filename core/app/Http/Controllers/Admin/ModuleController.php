<?php

namespace App\Http\Controllers\Admin;

use App\Course;
use App\Http\Controllers\Controller;
use App\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
  public function index($id)
  {
    $course = Course::findOrFail($id);
    $modules = Module::where('course_id', $course->id)->get();

    return view('admin.course.module.index', compact('course', 'modules'));
  }

  public function store(Request $request)
  {
    $rules = [
      'name' => 'required',
      'duration' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');

      return response()->json($validator->errors());
    }

    $module = new Module;
    $module->course_id = $request->course_id;
    $module->name = $request->name;
    $module->duration = $request->duration;
    $module->save();

    Session::flash('success', 'Course Module Added Successfully');

    return 'success';
  }

  public function update(Request $request)
  {
    $rules = [
      'name' => 'required',
      'duration' => 'required'
    ];

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');

      return response()->json($validator->errors());
    }

    $module = Module::findOrFail($request->module_id);
    $module->name = $request->name;
    $module->duration = $request->duration;
    $module->save();

    Session::flash('success', 'Module Updated Successfully');

    return 'success';
  }

  public function delete(Request $request)
  {
    $module = Module::findOrFail($request->module_id);

    if ($module->lessons->count() > 1) {
      Session::flash('warning', 'First Delete All The Lessons of This Module');

      return back();
    }

    $module->delete();

    Session::flash('success', 'Module Deleted Successfully');

    return back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $module = Module::findOrFail($id);

      if ($module->lessons->count() > 1) {
        Session::flash('warning', 'First Delete All The Lessons of Those Modules');

        return 'success';
      }
    }

    foreach ($ids as $id) {
      $module = Module::findOrFail($id);

      $module->delete();
    }

    Session::flash('success', 'Modules Deleted Successfully');

    return 'success';
  }
}

<?php

namespace App\Http\Controllers\Admin;

use App\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ArticleCategoryController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $language = Language::where('code', $request->language)->first();
    $language_id = $language->id;

    $article_categories = ArticleCategory::where('language_id', $language_id)
      ->orderBy('serial_number', 'asc')
      ->paginate(10);

    return view('admin.article.article_category.index', compact('article_categories'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $rules = [
      'language_id' => 'required',
      'name' => 'required|max:255',
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

    $article_category = new ArticleCategory;
    $article_category->language_id = $request->language_id;
    $article_category->name = $request->name;
    $article_category->status = $request->status;
    $article_category->serial_number = $request->serial_number;
    $article_category->save();

    Session::flash('success', 'New Article Category Has Added');

    return 'success';
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
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

    $article_category = ArticleCategory::findOrFail($request->article_category_id);

    $article_category->name = $request->name;
    $article_category->status = $request->status;
    $article_category->serial_number = $request->serial_number;
    $article_category->save();

    Session::flash('success', 'Article Category Has Updated Successfully');

    return 'success';
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function delete(Request $request)
  {
    $article_category = ArticleCategory::findOrFail($request->article_category_id);

    if ($article_category->articles->count() > 0) {
      Session::flash('warning', 'First Delete All The Articles of This Category');
      return back();
    }

    $article_category->delete();

    Session::flash('success', 'Article Category Has Deleted Successfully');

    return back();
  }

  /**
   * bulk delete method
   */
  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $article_category = ArticleCategory::findOrFail($id);

      if ($article_category->articles->count() > 0) {
        Session::flash('warning', 'First Delete All The Articles of Those Categories');
        return 'success';
      }
    }

    foreach ($ids as $id) {
      $article_category = ArticleCategory::findOrFail($id);

      $article_category->delete();
    }

    Session::flash('success', 'Article Categories Has Deleted');

    return 'success';
  }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\ArticleCategory;
use App\Http\Controllers\Controller;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
	public function index(Request $request)
	{
		$language = Language::where('code', $request->language)->first();
		$language_id = $language->id;

		$articles = Article::where('language_id', $language_id)
			->orderBy('id', 'DESC')
			->get();

		return view('admin.article.article.index', compact('articles'));
	}

	public function store(Request $request)
	{
        $slug = make_slug($request->title);

		$rules = [
			'language_id' => 'required',
			'article_category_id' => 'required',
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug) {
                    $articles = Article::all();
                    foreach ($articles as $key => $article) {
                        if (strtolower($slug) == strtolower($article->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
			'content' => 'required',
			'serial_number' => 'required|integer'
		];

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$validator->getMessageBag()->add('error', 'true');
			return response()->json($validator->errors());
		}

		$article = new Article;

		$article->language_id = $request->language_id;
		$article->article_category_id = $request->article_category_id;
		$article->title = $request->title;
		$article->slug = $slug;
		$article->content = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);
		$article->serial_number = $request->serial_number;
		$article->meta_keywords = $request->meta_keywords;
		$article->meta_description = $request->meta_description;
		$article->save();

		Session::flash('success', 'Article Added Successfully');

		return 'success';
	}

	public function edit($id)
	{
		$article = Article::findOrFail($id);

		$article_categories = ArticleCategory::where('language_id', $article->language_id)
			->where('status', 1)
			->orderBy('id', 'desc')
			->get();

		return view('admin.article.article.edit', compact('article', 'article_categories'));
	}

	public function update(Request $request)
	{
        $slug = make_slug($request->title);
		$article = Article::findOrFail($request->article_id);
        $articleId = $request->article_id;

		$rules = [
			'article_category_id' => 'required',
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug, $articleId) {
                    $articles = Article::all();
                    foreach ($articles as $key => $article) {
                        if ($article->id != $articleId && strtolower($slug) == strtolower($article->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
			'content' => 'required',
			'serial_number' => 'required|integer'
		];

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$validator->getMessageBag()->add('error', 'true');
			return response()->json($validator->errors());
		}

		$article->article_category_id = $request->article_category_id;
		$article->title = $request->title;
		$article->slug = $slug;
		$article->content = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);
		$article->serial_number = $request->serial_number;
		$article->meta_keywords = $request->meta_keywords;
		$article->meta_description = $request->meta_description;
		$article->save();

		Session::flash('success', 'Article Updated Successfully');

		return 'success';
	}

	public function delete(Request $request)
	{
		$article = Article::findOrFail($request->article_id);

		$article->delete();

		Session::flash('success', 'Article Deleted Successfully');

		return back();
	}

	public function bulkDelete(Request $request)
	{
		$ids = $request->ids;

		foreach ($ids as $id) {
			$article = Article::findOrFail($id);
			$article->delete();
		}

		Session::flash('success', 'Articles Deleted Successfully');

		return 'success';
	}

	public function getCategories($langId)
	{
		$article_categories = ArticleCategory::where('language_id', $langId)
			->where('status', 1)
			->get();

		return $article_categories;
	}
}

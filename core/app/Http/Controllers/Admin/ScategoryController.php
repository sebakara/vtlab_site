<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Scategory;
use App\Language;
use App\Megamenu;
use Validator;
use Session;

class ScategoryController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();

        $lang_id = $lang->id;
        $data['scategorys'] = Scategory::where('language_id', $lang_id)->orderBy('id', 'DESC')->paginate(10);

        $data['lang_id'] = $lang_id;
        return view('admin.service.scategory.index', $data);
    }

    public function edit($id)
    {
        $data['scategory'] = Scategory::findOrFail($id);
        return view('admin.service.scategory.edit', $data);
    }

    public function store(Request $request)
    {
        $image = $request->image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'image' => 'nullable',
            'name' => 'required|max:255',
            'short_text' => 'required',
            'status' => 'required',
            'serial_number' => 'required|integer',
        ];
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

        $scategory = new Scategory;
        $scategory->language_id = $request->language_id;
        $scategory->name = $request->name;
        $scategory->status = $request->status;
        $scategory->short_text = $request->short_text;
        $scategory->serial_number = $request->serial_number;

        if ($request->filled('image')) {
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/service_category_icons/' . $filename);
            $scategory->image = $filename;
        }

        $scategory->save();

        Session::flash('success', 'Category added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $image = $request->image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $rules = [
            'name' => 'required|max:255',
            'status' => 'required',
            'short_text' => 'required',
            'serial_number' => 'required|integer',
        ];

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

        $scategory = Scategory::findOrFail($request->scategory_id);
        $scategory->name = $request->name;
        $scategory->status = $request->status;
        $scategory->short_text = $request->short_text;
        $scategory->serial_number = $request->serial_number;

        if ($request->filled('image')) {
            @unlink('assets/front/img/service_category_icons/' . $scategory->image);
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/service_category_icons/' . $filename);
            $scategory->image = $filename;
        }

        $scategory->save();

        Session::flash('success', 'Category updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {
        $scategory = Scategory::findOrFail($request->scategory_id);

        if ($scategory->services()->count() > 0) {
            Session::flash('warning', 'First, delete all the services under this category!');
            return back();
        }
        @unlink('assets/front/img/service_category_icons/' . $scategory->image);

        $this->deleteFromMegaMenu($scategory);

        $scategory->delete();

        Session::flash('success', 'Scategory deleted successfully!');
        return back();
    }

    public function deleteFromMegaMenu($scategory) {
        $megamenu = Megamenu::where('language_id', $scategory->language_id)->where('category', 1)->where('type', 'services');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            $catId = $scategory->id;
            if (is_array($menus) && array_key_exists("$catId", $menus)) {
                unset($menus["$catId"]);
                $megamenu->menus = json_encode($menus);
                $megamenu->save();
            }
        }
        $megamenu = Megamenu::where('language_id', $scategory->language_id)->where('category', 1)->where('type', 'portfolios');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            $catId = $scategory->id;
            if (is_array($menus) && array_key_exists("$catId", $menus)) {
                unset($menus["$catId"]);
                $megamenu->menus = json_encode($menus);
                $megamenu->save();
            }
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $scategory = Scategory::findOrFail($id);
            if ($scategory->services()->count() > 0) {
                Session::flash('warning', 'First, delete all the services under the selected categories!');
                return "success";
            }
        }

        foreach ($ids as $id) {
            $scategory = Scategory::findOrFail($id);
            @unlink('assets/front/img/service_category_icons/' . $scategory->image);

            $this->deleteFromMegaMenu($scategory);

            $scategory->delete();
        }

        Session::flash('success', 'Service categories deleted successfully!');
        return "success";
    }

    public function feature(Request $request)
    {
        $scategory = Scategory::find($request->scategory_id);
        $scategory->feature = $request->feature;
        $scategory->save();

        if ($request->feature == 1) {
            Session::flash('success', 'Featured successfully!');
        } else {
            Session::flash('success', 'Unfeatured successfully!');
        }

        return back();
    }
}

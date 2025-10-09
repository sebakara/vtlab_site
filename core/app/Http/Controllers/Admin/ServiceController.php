<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtended;
use App\BasicExtra;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Service;
use App\Scategory;
use App\Language;
use App\Megamenu;
use Validator;
use Session;

class ServiceController extends Controller
{

    public function settings()
    {
        $data['abex'] = BasicExtra::first();
        return view('admin.service.settings', $data);
    }

    public function updateSettings(Request $request)
    {
        $bexs = BasicExtra::all();
        foreach ($bexs as $bex) {
            $bex->service_category = $request->service_category;
            $bex->save();
        }

        $request->session()->flash('success', 'Settings updated successfully!');
        return back();
    }

    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();

        $lang_id = $lang->id;
        $data['services'] = Service::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();

        $data['lang_id'] = $lang_id;
        $data['abe'] = BasicExtended::where('language_id', $lang_id)->first();

        return view('admin.service.service.index', $data);
    }

    public function edit($id)
    {
        $data['service'] = Service::findOrFail($id);
        $data['ascats'] = Scategory::where('status', 1)->where('language_id', $data['service']->language_id)->get();
        $data['abe'] = BasicExtended::where('language_id', $data['service']->language_id)->first();
        return view('admin.service.service.edit', $data);
    }

    public function store(Request $request)
    {
        $image = $request->image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $language = Language::find($request->language_id);
        $be = $language->basic_extended;

        $messages = [
            'language_id.required' => 'The language field is required'
        ];

        $slug = make_slug($request->title);

        $rules = [
            'language_id' => 'required',
            'image' => 'required',
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug) {
                    $services = Service::all();
                    foreach ($services as $key => $service) {
                        if (strtolower($slug) == strtolower($service->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'serial_number' => 'required',
            'content' => 'required',
            'details_page_status' => 'required',
            'summary' => 'required',
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

        // if 'theme version'contains service category
        if (serviceCategory()) {
            $rules["category"] = 'required';
        }

        // if 'theme version' doesn't contain service category
        if ($request->details_page_status == 0) {
            $rules["content"] = 'nullable';
        }

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $service = new Service;
        $service->language_id = $request->language_id;
        $service->title = $request->title;

        if ($request->filled('image')) {
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/services/' . $filename);
            $service->main_image = $filename;
        }

        $service->slug = $slug;
        // if 'theme version'contains service category
        if (serviceCategory()) {
            $service->scategory_id = $request->category;
        }
        $service->summary = $request->summary;
        $service->details_page_status = $request->details_page_status;
        $service->meta_description = $request->meta_description;
        $service->meta_keywords = $request->meta_keywords;
        $service->serial_number = $request->serial_number;
        $service->content = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);
        $service->save();

        Session::flash('success', 'Service added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $slug = make_slug($request->title);
        $service = Service::findOrFail($request->service_id);
        $serviceId = $request->service_id;

        $image = $request->image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $language = Language::find($service->language_id);
        $be = $language->basic_extended;

        $rules = [
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug, $serviceId) {
                    $services = Service::all();
                    foreach ($services as $key => $service) {
                        if ($service->id != $serviceId && strtolower($slug) == strtolower($service->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'content' => 'required',
            'serial_number' => 'required',
            'details_page_status' => 'required',
            'summary' => 'required',
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

        if (serviceCategory()) {
            $rules["category"] = 'required';
        }

        if ($request->details_page_status == 0) {
            $rules["content"] = 'nullable';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $service->title = $request->title;
        $service->slug = $slug;
        if (serviceCategory()) {
            $service->scategory_id = $request->category;
        }
        $service->summary = $request->summary;
        $service->details_page_status = $request->details_page_status;
        $service->serial_number = $request->serial_number;
        $service->meta_keywords = $request->meta_keywords;
        $service->meta_description = $request->meta_description;
        $service->content = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->content);

        if ($request->filled('image')) {
            @unlink('assets/front/img/services/' . $service->main_image);
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/services/' . $filename);
            $service->main_image = $filename;
        }

        $service->save();

        Session::flash('success', 'Service updated successfully!');
        return "success";
    }

    public function deleteFromMegaMenu($service) {
        // unset service from megamenu for service_category = 1
        $megamenu = Megamenu::where('language_id', $service->language_id)->where('category', 1)->where('type', 'services');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            $catId = $service->scategory->id;
            if (is_array($menus) && array_key_exists("$catId", $menus)) {
                if (in_array($service->id, $menus["$catId"])) {
                    $index = array_search($service->id, $menus["$catId"]);
                    unset($menus["$catId"]["$index"]);
                    $menus["$catId"] = array_values($menus["$catId"]);
                    if (count($menus["$catId"]) == 0) {
                        unset($menus["$catId"]);
                    }
                    $megamenu->menus = json_encode($menus);
                    $megamenu->save();
                }
            }
        }

        // unset service from megamenu for service_category = 0
        $megamenu = Megamenu::where('language_id', $service->language_id)->where('category', 0)->where('type', 'services');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            if (is_array($menus)) {
                if (in_array($service->id, $menus)) {
                    $index = array_search($service->id, $menus);
                    unset($menus["$index"]);
                    $menus = array_values($menus);
                    $megamenu->menus = json_encode($menus);
                    $megamenu->save();
                }
            }
        }
    }

    public function delete(Request $request)
    {
        $service = Service::findOrFail($request->service_id);
        @unlink('assets/front/img/services/' . $service->main_image);

        $this->deleteFromMegaMenu($service);

        $service->delete();

        Session::flash('success', 'Service deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $service = Service::findOrFail($id);
            @unlink('assets/front/img/services/' . $service->main_image);

            $this->deleteFromMegaMenu($service);

            $service->delete();
        }

        Session::flash('success', 'Services deleted successfully!');
        return "success";
    }

    public function getcats($langid)
    {
        $scategories = Scategory::where('language_id', $langid)->get();

        return $scategories;
    }

    public function feature(Request $request)
    {
        $service = Service::find($request->service_id);
        $service->feature = $request->feature;
        $service->save();

        if ($request->feature == 1) {
            Session::flash('success', 'Featured successfully!');
        } else {
            Session::flash('success', 'Unfeatured successfully!');
        }

        return back();
    }

    public function sidebar(Request $request)
    {
        $service = Service::find($request->service_id);
        $service->sidebar = $request->sidebar;
        $service->save();

        if ($request->sidebar == 1) {
            Session::flash('success', 'Enabled successfully!');
        } else {
            Session::flash('success', 'Disabled successfully!');
        }

        return back();
    }
}

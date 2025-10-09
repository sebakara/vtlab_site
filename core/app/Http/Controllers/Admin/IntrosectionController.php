<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtended;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BasicSetting as BS;
use App\Language;
use Validator;
use Session;

class IntrosectionController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;
        $data['abe'] = $lang->basic_extended;

        return view('admin.home.intro-section', $data);
    }

    public function update(Request $request, $langid)
    {
        $image = $request->image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $image2 = $request->image_2;
        $extImage2 = pathinfo($image2, PATHINFO_EXTENSION);

        $rules = [
            'intro_section_title' => 'required|max:25',
            'intro_section_text' => 'required|max:80',
            'intro_section_button_text' => 'nullable|max:15',
            'intro_section_button_url' => 'nullable|max:255',
            'intro_section_video_link' => 'nullable'
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

        if ($request->filled('image_2')) {
            $rules['image_2'] = [
                function ($attribute, $value, $fail) use ($extImage2, $allowedExts) {
                    if (!in_array($extImage2, $allowedExts)) {
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

        $bs = BS::where('language_id', $langid)->firstOrFail();
        $bs->intro_section_title = $request->intro_section_title;
        $bs->intro_section_text = $request->intro_section_text;
        $bs->intro_section_button_text = $request->intro_section_button_text;
        $bs->intro_section_button_url = $request->intro_section_button_url;
        $videoLink = $request->intro_section_video_link;
        if (strpos($videoLink, "&") != false) {
            $videoLink = substr($videoLink, 0, strpos($videoLink, "&"));
        }
        $bs->intro_section_video_link = $videoLink;

        if ($request->filled('image')) {
            @unlink('assets/front/img/' . $bs->intro_bg);
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/' . $filename);

            $bs->intro_bg = $filename;
        }

        $bs->save();

        $be = BasicExtended::where('language_id', $langid)->firstOrFail();
        if ($request->filled('image_2')) {
            @unlink('assets/front/img/' . $be->intro_bg2);
            $filename = uniqid() .'.'. $extImage2;
            @copy($image2, 'assets/front/img/' . $filename);

            $be->intro_bg2 = $filename;
        }
        $be->save();

        Session::flash('success', 'Informations updated successfully!');
        return "success";
    }
}

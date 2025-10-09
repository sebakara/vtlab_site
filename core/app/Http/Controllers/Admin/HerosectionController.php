<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtended;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BasicSetting as BS;
use App\Language;
use Validator;
use Session;

class HerosectionController extends Controller
{
    public function static(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;
        $data['abe'] = $lang->basic_extended;

        return view('admin.home.hero.static', $data);
    }

    public function update(Request $request, $langid)
    {
        $image = $request->image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $rules = [
            'hero_section_title' => 'nullable',
            'hero_section_title_font_size' => 'required|integer|digits_between:1,3',
            'hero_section_text' => 'nullable',
            'hero_section_text_font_size' => 'required|integer|digits_between:1,3',
            'hero_section_button_text' => 'nullable',
            'hero_section_button_text_font_size' => 'required|integer|digits_between:1,3',
            'hero_section_button_url' => 'nullable',
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

        $be = BasicExtended::where('language_id', $langid)->firstOrFail();
        $version = $be->theme_version;

        if ($version == 'gym' || $version == 'car' || $version == 'cleaning') {
            $rules['hero_section_bold_text'] = 'nullable';
            $rules['hero_section_bold_text_font_size'] = 'required|integer|digits_between:1,3';
        }

        if ($version == 'cleaning') {
            $rules['hero_section_bold_text_color'] = 'required';
        }

        if ($version == 'cleaning') {
            $rules['hero_section_text_font_size'] = 'nullable';
        }


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $bs = BS::where('language_id', $langid)->firstOrFail();
        $bs->hero_section_title = $request->hero_section_title;
        if ($version == 'gym' || $version == 'car' || $version == 'cleaning') {
            $bs->hero_section_bold_text = $request->hero_section_bold_text;
        }
        if ($version != 'cleaning') {
            $bs->hero_section_text = $request->hero_section_text;
        }
        $bs->hero_section_button_text = $request->hero_section_button_text;
        $bs->hero_section_button_url = $request->hero_section_button_url;
        if ($request->filled('image')) {
            @unlink('assets/front/img/' . $bs->hero_bg);
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/' . $filename);

            $bs->hero_bg = $filename;
        }
        $bs->save();


        $be->hero_section_title_font_size = $request->hero_section_title_font_size;
        if ($version == 'gym' || $version == 'car' || $version == 'cleaning') {
            $be->hero_section_bold_text_font_size = $request->hero_section_bold_text_font_size;
        }
        if ($version == 'cleaning') {
            $be->hero_section_bold_text_color = $request->hero_section_bold_text_color;
        }
        if ($version != 'cleaning') {
            $be->hero_section_text_font_size = $request->hero_section_text_font_size;
        }
        $be->hero_section_button_text_font_size = $request->hero_section_button_text_font_size;

        $be->save();

        Session::flash('success', 'Informations updated successfully!');
        return "success";
    }

    public function video(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;

        return view('admin.home.hero.video', $data);
    }

    public function videoupdate(Request $request, $langid)
    {
        $rules = [
            'video_link' => 'required|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $bs = BS::where('language_id', $langid)->firstOrFail();
        $videoLink = $request->video_link;
        if (strpos($videoLink, "&") != false) {
            $videoLink = substr($videoLink, 0, strpos($videoLink, "&"));
        }
        $bs->hero_section_video_link = $videoLink;
        $bs->save();

        Session::flash('success', 'Informations updated successfully!');
        return "success";
    }
}

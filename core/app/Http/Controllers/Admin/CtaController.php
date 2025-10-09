<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BasicSetting as BS;
use App\Language;
use Validator;
use Session;

class CtaController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;
        $data['abe'] = $lang->basic_extended;

        return view('admin.home.cta', $data);
    }

    public function update(Request $request, $langid)
    {
        $background = $request->background;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extBackground = pathinfo($background, PATHINFO_EXTENSION);

        $rules = [
            'cta_section_text' => 'required|max:80',
            'cta_section_button_text' => 'required|max:15',
            'cta_section_button_url' => 'required|max:255',
        ];

        if ($request->filled('background')) {
            $rules['background'] = [
                function ($attribute, $value, $fail) use ($extBackground, $allowedExts) {
                    if (!in_array($extBackground, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                }
            ];
        }

        $request->validate($rules);

        $bs = BS::where('language_id', $langid)->firstOrFail();
        $bs->cta_section_text = $request->cta_section_text;
        $bs->cta_section_button_text = $request->cta_section_button_text;
        $bs->cta_section_button_url = $request->cta_section_button_url;

        if ($request->filled('background')) {
            @unlink('assets/front/img/' . $bs->cta_bg);
            $filename = uniqid() .'.'. $extBackground;
            @copy($background, 'assets/front/img/' . $filename);
            $bs->cta_bg = $filename;
        }

        $bs->save();

        Session::flash('success', 'Texts updated successfully!');
        return back();
    }
}

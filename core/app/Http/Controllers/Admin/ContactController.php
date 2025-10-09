<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtra;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BasicSetting;
use App\Language;
use Session;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;
        $data['abex'] = $lang->basic_extra;

        return view('admin.contact', $data);
    }

    public function update(Request $request, $langid)
    {
        $request->validate([
            'contact_form_title' => 'required|max:255',
            'contact_form_subtitle' => 'required|max:255',
            'contact_addresses' => 'required',
            'contact_numbers' => 'required',
            'contact_mails' => 'required',
            'latitude' => 'nullable|max:255',
            'longitude' => 'nullable|max:255',
            'map_zoom' => 'nullable|max:255',
        ]);

        $bs = BasicSetting::where('language_id', $langid)->firstOrFail();
        $bs->contact_form_title = $request->contact_form_title;
        $bs->contact_form_subtitle = $request->contact_form_subtitle;
        $bs->save();

        $bex = BasicExtra::where('language_id', $langid)->firstOrFail();
        $bex->contact_addresses = $request->contact_addresses;
        $bex->contact_numbers = $request->contact_numbers;
        $bex->contact_mails = $request->contact_mails;
        $bex->latitude = $request->latitude;
        $bex->longitude = $request->longitude;
        $bex->map_zoom = $request->map_zoom ? $request->map_zoom : 0;
        $bex->save();

        Session::flash('success', 'Contact page updated successfully!');
        return back();
    }
}

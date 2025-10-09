<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtended;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Member;
use App\Language;
use App\BasicSetting as BS;
use Validator;
use Session;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;
        $data['members'] = Member::where('language_id', $data['lang_id'])->get();

        return view('admin.home.member.index', $data);
    }

    public function create()
    {
        return view('admin.home.member.create');
    }

    public function edit($id)
    {
        $data['member'] = Member::findOrFail($id);
        return view('admin.home.member.edit', $data);
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
            'image' => 'required',
            'name' => 'required|max:50',
            'rank' => 'required|max:50',
            'facebook' => 'nullable|max:50',
            'twitter' => 'nullable|max:50',
            'linkedin' => 'nullable|max:50',
            'instagram' => 'nullable|max:50',
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

        $member = new Member;
        $member->language_id = $request->language_id;
        $member->image = $request->member_image;
        $member->name = $request->name;
        $member->rank = $request->rank;
        $member->facebook = $request->facebook;
        $member->twitter = $request->twitter;
        $member->linkedin = $request->linkedin;
        $member->instagram = $request->instagram;

        if ($request->filled('image')) {
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/members/' . $filename);
            $member->image = $filename;
        }

        $member->save();

        Session::flash('success', 'Member added successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $image = $request->image;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extImage = pathinfo($image, PATHINFO_EXTENSION);

        $rules = [
            'name' => 'required|max:50',
            'rank' => 'required|max:50',
            'facebook' => 'nullable|max:50',
            'twitter' => 'nullable|max:50',
            'linkedin' => 'nullable|max:50',
            'instagram' => 'nullable|max:50',
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

        $member = Member::findOrFail($request->member_id);
        $member->name = $request->name;
        $member->rank = $request->rank;
        $member->facebook = $request->facebook;
        $member->twitter = $request->twitter;
        $member->linkedin = $request->linkedin;
        $member->instagram = $request->instagram;

        if ($request->filled('image')) {
            @unlink('assets/front/img/members/' . $member->image);
            $filename = uniqid() .'.'. $extImage;
            @copy($image, 'assets/front/img/members/' . $filename);
            $member->image = $filename;
        }

        $member->save();

        Session::flash('success', 'Member updated successfully!');
        return "success";
    }

    public function textupdate(Request $request, $langid)
    {
        $be = BasicExtended::firstOrFail();
        $version = $be->theme_version;

        if ($version == 'default' || $version == 'dark') {
            $background = $request->background;
            $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
            $extBackground = pathinfo($background, PATHINFO_EXTENSION);
        }

        $rules = [
            'team_section_title' => 'required|max:25',
            'team_section_subtitle' => 'required|max:80',
        ];

        if (($version == 'default' || $version == 'dark') && $request->filled('background')) {
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
        $bs->team_section_title = $request->team_section_title;
        $bs->team_section_subtitle = $request->team_section_subtitle;

        if (($version == 'default' || $version == 'dark') && $request->filled('background')) {
            @unlink('assets/front/img/'.$bs->team_bg);
            $filename = uniqid() .'.'. $extBackground;
            @copy($background, 'assets/front/img/' . $filename);
            $bs->team_bg = $filename;
        }

        $bs->save();

        Session::flash('success', 'Text & Background updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {

        $member = Member::findOrFail($request->member_id);
        @unlink('assets/front/img/members/' . $member->image);
        $member->delete();

        Session::flash('success', 'Member deleted successfully!');
        return back();
    }

    public function feature(Request $request)
    {
        $member = Member::find($request->member_id);
        $member->feature = $request->feature;
        $member->save();

        if ($request->feature == 1) {
            Session::flash('success', 'Featured successfully!');
        } else {
            Session::flash('success', 'Unfeatured successfully!');
        }

        return back();
    }
}

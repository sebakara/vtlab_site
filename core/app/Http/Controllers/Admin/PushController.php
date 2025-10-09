<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtra;
use App\Http\Controllers\Controller;
use App\Guest;
use App\Notifications\PushDemo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Notification;
use Validator;

class PushController extends Controller
{
    public function settings()
    {
        return view('admin.pushnotification.settings');
    }

    public function updateSettings(Request $request)
    {
        $icon = $request->icon;
        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $exticon = pathinfo($icon, PATHINFO_EXTENSION);

        $rules = [];

        if ($request->filled('icon')) {
            $rules['icon'] = [
                function ($attribute, $value, $fail) use ($exticon, $allowedExts) {
                    if (!in_array($exticon, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                }
            ];
        }

        $request->validate($rules);

        if ($request->filled('icon')) {
            $bexs = BasicExtra::all();

            foreach ($bexs as $key => $bex) {
                @unlink('assets/front/img/' . $bex->push_notification_icon);
                $filename = uniqid() . '.' . $exticon;
                @copy($icon, 'assets/front/img/' . $filename);
                $bex->push_notification_icon = $filename;
                $bex->save();
            }
        }

        if ($request->has('public_key') && $request->has('private_key')) {
            $arr = ['VAPID_PUBLIC_KEY' => $request->public_key,'VAPID_PRIVATE_KEY' => $request->private_key];
            setEnvironmentValue($arr);
            \Artisan::call('config:clear');
        }

        session()->flash('success', 'Push Notification icon updated!');
        return back();
    }

    public function send()
    {
        return view('admin.pushnotification.send');
    }

    public function push(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'button_url' => 'required',
            'button_text' => 'required'
        ]);

        $title = $request->title;
        $message = $request->message;
        $buttonText = $request->button_text;
        $buttonURL = $request->button_url;

        Notification::send(Guest::all(), new PushDemo($title, $message, $buttonText, $buttonURL));

        $request->session()->flash('success', 'Push notification sent');
        return redirect()->route('admin.pushnotification.send');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\BasicExtended;
use App\BasicExtra;
use App\BasicSetting;
use App\Home;
use App\Http\Controllers\Controller;
use App\Language;
use App\Service;
use App\Timezone;
use Artisan;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Validator;

class BasicController extends Controller
{
    public function fileManager() {
        return view('admin.basic.file-manager');
    }

    public function logo()
    {
        $data['abs'] = BasicSetting::first();

        return view('admin.basic.logo', $data);
    }

    public function updatelogo(Request $request)
    {
        $logo = $request->logo;
        $favicon = $request->favicon;
        $breadcrumb = $request->breadcrumb;

        $allowedExts = array('jpg', 'png', 'jpeg', 'svg');
        $extLogo = pathinfo($logo, PATHINFO_EXTENSION);
        $extFav = pathinfo($favicon, PATHINFO_EXTENSION);
        $extBread = pathinfo($breadcrumb, PATHINFO_EXTENSION);

        $rules = [];

        if ($request->filled('logo')) {
            $rules['logo'] = [
                function ($attribute, $value, $fail) use ($extLogo, $allowedExts) {
                    if (!in_array($extLogo, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                }
            ];
        }

        if ($request->filled('favicon')) {
            $rules['favicon'] = [
                function ($attribute, $value, $fail) use ($extFav, $allowedExts) {
                    if (!in_array($extFav, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                },
            ];
        }

        if ($request->filled('breadcrumb')) {
            $rules['breadcrumb'] = [
                function ($attribute, $value, $fail) use ($extBread, $allowedExts) {
                    if (!in_array($extBread, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, svg image is allowed");
                    }
                },
            ];
        }

        $request->validate($rules);

        if ($request->filled('logo')) {

            $bss = BasicSetting::all();
            // only remove the the previous image, if it is not the same as default image or the first image is being updated

            foreach ($bss as $key => $bs) {
                @unlink('assets/front/img/' . $bs->logo);
                $filename = uniqid() .'.'. $extLogo;
                @copy($logo, 'assets/front/img/' . $filename);

                $bs->logo = $filename;
                $bs->save();
            }

        }

        if ($request->filled('favicon')) {

            $bss = BasicSetting::all();
            // only remove the the previous image, if it is not the same as default image or the first image is being updated

            foreach ($bss as $key => $bs) {
                @unlink('assets/front/img/' . $bs->favicon);
                $filename = uniqid() .'.'. $extFav;
                @copy($favicon, 'assets/front/img/' . $filename);

                $bs->favicon = $filename;
                $bs->save();
            }

        }

        if ($request->filled('breadcrumb')) {

            $bss = BasicSetting::all();
            // only remove the the previous image, if it is not the same as default image or the first image is being updated

            foreach ($bss as $key => $bs) {
                @unlink('assets/front/img/' . $bs->breadcrumb);
                $filename = uniqid() .'.'. $extBread;
                @copy($breadcrumb, 'assets/front/img/' . $filename);

                $bs->breadcrumb = $filename;
                $bs->save();
            }

        }

        $request->session()->flash('success', 'Images updated successfully!');
        return back();
    }


    public function featuresettings()
    {
        $data['abex'] = BasicExtra::first();

        return view('admin.basic.features', $data);
    }

    public function updatefeatrue(Request $request)
    {

        $bexs = BasicExtra::all();

        foreach ($bexs as $key => $bex) {
            $bex->is_user_panel = $request->is_user_panel;
            $bex->save();
        }

        Session::flash('success', 'Updated successfully!');
        return back();
    }

    public function updatethemeversion(Request $request)
    {
        $bes = BasicExtended::all();

        foreach ($bes as $key => $be) {
            $be->theme_version = $request->theme_version;
            $be->save();
        }

        Session::flash('success', "$request->theme_version version activated successfully!");
        return "success";
    }


    public function homeSettings(Request $request)
    {
        $data['abs'] = BasicSetting::first();
        $data['abe'] = BasicExtended::first();
        $data['abex'] = BasicExtra::first();
        $data['themes'] = Home::all();

        return view('admin.themeHome.settings', $data);
    }

    public function updateHomeSettings(Request $request)
    {
        $bss = BasicSetting::all();
        $bes = BasicExtended::all();
        $bexs = BasicExtra::all();

        foreach ($bss as $key => $bs) {
            $bs->home_version = $request->home_version;
            $bs->save();
        }
        foreach ($bes as $key => $be) {
            $be->theme_version = $request->theme_version;
            $be->save();
        }
        foreach ($bexs as $key => $bex) {
            $bex->home_page_pagebuilder = $request->home_page_pagebuilder;
            $bex->save();
        }

        Session::flash('success', "Settings updated successfully!");
        return back();
    }

    public function preloader(Request $request)
    {
        return view('admin.basic.preloader');
    }

    public function updatepreloader(Request $request)
    {
        $preloader = $request->preloader;
        $allowedExts = array('jpg', 'png', 'jpeg', 'gif', 'svg');
        $extPreloader = pathinfo($preloader, PATHINFO_EXTENSION);

        $rules = [
            'preloader_status' => 'required'
        ];

        if ($request->filled('preloader')) {
            $rules['preloader'] = [
                function ($attribute, $value, $fail) use ($extPreloader, $allowedExts) {
                    if (!in_array($extPreloader, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg, gif, svg images are allowed");
                    }
                }
            ];
        }

        $request->validate($rules);



        if ($request->filled('preloader')) {
            $filename = uniqid() .'.'. $extPreloader;
            @copy($preloader, 'assets/front/img/' . $filename);
        }

        $bexs = BasicExtra::all();
        foreach ($bexs as $key => $bex) {
            if ($request->filled('preloader')) {
                @unlink('assets/front/img/' . $bex->preloader);
                $bex->preloader = $filename;
            }

            $bex->preloader_status = $request->preloader_status;
            $bex->save();
        }
        Session::flash('success', 'Preloader updated successfully.');
        return back();
    }


    public function basicinfo(Request $request)
    {
        $data['abs'] = BasicSetting::first();
        $data['abe'] = BasicExtended::first();
        $data['abx'] = BasicExtra::first();
        $data['timezones'] = Timezone::all();

        return view('admin.basic.basicinfo', $data);
    }

    public function updatebasicinfo(Request $request)
    {
        $rules = [
            'website_title' => 'required',
            'base_color' => 'required',
            'secondary_base_color' => 'required',
            'hero_area_overlay_color' => 'required',
            'hero_area_overlay_opacity' => 'required|numeric|max:1|min:0',
            'breadcrumb_area_overlay_color' => 'required',
            'breadcrumb_area_overlay_opacity' => 'required|numeric|max:1|min:0',
            'base_currency_symbol' => 'required',
            'base_currency_symbol_position' => 'required',
            'base_currency_text' => 'required',
            'base_currency_text_position' => 'required',
            'base_currency_rate' => 'required|numeric',
        ];

        $be = BasicExtended::first();

        if ($be->theme_version == 'cleaning' || $be->theme_version == 'logistic') {
            $rules["hero_area_overlay_color"] = 'nullable';
            $rules["hero_area_overlay_opacity"] = 'nullable';
        }

        if ($be->theme_version == 'dark' || $be->theme_version == 'gym' || $be->theme_version == 'car' || $be->theme_version == 'construction' || $be->theme_version == 'lawyer') {
            $rules["secondary_base_color"] = 'nullable';
        }


        $request->validate($rules);

        $bss = BasicSetting::all();
        foreach ($bss as $key => $bs) {
            $bs->website_title = $request->website_title;
            $bs->base_color = $request->base_color;

            if ($be->theme_version != 'dark' && $be->theme_version != 'gym' && $be->theme_version != 'car' && $be->theme_version != 'construction' && $be->theme_version != 'lawyer') {
                $bs->secondary_base_color = $request->secondary_base_color;
            }


            $bs->save();
        }


        $bes = BasicExtended::all();
        foreach ($bes as $key => $be) {
            if ($be->theme_version != 'cleaning' && $be->theme_version != 'logistic') {
                $be->hero_overlay_color = $request->hero_area_overlay_color;
                $be->hero_overlay_opacity = $request->hero_area_overlay_opacity;
            }


            $be->breadcrumb_overlay_color = $request->breadcrumb_area_overlay_color;
            $be->breadcrumb_overlay_opacity = $request->breadcrumb_area_overlay_opacity;
            $be->save();
        }


        $bexs = BasicExtra::all();
        foreach ($bexs as $key => $bex) {
            $bex->base_currency_symbol = $request->base_currency_symbol;
            $bex->base_currency_symbol_position = $request->base_currency_symbol_position;
            $bex->base_currency_text = $request->base_currency_text;
            $bex->base_currency_text_position = $request->base_currency_text_position;
            $bex->base_currency_rate = $request->base_currency_rate;
            $bex->timezone = $request->timezone;
            $bex->save();
        }

        // set timezone in .env
        if ($request->has('timezone') && $request->filled('timezone')) {
            $arr = ['TIMEZONE' => $request->timezone];
            setEnvironmentValue($arr);
            \Artisan::call('config:clear');
        }

        Session::flash('success', 'Basic informations updated successfully!');
        return back();
    }

    public function seo(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abe'] = $lang->basic_extended;

        return view('admin.basic.seo', $data);
    }

    public function updateseo(Request $request, $langid)
    {
        $be = BasicExtended::where('language_id', $langid)->firstOrFail();
        $be->home_meta_keywords = $request->home_meta_keywords;
        $be->home_meta_description = $request->home_meta_description;
        $be->services_meta_keywords = $request->services_meta_keywords;
        $be->services_meta_description = $request->services_meta_description;
        $be->packages_meta_keywords = $request->packages_meta_keywords;
        $be->packages_meta_description = $request->packages_meta_description;
        $be->portfolios_meta_keywords = $request->portfolios_meta_keywords;
        $be->portfolios_meta_description = $request->portfolios_meta_description;
        $be->team_meta_keywords = $request->team_meta_keywords;
        $be->team_meta_description = $request->team_meta_description;
        $be->career_meta_keywords = $request->career_meta_keywords;
        $be->career_meta_description = $request->career_meta_description;
        $be->calendar_meta_keywords = $request->calendar_meta_keywords;
        $be->calendar_meta_description = $request->calendar_meta_description;
        $be->gallery_meta_keywords = $request->gallery_meta_keywords;
        $be->gallery_meta_description = $request->gallery_meta_description;
        $be->faq_meta_keywords = $request->faq_meta_keywords;
        $be->faq_meta_description = $request->faq_meta_description;
        $be->blogs_meta_keywords = $request->blogs_meta_keywords;
        $be->blogs_meta_description = $request->blogs_meta_description;
        $be->rss_meta_keywords = $request->rss_meta_keywords;
        $be->rss_meta_description = $request->rss_meta_description;
        $be->contact_meta_keywords = $request->contact_meta_keywords;
        $be->contact_meta_description = $request->contact_meta_description;
        $be->quote_meta_keywords = $request->quote_meta_keywords;
        $be->quote_meta_description = $request->quote_meta_description;
        $be->products_meta_keywords = $request->products_meta_keywords;
        $be->products_meta_description = $request->products_meta_description;
        $be->cart_meta_keywords = $request->cart_meta_keywords;
        $be->cart_meta_description = $request->cart_meta_description;
        $be->checkout_meta_keywords = $request->checkout_meta_keywords;
        $be->checkout_meta_description = $request->checkout_meta_description;
        $be->login_meta_keywords = $request->login_meta_keywords;
        $be->login_meta_description = $request->login_meta_description;
        $be->register_meta_keywords = $request->register_meta_keywords;
        $be->register_meta_description = $request->register_meta_description;
        $be->forgot_meta_keywords = $request->forgot_meta_keywords;
        $be->forgot_meta_description = $request->forgot_meta_description;
        $be->events_meta_keywords = $request->events_meta_keywords;
        $be->events_meta_description = $request->events_meta_description;
        $be->causes_meta_keywords = $request->causes_meta_keywords;
        $be->causes_meta_description = $request->causes_meta_description;
        $be->save();

        Session::flash('success', 'SEO informations updated successfully!');
        return back();
    }

    public function support(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;

        return view('admin.basic.support', $data);
    }

    public function updatesupport(Request $request, $langid)
    {
        $request->validate([
            'support_email' => 'required|email|max:100',
            'support_phone' => 'required|max:30',
        ]);

        $bs = BasicSetting::where('language_id', $langid)->firstOrFail();
        $bs->support_email = $request->support_email;
        $bs->support_phone = $request->support_phone;
        $bs->save();

        Session::flash('success', 'Support Informations updated successfully!');
        return back();
    }

    public function heading(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abs'] = $lang->basic_setting;
        $data['abe'] = $lang->basic_extended;
        $data['abex'] = $lang->basic_extra;

        return view('admin.basic.headings', $data);
    }

    public function updateheading(Request $request, $langid)
    {
        $request->validate([
            'service_title' => 'nullable|max:30',
            'service_subtitle' => 'nullable|max:40',
            'career_title' => 'nullable|max:30',
            'career_subtitle' => 'nullable|max:40',
            'event_calendar_title' => 'nullable|max:30',
            'event_calendar_subtitle' => 'nullable|max:40',
            'service_details_title' => 'nullable|max:30',
            'portfolio_title' => 'nullable|max:30',
            'portfolio_subtitle' => 'nullable|max:40',
            'portfolio_details_title' => 'nullable|max:40',
            'blog_details_title' => 'nullable|max:30',
            'rss_details_title' => 'nullable|max:30',
            'contact_title' => 'nullable|max:30',
            'contact_subtitle' => 'nullable|max:40',
            'gallery_title' => 'nullable|max:30',
            'gallery_subtitle' => 'nullable|max:40',
            'team_title' => 'nullable|max:30',
            'team_subtitle' => 'nullable|max:40',
            'faq_title' => 'nullable|max:30',
            'faq_subtitle' => 'nullable|max:40',
            'pricing_title' => 'nullable|max:30',
            'pricing_subtitle' => 'nullable|max:40',
            'blog_title' => 'nullable|max:30',
            'blog_subtitle' => 'nullable|max:40',
            'rss_title' => 'nullable|max:30',
            'rss_subtitle' => 'nullable|max:40',
            'quote_title' => 'nullable|max:30',
            'quote_subtitle' => 'nullable|max:40',
            'error_title' => 'nullable|max:30',
            'error_subtitle' => 'nullable|max:40',
            'product_title' => 'nullable|max:30',
            'product_subtitle' => 'nullable|max:40',
            'product_details_title' => 'nullable|max:30',
            // 'product_details_subtitle' => 'nullable|max:40',
            'cart_title' => 'nullable|max:30',
            'cart_subtitle' => 'nullable|max:40',
            'checkout_title' => 'nullable|max:30',
            'checkout_subtitle' => 'nullable|max:40',
            'event_title' => 'nullable|max:30',
            'event_subtitle' => 'nullable|max:40',
            'cause_title' => 'nullable|max:30',
            'cause_subtitle' => 'nullable|max:40',
            'knowledgebase_title' => 'nullable|max:70',
            'knowledgebase_subtitle' => 'nullable|max:70',
            'knowledgebase_details_title' => 'nullable|max:70',
            'client_feedback_title' => 'nullable|max:70',
            'client_feedback_subtitle' => 'nullable|max:70'
        ]);

        $bs = BasicSetting::where('language_id', $langid)->firstOrFail();
        $be = BasicExtended::where('language_id', $langid)->firstOrFail();
        $bex = BasicExtra::where('language_id', $langid)->firstOrFail();

        $bs->service_title = $request->service_title;
        $bs->service_subtitle = $request->service_subtitle;
        $bs->service_details_title = $request->service_details_title;
        $bs->portfolio_title = $request->portfolio_title;
        $bs->portfolio_subtitle = $request->portfolio_subtitle;
        $bs->portfolio_details_title = $request->portfolio_details_title;
        $bs->blog_details_title = $request->blog_details_title;
        $bs->contact_title = $request->contact_title;
        $bs->contact_subtitle = $request->contact_subtitle;
        $bs->gallery_title = $request->gallery_title;
        $bs->gallery_subtitle = $request->gallery_subtitle;
        $bs->team_title = $request->team_title;
        $bs->team_subtitle = $request->team_subtitle;
        $bs->faq_title = $request->faq_title;
        $bs->faq_subtitle = $request->faq_subtitle;
        $bs->blog_title = $request->blog_title;
        $bs->blog_subtitle = $request->blog_subtitle;
        $bs->quote_title = $request->quote_title;
        $bs->quote_subtitle = $request->quote_subtitle;
        $bs->error_title = $request->error_title;
        $bs->error_subtitle = $request->error_subtitle;
        $bs->event_title = $request->event_title;
        $bs->event_subtitle = $request->event_subtitle;
        $bs->event_details_title = $request->event_details_title;
        $bs->cause_title = $request->cause_title;
        $bs->cause_subtitle = $request->cause_subtitle;
        $bs->cause_details_title = $request->cause_details_title;
        $bs->save();


        $be->pricing_title = $request->pricing_title;
        $be->pricing_subtitle = $request->pricing_subtitle;
        $be->career_title = $request->career_title;
        $be->career_subtitle = $request->career_subtitle;
        $be->event_calendar_title = $request->event_calendar_title;
        $be->event_calendar_subtitle = $request->event_calendar_subtitle;
        $be->rss_title = $request->rss_title;
        $be->rss_subtitle = $request->rss_subtitle;
        $be->rss_details_title = $request->blog_details_title;
        $be->product_title = $request->product_title;
        $be->product_subtitle = $request->product_subtitle;
        $be->product_details_title = $request->product_details_title;
        // $be->product_details_subtitle = $request->product_details_subtitle;
        $be->cart_title = $request->cart_title;
        $be->cart_subtitle = $request->cart_subtitle;
        $be->checkout_title = $request->checkout_title;
        $be->checkout_subtitle = $request->checkout_subtitle;
        $be->save();

        $bex->course_title = $request->course_title;
        $bex->course_subtitle = $request->course_subtitle;
        $bex->course_details_title = $request->course_details_title;
        $bex->knowledgebase_title = $request->knowledgebase_title;
        $bex->knowledgebase_subtitle = $request->knowledgebase_subtitle;
        $bex->knowledgebase_details_title = $request->knowledgebase_details_title;
        $bex->client_feedback_title = $request->client_feedback_title;
        $bex->client_feedback_subtitle = $request->client_feedback_subtitle;
        $bex->save();

        Session::flash('success', 'Page title & subtitles updated successfully!');
        return back();
    }

    public function script()
    {
        return view('admin.basic.scripts');
    }

    public function updatescript(Request $request)
    {

        $bss = BasicSetting::all();

        foreach ($bss as $bs) {
            $bs->tawk_to_script = $request->tawk_to_script;
            $bs->is_tawkto = $request->is_tawkto;
            $bs->is_disqus = $request->is_disqus;
            $bs->disqus_script = $request->disqus_script;
            $bs->google_analytics_script = $request->google_analytics_script;
            $bs->is_analytics = $request->is_analytics;
            $bs->appzi_script = $request->appzi_script;
            $bs->is_appzi = $request->is_appzi;
            $bs->addthis_script = $request->addthis_script;
            $bs->is_addthis = $request->is_addthis;
            $bs->is_recaptcha = $request->is_recaptcha;
            $bs->google_recaptcha_site_key = $request->google_recaptcha_site_key;
            $bs->google_recaptcha_secret_key = $request->google_recaptcha_secret_key;
            $bs->save();
        }


        $bes = BasicExtended::all();
        foreach ($bes as $key => $be) {
            $be->facebook_pexel_script = $request->facebook_pexel_script;
            $be->is_facebook_pexel = $request->is_facebook_pexel;
            $be->save();
        }

        $bexs = BasicExtra::all();
        foreach ($bexs as $key => $bex) {
            $bex->is_facebook_login = $request->is_facebook_login;
            $bex->facebook_app_id = $request->facebook_app_id;
            $bex->facebook_app_secret = $request->facebook_app_secret;

            $bex->is_google_login = $request->is_google_login;
            $bex->google_client_id = $request->google_client_id;
            $bex->google_client_secret = $request->google_client_secret;

            $bex->is_whatsapp = $request->is_whatsapp;
            $bex->whatsapp_number = $request->whatsapp_number;
            $bex->whatsapp_header_title = $request->whatsapp_header_title;
            $bex->whatsapp_popup_message = $request->whatsapp_popup_message;
            $bex->whatsapp_popup = $request->whatsapp_popup;

            $bex->save();
        }
        Session::flash('success', 'Scripts updated successfully!');
        return back();
    }

    public function maintainance()
    {
        return view('admin.basic.maintainance');
    }

    public function updatemaintainance(Request $request)
    {
        $maintenance = $request->maintenance;
        $allowedExts = array('jpg', 'png', 'jpeg');
        $extLogo = pathinfo($maintenance, PATHINFO_EXTENSION);

        $rules = [];

        if ($request->filled('maintenance')) {
            $rules['maintenance'] = [
                function ($attribute, $value, $fail) use ($extLogo, $allowedExts) {
                    if (!in_array($extLogo, $allowedExts)) {
                        return $fail("Only png, jpg, jpeg image is allowed");
                    }
                }
            ];
        }

        $request->validate($rules);

        if ($request->filled('maintenance')) {
            @unlink('assets/front/img/maintainance.png');
            @copy($maintenance, 'assets/front/img/maintainance.png');
        }

        $bss = BasicSetting::all();
        foreach ($bss as $bs) {
            $bs->maintainance_text = $request->maintainance_text;
            $bs->maintainance_mode = $request->maintainance_mode;
            $bs->secret_path = $request->secret_path;
            $bs->save();
        }


        $down = "down";
        if ($request->filled('secret_path')) {
            $down .= " --secret=" . $request->secret_path;
        }

        if ($request->maintainance_mode == 1) {
            @unlink('core/storage/framework/down');
            Artisan::call($down);
        } else {
            Artisan::call('up');
        }

        Session::flash('success', 'Maintanance mode & page updated successfully!');
        return back();
    }


    public function sections(Request $request)
    {
        $data['abs'] = BasicSetting::first();
        $data['abe'] = BasicExtended::first();

        return view('admin.home.sections', $data);
    }

    public function updatesections(Request $request)
    {
        $be = BasicExtended::select('theme_version')->first();
        $bss = BasicSetting::all();

        foreach ($bss as $key => $bs) {
            $bs->feature_section = $request->feature_section;

            if ($be->theme_version != 'ecommerce') {
                $bs->intro_section = $request->intro_section;
                $bs->service_section = $request->service_section;
                $bs->approach_section = $request->approach_section;
                $bs->statistics_section = $request->statistics_section;
                $bs->portfolio_section = $request->portfolio_section;
                $bs->testimonial_section = $request->testimonial_section;
                $bs->team_section = $request->team_section;
                $bs->call_to_action_section = $request->call_to_action_section;
            }

            if ($be->theme_version == 'ecommerce') {
                $bs->newsletter_section = $request->newsletter_section;
            }

            $bs->news_section = $request->news_section;
            $bs->partner_section = $request->partner_section;
            $bs->top_footer_section = $request->top_footer_section;
            $bs->copyright_section = $request->copyright_section;
            $bs->save();
        }
        
        $bes = BasicExtended::all();
        foreach ($bes as $key => $be) {
            if ($be->theme_version != 'ecommerce') {
                $be->pricing_section = $request->pricing_section;
            }
            if ($be->theme_version == 'ecommerce') {
                $be->categories_section = $request->categories_section;
                $be->featured_products_section = $request->featured_products_section;
                $be->category_products_section = $request->category_products_section;
            }
            $be->save();
        }
        
        Session::flash('success', 'Sections customized successfully!');
        return back();
    }

    public function cookiealert(Request $request)
    {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;
        $data['abe'] = $lang->basic_extended;

        return view('admin.basic.cookie', $data);
    }

    public function updatecookie(Request $request, $langid)
    {
        $request->validate([
            'cookie_alert_status' => 'required',
            'cookie_alert_text' => 'required',
            'cookie_alert_button_text' => 'required|max:25',
        ]);

        $be = BasicExtended::where('language_id', $langid)->firstOrFail();
        $be->cookie_alert_status = $request->cookie_alert_status;
        $be->cookie_alert_text = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->cookie_alert_text);
        $be->cookie_alert_button_text = $request->cookie_alert_button_text;
        $be->save();

        Session::flash('success', 'Cookie alert updated successfully!');
        return back();
    }
}

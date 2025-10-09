<?php

namespace App\Http\Controllers\Front;

use App\Donation;
use App\DonationDetail;
use App\Event;
use App\EventCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\BasicSetting as BS;
use App\BasicExtended as BE;
use App\Slider;
use App\Scategory;
use App\Jcategory;
use App\Portfolio;
use App\Feature;
use App\Point;
use App\Statistic;
use App\Testimonial;
use App\Gallery;
use App\GalleryCategory;
use App\Faq;
use App\Page;
use App\Member;
use App\Blog;
use App\Partner;
use App\Service;
use App\Job;
use App\Archive;
use App\Article;
use App\ArticleCategory;
use App\Bcategory;
use App\Subscriber;
use App\Quote;
use App\Language;
use App\Package;
use App\PackageOrder;
use App\Admin;
use App\BasicExtra;
use App\CalendarEvent;
use App\FAQCategory;
use App\Home;
use App\Mail\ContactMail;
use App\Mail\OrderPackage;
use App\Mail\OrderQuote;
use App\OfflineGateway;
use App\PackageCategory;
use App\PackageInput;
use App\PaymentGateway;
use App\Pcategory;
use App\Product;
use App\QuoteInput;
use App\RssFeed;
use App\RssPost;
use App\Subscription;
use Validator;
use Config;
use Mail;
use PDF;
use Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Payment\MyFatoorahController;
use App\Http\Controllers\Payment\product\MyFatoorahController as ShopMyFatoorahController;
use App\Http\Controllers\Payment\Course\MyFatoorahController as CourseMyFatoorahController;
use App\Http\Controllers\Payment\causes\MyFatoorahController as CauseEventMyFatoorahController;

class FrontendController extends Controller
{
    public function __construct()
    {
        $bs = BS::first();
        $be = BE::first();

        Config::set('captcha.sitekey', $bs->google_recaptcha_site_key);
        Config::set('captcha.secret', $bs->google_recaptcha_secret_key);
    }

    public function index()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['currentLang'] = $currentLang;

        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;
        $lang_id = $currentLang->id;

        $data['sliders'] = Slider::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
        $data['features'] = Feature::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
        $version = $be->theme_version;

        // if home page page builder is disabled
        if ($bex->home_page_pagebuilder == 0) {
            $data['portfolios'] = Portfolio::where('language_id', $lang_id)->where('feature', 1)->orderBy('serial_number', 'ASC')->limit(10)->get();
            $data['points'] = Point::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
            $data['statistics'] = Statistic::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
            $data['testimonials'] = Testimonial::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
            $data['faqs'] = Faq::orderBy('serial_number', 'ASC')->get();
            $data['members'] = Member::where('language_id', $lang_id)->where('feature', 1)->get();
            $data['blogs'] = Blog::where('language_id', $lang_id)->orderBy('id', 'DESC')->limit(6)->get();
            $data['partners'] = Partner::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();
            $data['packages'] = Package::where('language_id', $lang_id)->where('feature', 1)->orderBy('serial_number', 'ASC')->get();
            $data['scategories'] = Scategory::where('language_id', $lang_id)->where('feature', 1)->where('status', 1)->orderBy('serial_number', 'ASC')->get();
            if (!serviceCategory()) {
                $data['services'] = Service::where('language_id', $lang_id)->where('feature', 1)->orderBy('serial_number', 'ASC')->get();
            }
        }
        // if home page page builder is disabled
        else {
            $data['home'] = Home::where('theme', $be->theme_version)->where('language_id', $currentLang->id)->first();
        }

        if ($version == 'gym') {
            if ($bex->home_page_pagebuilder == 1) {
                return view('front.gym.index', $data);
            } else {
                return view('front.gym.index1', $data);
            }
        } elseif ($version == 'car') {
            if ($bex->home_page_pagebuilder == 1) {
                return view('front.car.index', $data);
            } else {
                return view('front.car.index1', $data);
            }
        } elseif ($version == 'cleaning') {
            if ($bex->home_page_pagebuilder == 1) {
                return view('front.cleaning.index', $data);
            } else {
                return view('front.cleaning.index1', $data);
            }
        } elseif ($version == 'construction') {
            if ($bex->home_page_pagebuilder == 1) {
                return view('front.construction.index', $data);
            } else {
                return view('front.construction.index1', $data);
            }
        } elseif ($version == 'logistic') {
            if ($bex->home_page_pagebuilder == 1) {
                return view('front.logistic.index', $data);
            } else {
                return view('front.logistic.index1', $data);
            }
        } elseif ($version == 'lawyer') {
            if ($bex->home_page_pagebuilder == 1) {
                return view('front.lawyer.index', $data);
            } else {
                return view('front.lawyer.index1', $data);
            }
        } elseif ($version == 'ecommerce') {
            $data['fcategories'] = Pcategory::where('status', 1)->where('language_id', $currentLang->id)->where('is_feature', 1)->get();
            $data['hcategories'] = Pcategory::where('status', 1)->where('language_id', $currentLang->id)->where('products_in_home', 1)->get();
            $data['fproducts'] = Product::where('status', 1)->where('is_feature', 1)->where('language_id', $currentLang->id)->orderBy('id', 'DESC')->limit(10)->get();
            $data['products'] = Product::where('status', 1)->where('language_id', $currentLang->id)->orderBy('id', 'DESC')->limit(10)->get();
            if ($bex->home_page_pagebuilder == 1) {
                return view('front.ecommerce.index', $data);
            } else {
                return view('front.ecommerce.index1', $data);
            }
        } elseif ($version == 'default' || $version == 'dark') {
            if ($bex->home_page_pagebuilder == 1) {
                return view('front.default.index', $data);
            } else {
                return view('front.default.index1', $data);
            }
        }
    }

    public function services(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['currentLang'] = $currentLang;
        $be = $currentLang->basic_extended;


        $category = $request->category;
        $term = $request->term;

        if (!empty($category)) {
            $data['category'] = Scategory::findOrFail($category);
        }

        $data['services'] = Service::when($category, function ($query, $category) {
            return $query->where('scategory_id', $category);
        })->when($term, function ($query, $term) {
            return $query->where('title', 'like', '%' . $term . '%');
        })->when($currentLang, function ($query, $currentLang) {
            return $query->where('language_id', $currentLang->id);
        })->orderBy('serial_number', 'ASC')->paginate(6);


        $version = $be->theme_version;

        if ($version == 'gym') {
            return view('front.gym.services', $data);
        } elseif ($version == 'car') {
            return view('front.car.services', $data);
        } elseif ($version == 'cleaning') {
            return view('front.cleaning.services', $data);
        } elseif ($version == 'construction') {
            return view('front.construction.services', $data);
        } elseif ($version == 'logistic') {
            return view('front.logistic.services', $data);
        } elseif ($version == 'lawyer') {
            return view('front.lawyer.services', $data);
        } elseif ($version == 'default' || $version == 'dark' || $version == 'ecommerce') {
            $data['version'] = $version == 'dark' ? 'default' : $version;
            return view('front.services', $data);
        }
    }

    public function packages()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['currentLang'] = $currentLang;
        $be = $currentLang->basic_extended;

        $data['categories'] = PackageCategory::where('language_id', $currentLang->id)
            ->where('status', 1)->orderBy('serial_number', 'ASC')->get();

        $data['packages'] = Package::when($currentLang, function ($query, $currentLang) {
            return $query->where('language_id', $currentLang->id);
        })->orderBy('serial_number', 'ASC')->get();

        if (Auth::check()) {
            $data['activeSub'] = Subscription::where('user_id', Auth::user()->id)->where('status', 1);
        }

        $version = $be->theme_version;

        if ($version == 'gym') {
            return view('front.gym.packages', $data);
        } elseif ($version == 'car') {
            return view('front.car.packages', $data);
        } elseif ($version == 'cleaning') {
            return view('front.cleaning.packages', $data);
        } elseif ($version == 'construction') {
            return view('front.construction.packages', $data);
        } elseif ($version == 'logistic') {
            return view('front.logistic.packages', $data);
        } elseif ($version == 'lawyer') {
            return view('front.lawyer.packages', $data);
        } elseif ($version == 'default' || $version == 'dark' || $version == 'ecommerce') {
            $data['version'] = $version == 'dark' ? 'default' : $version;
            return view('front.packages', $data);
        }
    }

    public function causes(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['currentLang'] = $currentLang;
        $data['bs'] = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;
        if ($bex->is_donation == 0) {
            return back();
        }
        $be = $currentLang->basic_extended;
        $causes = Donation::query()
            ->where('lang_id', $currentLang->id)
            ->orderByDesc('id')
            ->paginate(6);
        $causes->map(function ($cause) use ($bex) {
            $raised_amount = DonationDetail::query()
                ->where('donation_id', '=', $cause->id)
                ->where('status', '=', "Success")
                ->sum('amount');
            $goal_percentage = $raised_amount > 0 ? (($raised_amount / $cause->goal_amount) * 100) : 0;
            $cause['raised_amount'] = $raised_amount > 0 ? round($raised_amount, 2) : 0;
            $cause['goal_percentage'] = round($goal_percentage, 1);
        });
        $data['causes'] = $causes;
        $data['bex'] = $bex;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;
        return view('front.causes', $data);
    }
    public function causeDetails($slug)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['currentLang'] = $currentLang;
        $data['bs'] = $currentLang->basic_setting;
        $bex = $currentLang->basic_extra;
        if ($bex->is_donation == 0) {
            return back();
        }
        $be = $currentLang->basic_extended;
        $version = $be->theme_version;
        $cause = Donation::where('slug', $slug)->firstOrFail();
        $raised_amount = DonationDetail::query()
            ->where('donation_id', '=', $cause->id)
            ->where('status', '=', "Success")
            ->sum('amount');
        $goal_percentage = $raised_amount > 0 ? (($raised_amount / $cause->goal_amount) * 100) : 0;
        $cause['raised_amount'] = $raised_amount > 0 ? round($raised_amount, 2) : 0;
        $cause['goal_percentage'] = round($goal_percentage, 1);
        $data['custom_amounts'] = explode(',', $cause->custom_amount);
        $online = PaymentGateway::where('status', 1)->get();
        $offline = OfflineGateway::where('donation_checkout_status', 1)->orderBy('serial_number', 'ASC')->get();
        $data['offline'] = $offline;
        $data['payment_gateways'] = $online->mergeRecursive($offline);
        $data['cause'] = $cause;
        $data['bex'] = $bex;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;
        $stripeData = PaymentGateway::whereKeyword('stripe')->first();
        $stripe = $stripeData->convertAutoData();
        $data['stripe_key'] =  $stripe['key'];
        return view('front.cause-details', $data);
    }
    public function paymentInstruction(Request $request)
    {
        $offline = OfflineGateway::where('name', $request->name)->select('short_description', 'instructions', 'is_receipt')->first();
        return response()->json(['description' => $offline->short_description, 'instructions' => replaceBaseUrl($offline->instructions), 'is_receipt' => $offline->is_receipt]);
    }
    public function events(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['bex'] = $currentLang->basic_extra;
        $data['currentLang'] = $currentLang;
        $be = $currentLang->basic_extended;
        $data['bs'] = $currentLang->basic_setting;
        $data['event_categories'] = EventCategory::where('lang_id', $currentLang->id)->where('status', 1)->select('id', 'name')->get();
        $data['events'] = Event::with('eventCategories')
            ->when($request->title, function ($q) use ($request) {
                return $q->where('title', 'like', '%' . $request->title . '%');
            })->when($request->location, function ($q) use ($request) {
                return $q->where('venue_location', 'like', '%' . $request->location . '%');
            })->when($request->category, function ($q) use ($request) {
                return $q->where('cat_id', $request->category);
            })->when($request->date, function ($q) use ($request) {
                return $q->where('date', $request->date);
            })
            ->where('lang_id', $currentLang->id)
            ->orderBy('id', 'DESC')
            ->paginate(6);
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;
        return view('front.events', $data);
    }
    public function eventDetails($slug)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['bex'] = $currentLang->basic_extra;
        $data['bs'] = $currentLang->basic_setting;
        $data['currentLang'] = $currentLang;
        $be = $currentLang->basic_extended;
        $version = $be->theme_version;
        $event = Event::with('eventCategories')->where('slug', $slug)->firstOrFail();
        $data['event'] = $event;
        $online = PaymentGateway::where('status', 1)->get();
        $offline = OfflineGateway::where('event_checkout_status', 1)->orderBy('serial_number', 'ASC')->get();
        $data['offline'] = $offline;
        $data['payment_gateways'] = $online->mergeRecursive($offline);
        $data["moreEvents"] = Event::with('eventCategories')->where(function ($q) use ($event) {
            $q->where('id', '!=', $event->id)->where('cat_id', '=', $event->cat_id);
        })->where('lang_id', $currentLang->id)->take(5)->orderBy('id', 'DESC')->get();
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;
        $stripeData = PaymentGateway::whereKeyword('stripe')->first();
        $stripe = $stripeData->convertAutoData();
        $data['stripe_key'] =  $stripe['key'];
        return view('front.event-details', $data);
    }

    public function portfolios(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['currentLang'] = $currentLang;
        $be = $currentLang->basic_extended;

        $category = $request->category;

        if (!empty($category)) {
            $data['category'] = Scategory::findOrFail($category);
        }

        $data['portfolios'] = Portfolio::when($category, function ($query, $category) {
            $serviceIdArr = [];
            $serviceids = Service::select('id')->where('scategory_id', $category)->get();
            foreach ($serviceids as $key => $serviceid) {
                $serviceIdArr[] = $serviceid->id;
            }
            return $query->whereIn('service_id', $serviceIdArr);
        })->when($currentLang, function ($query, $currentLang) {
            return $query->where('language_id', $currentLang->id);
        })->orderBy('serial_number', 'ASC');

        $version = $be->theme_version;

        if ($version == 'gym') {
            $data['portfolios'] = $data['portfolios']->get();
            return view('front.gym.portfolios', $data);
        } elseif ($version == 'car') {
            $data['portfolios'] = $data['portfolios']->get();
            return view('front.car.portfolios', $data);
        } elseif ($version == 'cleaning') {
            $data['portfolios'] = $data['portfolios']->get();
            return view('front.cleaning.portfolios', $data);
        } elseif ($version == 'construction') {
            $data['portfolios'] = $data['portfolios']->get();
            return view('front.construction.portfolios', $data);
        } elseif ($version == 'logistic') {
            $data['portfolios'] = $data['portfolios']->get();
            return view('front.logistic.portfolios', $data);
        } elseif ($version == 'lawyer') {
            $data['portfolios'] = $data['portfolios']->get();
            return view('front.lawyer.portfolios', $data);
        } elseif ($version == 'default' || $version == 'dark' || $version == 'ecommerce') {
            $data['version'] = $version == 'dark' ? 'default' : $version;
            $data['portfolios'] = $data['portfolios']->get();
            return view('front.portfolios', $data);
        }
    }

    public function portfoliodetails($slug)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['portfolio'] = Portfolio::where('slug', $slug)->firstOrFail();

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.portfolio-details', $data);
    }

    public function servicedetails($slug)
    {

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['service'] = Service::where('slug', $slug)->firstOrFail();

        if ($data['service']->details_page_status == 0) {
            return back();
        }

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.service-details', $data);
    }

    public function careerdetails($slug)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['jcats'] = $currentLang->jcategories()->where('status', 1)->orderBy('serial_number', 'ASC')->get();

        $data['job'] = Job::where('slug', $slug)->firstOrFail();

        $data['jobscount'] = Job::when($currentLang, function ($query, $currentLang) {
            return $query->where('language_id', $currentLang->id);
        })->count();

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;


        return view('front.career-details', $data);
    }

    public function blogs(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $data['currentLang'] = $currentLang;

        $lang_id = $currentLang->id;
        $be = $currentLang->basic_extended;

        $category = $request->category;
        $catid = null;
        if (!empty($category)) {
            $data['category'] = Bcategory::where('slug', $category)->firstOrFail();
            $catid = $data['category']->id;
        }
        $term = $request->term;
        $tag = $request->tag;
        $month = $request->month;
        $year = $request->year;
        $data['archives'] = Archive::orderBy('id', 'DESC')->get();
        $data['bcats'] = Bcategory::where('language_id', $lang_id)->where('status', 1)->orderBy('serial_number', 'ASC')->get();
        if (!empty($month) && !empty($year)) {
            $archive = true;
        } else {
            $archive = false;
        }

        $data['blogs'] = Blog::when($catid, function ($query, $catid) {
            return $query->where('bcategory_id', $catid);
        })
            ->when($term, function ($query, $term) {
                return $query->where('title', 'like', '%' . $term . '%');
            })
            ->when($tag, function ($query, $tag) {
                return $query->where('tags', 'like', '%' . $tag . '%');
            })
            ->when($archive, function ($query) use ($month, $year) {
                return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            })
            ->when($currentLang, function ($query, $currentLang) {
                return $query->where('language_id', $currentLang->id);
            })->orderBy('serial_number', 'ASC')->paginate(6);

        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;


        return view('front.blogs', $data);
    }

    public function blogdetails($slug)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $lang_id = $currentLang->id;


        $data['blog'] = Blog::where('slug', $slug)->firstOrFail();

        $data['archives'] = Archive::orderBy('id', 'DESC')->get();
        $data['bcats'] = Bcategory::where('status', 1)->where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.blog-details', $data);
    }

    public function knowledgebase()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['bse'] = $currentLang->basic_extra;

        $data['article_categories'] = ArticleCategory::where('language_id', $currentLang->id)
            ->where('status', 1)
            ->orderBy('serial_number', 'ASC')
            ->get();

        $data['currentLang'] = $currentLang;

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.articles', $data);
    }

    public function knowledgebase_details($slug)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['bse'] = $currentLang->basic_extra;

        $data['article_categories'] = ArticleCategory::where('language_id', $currentLang->id)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        $data['details'] = Article::where('language_id', $currentLang->id)
            ->where('slug', $slug)
            ->firstOrFail();

        $data['currentLang'] = $currentLang;

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.article_details', $data);
    }

    public function rss(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $lang_id = $currentLang->id;
        $id = $request->id;
        $data['categories'] = RssFeed::where('language_id', $lang_id)->orderBy('id', 'desc')->get();
        $data['rss_posts']  = RssPost::where('language_id', $lang_id)
            ->when($id, function ($query, $id) {
                return $query->where('rss_feed_id', $id);
            })->orderBy('id', 'desc')->paginate(4);

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;
        if ($version == 'dark') {
            $version = 'default';
        }
        $data['version'] = $version;

        return view('front.rss', $data);
    }

    public function rssdetails($slug, $id)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $lang_id = $currentLang->id;
        $data['categories'] = RssFeed::where('language_id', $lang_id)->orderBy('id', 'desc')->get();
        $data['post']  = RssPost::findOrFail($id);

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.rss-details', $data);
    }

    public function contact()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        $data['langg'] = Language::where('code', session('lang'))->first();

        return view('front.contact', $data);
    }

    public function sendmail(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }
        $bs = $currentLang->basic_setting;

        $messages = [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        ];

        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required'
        ];
        if ($bs->is_recaptcha == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $request->validate($rules, $messages);

        $request->validate($rules, $messages);

        $be =  BE::firstOrFail();
        $from = $request->email;
        $to = $be->to_mail;
        $subject = $request->subject;
        $message = $request->message;

        try {

            $mail = new PHPMailer(true);
            $mail->setFrom($from, $request->name);
            $mail->addAddress($to);     // Add a recipient

            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
        } catch (\Exception $e) {
            // die($e->getMessage());
        }

        Session::flash('success', 'Email sent successfully!');
        return back();
    }

    public function subscribe(Request $request)
    {
        $rules = [
            'email' => 'required|email|unique:subscribers'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $subsc = new Subscriber;
        $subsc->email = $request->email;
        $subsc->save();

        return "success";
    }

    public function quote()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;

        if ($bs->is_quote == 0) {
            return view('errors.404');
        }

        $lang_id = $currentLang->id;

        $data['services'] = Service::all();
        $data['inputs'] = QuoteInput::where('language_id', $lang_id)->get();
        $data['ndaIn'] = QuoteInput::find(10);

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.quote', $data);
    }

    public function sendquote(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $quote_inputs = $currentLang->quote_inputs;

        $messages = [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        ];

        $rules = [
            'name' => 'required',
            'email' => 'required|email'
        ];


        $allowedExts = array('zip');
        foreach ($quote_inputs as $input) {
            if ($input->required == 1) {
                $rules["$input->name"][] = 'required';
            }
            // check if input type is 5, then check for zip extension
            if ($input->type == 5) {
                $rules["$input->name"][] = function ($attribute, $value, $fail) use ($request, $input, $allowedExts) {
                    if ($request->hasFile("$input->name")) {
                        $ext = $request->file("$input->name")->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only zip file is allowed");
                        }
                    }
                };
            }
        }

        if ($bs->is_recaptcha == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $request->validate($rules, $messages);

        $fields = [];
        foreach ($quote_inputs as $key => $input) {
            $in_name = $input->name;
            // if the input is file, then move it to 'files' folder
            if ($input->type == 5) {
                if ($request->hasFile("$in_name")) {
                    $fileName = uniqid() . '.' . $request->file("$in_name")->getClientOriginalExtension();
                    $directory = 'assets/front/files/';
                    @mkdir($directory, 0775, true);
                    $request->file("$in_name")->move($directory, $fileName);

                    $fields["$in_name"]['value'] = $fileName;
                    $fields["$in_name"]['type'] = $input->type;
                }
            } else {
                if ($request["$in_name"]) {
                    $fields["$in_name"]['value'] = $request["$in_name"];
                    $fields["$in_name"]['type'] = $input->type;
                }
            }
        }
        $jsonfields = json_encode($fields);
        $jsonfields = str_replace("\/", "/", $jsonfields);


        $quote = new Quote;
        $quote->name = $request->name;
        $quote->email = $request->email;
        $quote->fields = $jsonfields;

        $quote->save();


        // send mail to Admin
        $from = $request->email;
        $to = $be->to_mail;
        $subject = "Quote Request Received";

        $fields = json_decode($quote->fields, true);

        try {

            $mail = new PHPMailer(true);
            $mail->setFrom($from, $request->name);
            $mail->addAddress($to);     // Add a recipient

            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = 'A new quote request has been sent.<br/><strong>Client Name: </strong>' . $request->name . '<br/><strong>Client Mail: </strong>' . $request->email;

            $mail->send();
        } catch (\Exception $e) {
            // die($e->getMessage());
        }

        Session::flash('success', 'Quote request sent successfully');
        return back();
    }

    public function team()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['members'] = Member::when($currentLang, function ($query, $currentLang) {
            return $query->where('language_id', $currentLang->id);
        })->get();

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'gym') {
            return view('front.gym.team', $data);
        } elseif ($version == 'car') {
            return view('front.car.team', $data);
        } elseif ($version == 'cleaning') {
            return view('front.cleaning.team', $data);
        } elseif ($version == 'construction') {
            return view('front.construction.team', $data);
        } elseif ($version == 'logistic') {
            return view('front.logistic.team', $data);
        } elseif ($version == 'lawyer') {
            return view('front.lawyer.team', $data);
        } elseif ($version == 'default' || $version == 'dark' || $version == 'ecommerce') {
            $data['version'] = $version == 'dark' ? 'default' : $version;
            return view('front.team', $data);
        }
    }

    public function career(Request $request)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['jcats'] = $currentLang->jcategories()->where('status', 1)->orderBy('serial_number', 'ASC')->get();


        $category = $request->category;
        $term = $request->term;

        if (!empty($category)) {
            $data['category'] = Jcategory::findOrFail($category);
        }

        $data['jobs'] = Job::when($category, function ($query, $category) {
            return $query->where('jcategory_id', $category);
        })->when($term, function ($query, $term) {
            return $query->where('title', 'like', '%' . $term . '%');
        })->when($currentLang, function ($query, $currentLang) {
            return $query->where('language_id', $currentLang->id);
        })->orderBy('serial_number', 'ASC')->paginate(4);

        $data['jobscount'] = Job::when($currentLang, function ($query, $currentLang) {
            return $query->where('language_id', $currentLang->id);
        })->count();

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.career', $data);
    }

    public function calendar()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $lang_id = $currentLang->id;

        $events = CalendarEvent::where('language_id', $lang_id)->get();
        $formattedEvents = [];

        foreach ($events as $key => $event) {
            $formattedEvents["$key"]['title'] = $event->title;

            $startDate = strtotime($event->start_date);
            $formattedEvents["$key"]['start'] = date('Y-m-d H:i', $startDate);

            $endDate = strtotime($event->end_date);
            $formattedEvents["$key"]['end'] = date('Y-m-d H:i', $endDate);
        }

        $data["formattedEvents"] = $formattedEvents;

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.calendar', $data);
    }

    public function gallery()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $lang_id = $currentLang->id;

        $data['categories'] = GalleryCategory::where('language_id', $lang_id)->where('status', 1)
            ->orderBy('serial_number', 'ASC')->get();

        $data['galleries'] = Gallery::with('galleryImgCategory')->where('language_id', $lang_id)
            ->orderBy('serial_number', 'ASC')->get();

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.gallery', $data);
    }

    public function faq()
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $lang_id = $currentLang->id;

        $data['categories'] = FAQCategory::where('language_id', $lang_id)->where('status', 1)
            ->orderBy('serial_number', 'ASC')->get();

        $data['faqs'] = Faq::where('language_id', $lang_id)->orderBy('serial_number', 'ASC')->get();

        $be = $currentLang->basic_extended;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.faq', $data);
    }

    public function dynamicPage($slug)
    {
        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $data['page'] = Page::where('slug', $slug)->firstOrFail();

        $be = $currentLang->basic_extended;
        $bex = $currentLang->basic_extra;
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        if ($bex->custom_page_pagebuilder == 1) {
            return view('front.dynamic', $data);
        } else {
            return view('front.dynamic1', $data);
        }
    }

    public function changeLanguage($lang)
    {
        session()->put('lang', $lang);
        app()->setLocale($lang);

        $be = be::first();
        $version = $be->theme_version;

        return redirect()->route('front.index');
    }

    public function packageorder(Request $request, $id)
    {
        $bex = BasicExtra::first();

        if ($bex->package_guest_checkout == 1 && $request->type != 'guest' && !Auth::check()) {
            Session::put('link', route('front.packageorder.index', $id));
            return redirect(route('user.login', ['redirected' => 'package-checkout']));
        } elseif ($bex->package_guest_checkout == 0 && !Auth::check()) {
            Session::put('link', route('front.packageorder.index', $id));
            return redirect(route('user.login'));
        }
        if ($bex->recurring_billing == 1) {
            $sub = Subscription::select('next_package_id', 'pending_package_id')->where('user_id', Auth::user()->id)->first();

            if (!empty($sub->next_package_id)) {
                Session::flash('error', 'You already have a package to activate in stock.');
                return back();
            }
            if (!empty($sub->pending_package_id)) {
                Session::flash('error', 'You already have a pending subscription request.');
                return back();
            }
        }

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $lang_id = $currentLang->id;

        $data['package'] = Package::findOrFail($id);

        if ($data['package']->order_status == 0) {
            return view('errors.404');
        }

        $data['inputs'] = PackageInput::where('language_id', $lang_id)->get();
        $data['gateways']  = PaymentGateway::whereStatus(1)->whereType('automatic')->get();
        $data['ogateways']  = OfflineGateway::wherePackageOrderStatus(1)->orderBy('serial_number', 'ASC')->get();
        $paystackData = PaymentGateway::whereKeyword('paystack')->first();
        $data['paystack'] = $paystackData->convertAutoData();
        $stripeData = PaymentGateway::whereKeyword('stripe')->first();
        $stripe = $stripeData->convertAutoData();
        $data['stripe_key'] =  $stripe['key'];

        $be = be::first();
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        return view('front.package-order', $data);
    }

    public function submitorder(Request $request)
    {

        if (session()->has('lang')) {
            $currentLang = Language::where('code', session()->get('lang'))->first();
        } else {
            $currentLang = Language::where('is_default', 1)->first();
        }

        $bs = $currentLang->basic_setting;
        $be = $currentLang->basic_extended;
        $package_inputs = $currentLang->package_inputs;

        $messages = [
            'g-recaptcha-response.required' => 'Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
        ];

        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'package_id' => 'required'
        ];

        $allowedExts = array('zip');
        foreach ($package_inputs as $input) {
            if ($input->required == 1) {
                $rules["$input->name"][] = 'required';
            }
            // check if input type is 5, then check for zip extension
            if ($input->type == 5) {
                $rules["$input->name"][] = function ($attribute, $value, $fail) use ($request, $input, $allowedExts) {
                    if ($request->hasFile("$input->name")) {
                        $ext = $request->file("$input->name")->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail("Only zip file is allowed");
                        }
                    }
                };
            }
        }

        if ($bs->is_recaptcha == 1) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $request->validate($rules, $messages);

        $fields = [];
        foreach ($package_inputs as $key => $input) {
            $in_name = $input->name;
            // if the input is file, then move it to 'files' folder
            if ($input->type == 5) {
                if ($request->hasFile("$in_name")) {
                    $fileName = uniqid() . '.' . $request->file("$in_name")->getClientOriginalExtension();
                    $directory = 'assets/front/files/';
                    @mkdir($directory, 0775, true);
                    $request->file("$in_name")->move($directory, $fileName);

                    $fields["$in_name"]['value'] = $fileName;
                    $fields["$in_name"]['type'] = $input->type;
                }
            } else {
                if ($request["$in_name"]) {
                    $fields["$in_name"]['value'] = $request["$in_name"];
                    $fields["$in_name"]['type'] = $input->type;
                }
            }
        }
        $jsonfields = json_encode($fields);
        $jsonfields = str_replace("\/", "/", $jsonfields);

        $package = Package::findOrFail($request->package_id);

        $in = $request->all();
        $in['name'] = $request->name;
        $in['email'] = $request->email;
        $in['fields'] = $jsonfields;

        $in['package_title'] = $package->title;
        $in['package_currency'] = $package->currency;
        $in['package_price'] = $package->price;
        $in['package_description'] = $package->description;
        $fileName = \Str::random(4) . time() . '.pdf';
        $in['invoice'] = $fileName;
        $po = PackageOrder::create($in);


        // saving order number
        $po->order_number = $po->id + 1000000000;
        $po->save();


        // sending datas to view to make invoice PDF
        $fields = json_decode($po->fields, true);
        $data['packageOrder'] = $po;
        $data['fields'] = $fields;


        // generate pdf from view using dynamic datas
        PDF::loadView('pdf.package', $data)->save('assets/front/invoices/' . $fileName);


        // Send Mail to Buyer
        $mail = new PHPMailer(true);

        if ($be->is_smtp == 1) {
            try {
                //Server settings
                // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = $be->smtp_host;                    // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = $be->smtp_username;                     // SMTP username
                $mail->Password   = $be->smtp_password;                               // SMTP password
                $mail->SMTPSecure = $be->encryption;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = $be->smtp_port;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                //Recipients
                $mail->setFrom($be->from_mail, $be->from_name);
                $mail->addAddress($request->email, $request->name);     // Add a recipient

                // Attachments
                $mail->addAttachment('assets/front/invoices/' . $fileName);         // Add attachments

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = "Order placed for " . $package->title;
                $mail->Body    = 'Hello <strong>' . $request->name . '</strong>,<br/>Your order has been placed successfully. We have attached an invoice in this mail.<br/>Thank you.';

                $mail->send();
            } catch (Exception $e) {
                // die($e->getMessage());
            }
        } else {
            try {

                //Recipients
                $mail->setFrom($be->from_mail, $be->from_name);
                $mail->addAddress($request->email, $request->name);     // Add a recipient

                // Attachments
                $mail->addAttachment('assets/front/invoices/' . $fileName);         // Add attachments

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = "Order placed for " . $package->title;
                $mail->Body    = 'Hello <strong>' . $request->name . '</strong>,<br/>Your order has been placed successfully. We have attached an invoice in this mail.<br/>Thank you.';

                $mail->send();
            } catch (Exception $e) {
                // die($e->getMessage());
            }
        }

        // send mail to Admin
        try {

            $mail = new PHPMailer(true);
            $mail->setFrom($po->email, $po->name);
            $mail->addAddress($be->from_mail);     // Add a recipient

            // Attachments
            $mail->addAttachment('assets/front/invoices/' . $fileName);         // Add attachments

            // Content
            $mail->isHTML(true);  // Set email format to HTML
            $mail->Subject = "Order placed for " . $package->title;
            $mail->Body    = 'A new order has been placed.<br/><strong>Order Number: </strong>' . $po->order_number;

            $mail->send();
        } catch (\Exception $e) {
            // die($e->getMessage());
        }

        Session::flash('success', 'Order placed successfully!');
        return redirect()->route('front.packageorder.confirmation', [$package->id, $po->id]);
    }


    public function orderConfirmation($packageid, $packageOrderId)
    {
        $data['package'] = Package::findOrFail($packageid);
        $bex = BasicExtra::first();

        if ($bex->recurring_billing == 1) {
            $packageOrder = Subscription::findOrFail($packageOrderId);
        } else {
            $packageOrder = PackageOrder::findOrFail($packageOrderId);
        }

        $data['packageOrder'] = $packageOrder;
        $data['fields'] = json_decode($packageOrder->fields, true);

        $be = be::first();
        $version = $be->theme_version;

        if ($version == 'dark') {
            $version = 'default';
        }

        $data['version'] = $version;

        if ($bex->recurring_billing == 1) {
            return view('front.subscription-confirmation', $data);
        } else {
            return view('front.order-confirmation', $data);
        }
    }

    public function loadpayment($slug, $id)
    {
        $data['payment'] = $slug;
        $data['pay_id'] = $id;
        $gateway = '';
        if ($data['pay_id'] != 0 && $data['payment'] != "offline") {
            $gateway = PaymentGateway::findOrFail($data['pay_id']);
        } else {
            $gateway = OfflineGateway::findOrFail($data['pay_id']);
        }
        $data['gateway'] = $gateway;

        return view('front.load.payment', $data);
    }    // Redirect To Checkout Page If Payment is Cancelled



    // Redirect To Success Page If Payment is Comleted

    public function payreturn($packageid)
    {
        return redirect()->route('front.packageorder.index', $packageid)->with('success', __('Pament Compelted!'));
    }

    public function myfatoorah_callback(Request $request)
    {
        $type = Session::get('myfatoorah_payment_type');
        if ($type == 'package') {
            $data = new MyFatoorahController();
            $data = $data->notify($request);
            Session::forget('myfatoorah_payment_type');
            return redirect($data['url']);
        } elseif ($type == 'product') {
            $data = new ShopMyFatoorahController();
            $data = $data->notify($request);
            Session::forget('myfatoorah_payment_type');
            if ($data['status'] == 'success') {
                return redirect($data['url']);
            } else {
                return redirect()->route('myfatoorah.cancel');
            }
        } elseif ($type == 'course') {
            $data = new CourseMyFatoorahController();
            $data = $data->notify($request);
            Session::forget('myfatoorah_payment_type');
            return redirect($data['url']);
        } elseif ($type == 'cause_event') {
            $data = new CauseEventMyFatoorahController();
            $data = $data->notify($request);
            Session::forget('myfatoorah_payment_type');
            return redirect($data['url']);
        }
    }

    public function myfatoorah_cancel(Request $request)
    {
        return redirect()->route('front.index')->with(['alert-type' => 'error', 'message' => 'Payment failed']);
    }
}

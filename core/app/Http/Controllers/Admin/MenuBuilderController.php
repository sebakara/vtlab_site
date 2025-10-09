<?php

namespace App\Http\Controllers\Admin;

use App\BasicSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Language;
use App\Megamenu;
use App\Menu;
use App\Page;
use App\Permalink;
use App\Scategory;
use Illuminate\Support\Facades\Session;

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class MenuBuilderController extends Controller
{

    public function index(Request $request) {
        $lang = Language::where('code', $request->language)->firstOrFail();
        $data['lang_id'] = $lang->id;

        // set language
        app()->setLocale($lang->code);

        // get page names of selected language
        $pages = Page::where('language_id', $lang->id)->get();
        $data["pages"] = $pages;

        // get previous menus
        $menu = Menu::where('language_id', $lang->id)->first();
        $data['prevMenu'] = '';
        if (!empty($menu)) {
            $data['prevMenu'] = $menu->menus;
        }

        return view('admin.menu_builder.index', $data);
    }

    public function update(Request $request) {
        // return response()->json(json_decode($request->str, true));
        $menus = json_decode($request->str, true);
        foreach ($menus as $key => $menu) {
            if (strpos($menu['type'], 'megamenu') !== false) {
                if (array_key_exists('children', $menu) && !empty($menu['children'])) {
                    return response()->json(['status' => 'error', 'message' => 'Mega Menu cannot contain children!']);
                }
            }
            if (array_key_exists('children', $menu) && !empty($menu['children'])) {
                $allChildren = json_encode($menu['children']);
                if (strpos($allChildren, '-megamenu') !== false) {
                    return response()->json(['status' => 'error', 'message' => 'Mega Menu cannot be children of a Menu!']);
                }
            }
        }

        Menu::where('language_id', $request->language_id)->delete();

        $menu = new Menu;
        $menu->language_id = $request->language_id;
        $menu->menus = json_encode($menus);
        $menu->save();

        return response()->json(['status' => 'success', 'message' => 'Menu updated successfully!']);
    }

    public function megamenus() {
        return view('admin.menu_builder.megamenus.megamenus');
    }

    public function megaMenuEdit(Request $request) {
        $lang = Language::where('code', $request->language)->firstOrFail();

        // for 'services' mega menu
        if ($request->type == 'services') {
            if (serviceCategory()) {
                $data['cats'] = $lang->scategories()->where('status', 1)->get();
                $megamenu = Megamenu::where('language_id', $lang->id)->where('type', 'services')->where('category', 1);
                $catStatus = 1;
            } elseif (!serviceCategory()) {
                $data['items'] = $lang->services()->get();
                $megamenu = Megamenu::where('language_id', $lang->id)->where('type', 'services')->where('category', 0);
                $catStatus = 0;
            }
        }

        // for 'products' mega menu
        if ($request->type == 'products') {
            $data['cats'] = $lang->pcategories()->where('status', 1)->get();
            $megamenu = Megamenu::where('language_id', $lang->id)->where('type', 'products')->where('category', 1);
            $catStatus = 1;
        }

        // for 'portfolios' mega menu
        if ($request->type == 'portfolios') {
            if (serviceCategory()) {
                $data['cats'] = $lang->scategories()->where('status', 1)->get();
                $megamenu = Megamenu::where('language_id', $lang->id)->where('type', 'portfolios')->where('category', 1);
                $catStatus = 1;
            } elseif (!serviceCategory()) {
                $data['items'] = $lang->portfolios()->get();
                $megamenu = Megamenu::where('language_id', $lang->id)->where('type', 'portfolios')->where('category', 0);
                $catStatus = 0;
            }
        }

        // for 'courses' mega menu
        if ($request->type == 'courses') {
            $data['cats'] = $lang->course_categories()->get();
            $megamenu = Megamenu::where('language_id', $lang->id)->where('type', 'courses')->where('category', 1);
            $catStatus = 1;
        }

        // for 'causes' mega menu
        if ($request->type == 'causes') {
            $data['items'] = $lang->causes()->get();
            $megamenu = Megamenu::where('language_id', $lang->id)->where('type', 'causes')->where('category', 0);
            $catStatus = 0;
        }

        // for 'events' mega menu
        if ($request->type == 'events') {
            $data['cats'] = $lang->event_categories()->get();
            $megamenu = Megamenu::where('language_id', $lang->id)->where('type', 'events')->where('category', 1);
            $catStatus = 1;
        }

        // for 'blogs' mega menu
        if ($request->type == 'blogs') {
            $data['cats'] = $lang->bcategories()->get();
            $megamenu = Megamenu::where('language_id', $lang->id)->where('type', 'blogs')->where('category', 1);
            $catStatus = 1;
        }

        $data['lang'] = $lang;

        if ($megamenu->count() == 0) {
            $megamenu = new Megamenu;
            $megamenu->language_id = $lang->id;
            $megamenu->type = $request->type;
            $megamenu->menus = json_encode([]);
            $megamenu->category = $catStatus;
            $megamenu->save();
        } else {
            $megamenu = $megamenu->first();
        }

        $data['megamenu'] = $megamenu;
        $data['mmenus'] = json_decode($megamenu->menus, true);

        return view('admin.menu_builder.megamenus.edit', $data);
    }

    public function megaMenuUpdate(Request $request) {
        $menus = [];
        $items = $request->items;
        $langid = $request->language_id;
        $type = $request->type;

        if ($type == 'services') {
            if (!empty($items)) {
                if (serviceCategory()) {
                    foreach ($items as $key => $item) {
                        $item = json_decode($item, true);
                        $catid = $item[0];
                        $menus["$catid"][] = $item[1];
                    }

                    $megamenu = Megamenu::where('language_id', $langid)->where('type', 'services')->where('category', 1)->firstOrFail();
                } elseif (!serviceCategory()) {
                    $menus = $request->items;
                    $megamenu = Megamenu::where('language_id', $langid)->where('type', 'services')->where('category', 0)->firstOrFail();
                }
            }
        } elseif ($type == 'products') {
            if (!empty($items)) {
                foreach ($items as $key => $item) {
                    $item = json_decode($item, true);
                    $catid = $item[0];
                    $menus["$catid"][] = $item[1];
                }

                $megamenu = Megamenu::where('language_id', $langid)->where('type', 'products')->where('category', 1)->firstOrFail();
            }
        } elseif ($type == 'portfolios') {
            if (!empty($items)) {
                if (serviceCategory()) {
                    foreach ($items as $key => $item) {
                        $item = json_decode($item, true);
                        $catid = $item[0];
                        $menus["$catid"][] = $item[1];
                    }

                    $megamenu = Megamenu::where('language_id', $langid)->where('type', 'portfolios')->where('category', 1)->firstOrFail();
                } elseif (!serviceCategory()) {
                    $menus = $request->items;
                    $megamenu = Megamenu::where('language_id', $langid)->where('type', 'portfolios')->where('category', 0)->firstOrFail();
                }
            }
        } elseif ($type == 'courses') {
            if (!empty($items)) {
                foreach ($items as $key => $item) {
                    $item = json_decode($item, true);
                    $catid = $item[0];
                    $menus["$catid"][] = $item[1];
                }

                $megamenu = Megamenu::where('language_id', $langid)->where('type', 'courses')->where('category', 1)->firstOrFail();
            }
        } elseif ($type == 'causes') {
            if (!empty($items)) {
                $menus = $request->items;
                $megamenu = Megamenu::where('language_id', $langid)->where('type', 'causes')->where('category', 0)->firstOrFail();
            }
        } elseif ($type == 'events') {
            if (!empty($items)) {
                foreach ($items as $key => $item) {
                    $item = json_decode($item, true);
                    $catid = $item[0];
                    $menus["$catid"][] = $item[1];
                }

                $megamenu = Megamenu::where('language_id', $langid)->where('type', 'events')->where('category', 1)->firstOrFail();
            }
        } elseif ($type == 'blogs') {
            if (!empty($items)) {
                foreach ($items as $key => $item) {
                    $item = json_decode($item, true);
                    $catid = $item[0];
                    $menus["$catid"][] = $item[1];
                }

                $megamenu = Megamenu::where('language_id', $langid)->where('type', 'blogs')->where('category', 1)->firstOrFail();
            }
        }


        $menus = json_encode($menus);
        $megamenu->menus = $menus;
        $megamenu->save();

        $request->session()->flash('success', 'Mega Menu updated for ' . $request->type);
        return back();
    }

    public function permalinks() {
        $permalinks = Permalink::all();
        $data['permalinks'] = $permalinks;

        return view('admin.menu_builder.permalink', $data);
    }

    public function permalinksUpdate(Request $request) {
        $requests = $request->except("_token");
        // return $requests;

        $rules = [];
        foreach ($requests as $type => $permalink) {
            $rules["$type"] = [
                'required',
                'max:50',
                function ($attribute, $value, $fail) use ($type, $permalink) {
                    // fetch details
                    $details = Permalink::where('type', $type)->first()->details;

                    // if the 'permalink' matches with same 'details' row, then throw error
                    $permalinks = Permalink::where('details', $details)->where('type', '<>', $type)->get();
                    foreach ($permalinks as $key => $pl) {
                        if ($pl->permalink == $permalink) {
                            $fail('Must be unique ' . ($details == 1 ? 'Details Page ' : 'Non-Details Page ') . 'link');
                        }
                    }

                }
            ];
        }

        $request->validate($rules);

        foreach ($requests as $key => $val) {
            $pl = Permalink::where('type', $key)->firstOrFail();
            $pl->permalink = $val;
            $pl->save();
        }

        $request->session()->flash('success', 'Permalinks updated successfully');
        return back();
    }
}

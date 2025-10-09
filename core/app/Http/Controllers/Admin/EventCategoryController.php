<?php

namespace App\Http\Controllers\Admin;

use App\EventCategory;
use App\Http\Requests\EventCategory\EventCategoryStoreRequest;
use App\Http\Requests\EventCategory\EventCategoryUpdateRequest;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Megamenu;
use Validator;
use Session;
use DB;

class EventCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return
     */
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();
        $lang_id = $lang->id;
        $data['lang_id'] = $lang_id;
        $data['event_categories'] = EventCategory::where('lang_id', $lang_id)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.event.event_category.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventCategoryStoreRequest $request)
    {
        EventCategory::create($request->all()+[
                'slug' => make_slug($request->name)
            ]);
        Session::flash('success', 'Event category added successfully!');
        return "success";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EventCategoryUpdateRequest $request)
    {
        EventCategory::findOrFail($request->event_category_id)->update($request->all()+[
            'slug' => make_slug($request->name)
        ]);
        Session::flash('success', 'Event category updated successfully!');
        return "success";
    }

    public function deleteFromMegaMenu($ecat) {
        $megamenu = Megamenu::where('language_id', $ecat->lang_id)->where('category', 1)->where('type', 'events');
        if ($megamenu->count() > 0) {
            $megamenu = $megamenu->first();
            $menus = json_decode($megamenu->menus, true);
            $catId = $ecat->id;
            if (is_array($menus) && array_key_exists("$catId", $menus)) {
                unset($menus["$catId"]);
                $megamenu->menus = json_encode($menus);
                $megamenu->save();
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ecat = EventCategory::findOrFail($request->event_category_id);
        $this->deleteFromMegaMenu($ecat);
        $ecat->delete();
        Session::flash('success', 'Event category deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        return DB::transaction(function() use ($request){
            $ids = $request->ids;
            foreach ($ids as $id) {
                $ecat = EventCategory::findOrFail($id);
                $this->deleteFromMegaMenu($ecat);
                $ecat->delete();
            }
            Session::flash('success', 'Event category deleted successfully!');
            return "success";
        });
    }
}

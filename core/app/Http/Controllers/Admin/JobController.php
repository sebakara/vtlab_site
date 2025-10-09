<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Job;
use App\Jcategory;
use App\Language;
use Validator;
use Session;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->first();

        $lang_id = $lang->id;
        $data['jobs'] = Job::where('language_id', $lang_id)->orderBy('id', 'DESC')->get();

        return view('admin.job.job.index', $data);
    }

    public function edit($id)
    {
        $data['job'] = Job::findOrFail($id);
        $data['jcats'] = Jcategory::where('status', 1)->where('language_id', $data['job']->language_id)->get();
        return view('admin.job.job.edit', $data);
    }

    public function create()
    {
        $data['jcats'] = Jcategory::all();
        $data['tjobs'] = Job::where('language_id', 0)->get();
        return view('admin.job.job.create', $data);
    }

    public function store(Request $request)
    {
        $slug = make_slug($request->title);

        $messages = [
            'jcategory_id.required' => 'The category field is required',
            'language_id.required' => 'The language field is required'
        ];

        $rules = [
            'language_id' => 'required',
            'deadline' => 'required|date',
            'experience' => 'required',
            'jcategory_id' => 'required',
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug) {
                    $jobs = Job::all();
                    foreach ($jobs as $key => $job) {
                        if (strtolower($slug) == strtolower($job->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'vacancy' => 'required|integer',
            'employment_status' => 'required|max:255',
            'job_responsibilities' => 'required',
            'educational_requirements' => 'required',
            'experience_requirements' => 'required',
            'additional_requirements' => 'nullable',
            'job_location' => 'required|max:255',
            'salary' => 'required',
            'email' => 'required|email|max:255',
            'benefits' => 'nullable',
            'read_before_apply' => 'nullable',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }

        $in = $request->all();
        $in['slug'] = $slug;
        $in['job_responsibilities'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->job_responsibilities);
        $in['educational_requirements'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->educational_requirements);
        $in['experience_requirements'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->experience_requirements);
        $in['additional_requirements'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->additional_requirements);
        $in['salary'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->salary);
        $in['benefits'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->benefits);
        $in['read_before_apply'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->read_before_apply);
        Job::create($in);

        Session::flash('success', 'Job posted successfully!');
        return "success";
    }

    public function update(Request $request)
    {
        $slug = make_slug($request->title);
        $jobId = $request->job_id;

        $messages = [
            'jcategory_id.required' => 'The category field is required'
        ];

        $rules = [
            'deadline' => 'required|date',
            'experience' => 'required',
            'jcategory_id' => 'required',
            'title' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($slug, $jobId) {
                    $jobs = Job::all();
                    foreach ($jobs as $key => $job) {
                        if ($job->id != $jobId && strtolower($slug) == strtolower($job->slug)) {
                            $fail('The title field must be unique.');
                        }
                    }
                }
            ],
            'vacancy' => 'required|integer',
            'employment_status' => 'required|max:255',
            'job_responsibilities' => 'required',
            'educational_requirements' => 'required',
            'experience_requirements' => 'required',
            'additional_requirements' => 'nullable',
            'job_location' => 'required|max:255',
            'salary' => 'required',
            'email' => 'required|email|max:255',
            'benefits' => 'nullable',
            'read_before_apply' => 'nullable',
            'serial_number' => 'required|integer',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $errmsgs = $validator->getMessageBag()->add('error', 'true');
            return response()->json($validator->errors());
        }


        $job = Job::findOrFail($request->job_id);
        $in = $request->all();
        $in['slug'] = $slug;


        $in['job_responsibilities'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->job_responsibilities);
        $in['educational_requirements'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->educational_requirements);
        $in['experience_requirements'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->experience_requirements);
        $in['additional_requirements'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->additional_requirements);
        $in['salary'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->salary);
        $in['benefits'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->benefits);
        $in['read_before_apply'] = str_replace(url('/') . '/assets/front/img/', "{base_url}/assets/front/img/", $request->read_before_apply);

        $job->fill($in)->save();

        Session::flash('success', 'Job details updated successfully!');
        return "success";
    }

    public function delete(Request $request)
    {
        $job = Job::findOrFail($request->job_id);
        $job->delete();

        Session::flash('success', 'Job deleted successfully!');
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;

        foreach ($ids as $id) {
            $job = Job::findOrFail($id);
            $job->delete();
        }

        Session::flash('success', 'Jobs deleted successfully!');
        return "success";
    }

    public function getcats($langid)
    {
        $jcategories = Jcategory::where('language_id', $langid)->get();

        return $jcategories;
    }
}

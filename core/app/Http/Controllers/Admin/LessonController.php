<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lesson;
use App\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
  public function index($id)
  {
    $module = Module::findOrFail($id);
    $lessons = Lesson::where('module_id', $module->id)->get();

    return view('admin.course.lesson.index', compact('module', 'lessons'));
  }

  public function store(Request $request)
  {
    if ($request->video == 1) {
        $request->video_link = NULL;
    } elseif ($request->video == 2) {
        $request->video_file = NULL;
    }

    // $videoFile = $request->file('video_file');
    $videoLink = $request->video_link;
    $videoFile = $request->video_file;
    $videoExts = array('mp4');
    $extVideo = pathinfo($videoFile, PATHINFO_EXTENSION);

    $rules = [
      'name' => 'required',
      'video' => function ($attribute, $value, $fail) use ($videoFile, $videoLink) {
        if (empty($videoFile) && empty($videoLink)) {
          $fail('The video field is required');
        }
      },
      'duration' => 'required'
    ];
    if ($request->filled('video_file')) {
        $rules['video_file'] = [
            function ($attribute, $value, $fail) use ($extVideo, $videoExts) {
                if (!in_array($extVideo, $videoExts)) {
                    return $fail("Only mp4 video is allowed");
                }
            }
        ];
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $lesson = new Lesson;
    $lesson->module_id = $request->module_id;
    $lesson->name = $request->name;


    if ($request->filled('video_file')) {
        $videoFileName = uniqid() .'.'. $extVideo;
        $directory = 'assets/front/video/lesson_videos/';
        @mkdir($directory, 0775, true);
        @copy($videoFile, $directory . $videoFileName);
    } else {
        $videoFileName = null;
    }
    $lesson->video_file = $videoFileName;

    $lesson->video_link = $request->video_link;

    $lesson->duration = $request->duration;
    $lesson->save();

    Session::flash('success', 'Lesson Added Successfully');

    return 'success';
  }

  public function update(Request $request)
  {
    if ($request->edit_video == 1) {
        $request->edit_video_link = NULL;
    } elseif ($request->edit_video == 2) {
        $request->video_file = NULL;
    }

    $videoOptionVal = $request->edit_video;
    $videoLink = $request->edit_video_link;

    $videoFile = $request->video_file;
    $videoExts = array('mp4');
    $extVideo = pathinfo($videoFile, PATHINFO_EXTENSION);

    $rules = [
      'name' => 'required',
      'edit_video' => function ($attribute, $value, $fail) use ($videoOptionVal, $videoLink) {
        if ($videoOptionVal == 2 && empty($videoLink)) {
          $fail('The video field is required');
        }
      },
      'duration' => 'required'
    ];
    if ($request->filled('video_file')) {
        $rules['video_file'] = [
            function ($attribute, $value, $fail) use ($extVideo, $videoExts) {
                if (!in_array($extVideo, $videoExts)) {
                    return $fail("Only mp4 video is allowed");
                }
            }
        ];
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
      $validator->getMessageBag()->add('error', 'true');
      return response()->json($validator->errors());
    }

    $lesson = Lesson::findOrFail($request->lesson_id);
    $lesson->name = $request->name;

    if ($request->filled('video_file')) {
        @unlink('assets/front/video/lesson_videos/' . $lesson->video_file);
        $videoFileName = uniqid() .'.'. $extVideo;
        @copy($videoFile, 'assets/front/video/lesson_videos/' . $videoFileName);
        $lesson->video_file = $videoFileName;
        $lesson->video_link = null;
    } else if (!empty($request->edit_video_link)) {
      if (File::exists('assets/front/video/lesson_videos/' . $lesson->video_file)) {
        File::delete('assets/front/video/lesson_videos/' . $lesson->video_file);
      }

      $lesson->video_link = $request->edit_video_link;

      // if there has video link then video file will be null in the database
      $lesson->video_file = null;
    }

    $lesson->duration = $request->duration;
    $lesson->save();

    Session::flash('success', 'Lesson Updated Successfully');

    return 'success';
  }

  public function delete(Request $request)
  {
    $lesson = Lesson::findOrFail($request->lesson_id);

    if (File::exists('assets/front/video/lesson_videos/' . $lesson->video_file)) {
      File::delete('assets/front/video/lesson_videos/' . $lesson->video_file);
    }

    $lesson->delete();

    Session::flash('success', 'Lesson Deleted Successfully');

    return back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $lesson = Lesson::findOrFail($id);

      if (File::exists('assets/front/video/lesson_videos/' . $lesson->video_file)) {
        File::delete('assets/front/video/lesson_videos/' . $lesson->video_file);
      }

      $lesson->delete();
    }

    Session::flash('success', 'Lessons Deleted Successfully');

    return 'success';
  }
}

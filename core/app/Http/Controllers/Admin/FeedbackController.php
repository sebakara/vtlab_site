<?php

namespace App\Http\Controllers\Admin;

use App\Feedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class FeedbackController extends Controller
{
  public function feedbacks()
  {
    $feedbacks = Feedback::orderBy('id', 'desc')->paginate(10);

    return view('admin.feedback.client_feedback', compact('feedbacks'));
  }

  public function deleteFeedback(Request $request)
  {
    Feedback::findOrFail($request->feedback_id)->delete();

    Session::flash('success', 'Feedback deleted successfully!');

    return redirect()->back();
  }

  public function bulkDelete(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $fb = Feedback::findOrFail($id);
      $fb->delete();
    }

    Session::flash('success', 'Feedbacks deleted successfully!');
    return "success";
  }
}

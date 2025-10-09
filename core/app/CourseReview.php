<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
  protected $fillable = [
    'user_id',
    'course_id',
    'comment',
    'rating'
  ];

  public function reviewedCourse()
  {
    return $this->belongsTo('App\Course');
  }

  public function reviewByUser()
  {
    return $this->belongsTo('App\User', 'user_id', 'id');
  }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
  public function moduleBelongsToCourse()
  {
    return $this->belongsTo('App\Course');
  }

  public function lessons()
  {
    return $this->hasMany('App\Lesson');
  }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
  public function articleCategory() {
    return $this->belongsTo('App\ArticleCategory');
  }

  public function language() {
    return $this->belongsTo('App\Language');
  }
}

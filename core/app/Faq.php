<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
  public $timestamps = false;

  public function faqCategory()
  {
    return $this->belongsTo('App\FAQCategory', 'category_id', 'id');
  }
}

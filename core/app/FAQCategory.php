<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FAQCategory extends Model
{
  protected $table = 'faq_categories';

  protected $fillable = [
    'language_id',
    'name',
    'status',
    'serial_number'
  ];

  public function faqCategoryLang()
  {
    return $this->belongsTo('App\Language');
  }

  public function frequentlyAskedQuestion()
  {
    return $this->hasMany('App\Faq', 'category_id', 'id');
  }
}

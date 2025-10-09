<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GalleryCategory extends Model
{
  protected $fillable = [
    'language_id',
    'name',
    'status',
    'serial_number'
  ];

  public function galleryCategoryLang()
  {
    return $this->belongsTo('App\Language');
  }

  public function galleryImg()
  {
    return $this->hasMany('App\Gallery', 'category_id', 'id');
  }
}

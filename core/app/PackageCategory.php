<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageCategory extends Model
{
  protected $table = 'package_categories';

  protected $fillable = [
    'language_id',
    'name',
    'status',
    'serial_number'
  ];

  public function packageCategoryLang()
  {
    return $this->belongsTo('App\Language');
  }

  public function packageList()
  {
    return $this->hasMany('App\Package', 'category_id', 'id');
  }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
  protected $fillable = ['title', 'language_id', 'price', 'description', 'serial_number', 'meta_keywords', 'meta_description', 'color', 'order_status', 'link', 'image', 'feature', 'duration', 'category_id'];

  public function language()
  {
    return $this->belongsTo('App\Language');
  }

  public function package_orders()
  {
    return $this->hasMany('App\PackageOrder');
  }

  public function current_subscriptions()
  {
    return $this->hasMany('App\Subscription', 'current_package_id');
  }

  public function next_subscriptions()
  {
    return $this->hasMany('App\Subscription', 'next_package_id');
  }

  public function pending_subscriptions()
  {
    return $this->hasMany('App\Subscription', 'pending_package_id');
  }

  public function packageCategory()
  {
    return $this->belongsTo('App\PackageCategory', 'category_id', 'id');
  }
}

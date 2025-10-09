<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoursePurchase extends Model
{
  protected $fillable = [
    'user_id',
    'order_number',
    'first_name',
    'last_name',
    'email',
    'course_id',
    'currency_code',
    'current_price',
    'previous_price',
    'payment_method',
    'payment_status',
    'invoice'
  ];

  public function courseSellTo()
  {
    return $this->belongsTo('App\User');
  }

  public function course()
  {
    return $this->hasOne('App\Course', 'id', 'course_id');
  }

  public function user()
  {
    return $this->belongsTo('App\User');
  }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $table = "donations";
    protected $fillable = [
        'title',
        'slug',
        'content',
        'goal_amount',
        'min_amount',
        'custom_amount',
        'image',
        'meta_tags',
        'meta_description',
        'lang_id'
    ];
}

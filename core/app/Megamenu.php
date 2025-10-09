<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Megamenu extends Model
{
    public $timestamps = false;

    protected $fillable = ['language_id ', 'type', 'menus'];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permalink extends Model
{
    protected $fillable = ['permalink', 'type', 'details'];
    public $timestamps = false;
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    public $timestamps = false;

    public function language() {
        return $this->belongsTo('App\Models\Language');
    }
}

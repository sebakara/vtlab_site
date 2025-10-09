<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventDetail extends Model
{
    protected $table= "event_details";
    protected $fillable= [
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'amount',
                        'quantity',
                        'currency',
                        'currency_symbol',
                        'transaction_id',
                        'status',
                        'receipt',
                        'transaction_details',
                        'bex_details',
                        'event_id',
                        'payment_method'
                        ];

    public function event() {
        return $this->belongsTo('App\Event', 'event_id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonationDetail extends Model
{
    protected $table= "donation_details";
    protected $fillable= [
                        'user_id',
                        'name',
                        'email',
                        'phone',
                        'amount',
                        'currency',
                        'currency_position',
                        'currency_symbol',
                        'currency_symbol_position',
                        'transaction_id',
                        'status',
                        'receipt',
                        'transaction_details',
                        'bex_details',
                        'donation_id',
                        'payment_method'
                        ];

    public function cause() {
        return $this->belongsTo('App\Donation', 'donation_id');
    }
}

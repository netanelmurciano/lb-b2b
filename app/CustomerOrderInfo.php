<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOrderInfo extends Model
{
    public $table = 'customer_order_info';

    protected $fillable = [
        'firstName',
        'lastName',
        'address',
        'deliveryTime',
        'isIatHome',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo('App\User', 'id');
    }
}

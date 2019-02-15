<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    public $table = 'products_orders';

    protected $fillable = [
        'produtName',
        'numOfItems',
        'totalPrice',
        'produtId',
        'customerUserId',
        'customerOrderInfoId',
    ];
}

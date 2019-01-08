<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     protected $fillable = ['name', 'price', 'description', 'availability', 'image_path', 'image_thumbnail' , 'total_price'];
}

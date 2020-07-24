<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'price',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

     public function order_items()
     {
         return $this->belongsToMany('App\Order');
     }
    

}

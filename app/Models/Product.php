<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'specifications',
        'status',
        'quantity',
        'rating',
        'basePrise',
        'currentPrice',
        'thumbnailLink'
    ];

    protected $hidden = [
        //'trending',
        'popularity',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function subCategory(){
        return $this->belongsTo(ProductSubCategory::class);
    }

    public function carts(){
        return $this->hasMany(Cart::class);
    }
}

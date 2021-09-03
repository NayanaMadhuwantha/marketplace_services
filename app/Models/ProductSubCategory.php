<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSubCategory extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public function category(){
        return $this->belongsTo(ProductCategory::class);
    }

    public function products(){
        return $this->hasMany(Product::class);
    }

}

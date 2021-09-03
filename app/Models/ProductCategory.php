<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    public function subCategories(){
        return $this->hasMany(ProductSubCategory::class);
    }
}

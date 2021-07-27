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
        'popularity',
        'trending',
        'basePrise',
        'currentPrice',
        'thumbnailLink'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressBook extends Model
{
    protected $fillable = [
        'fullName',
        'mobile',
        'streetAddress1',
        'streetAddress2',
        'city',
        'state',
        'country',
        'zipCode',
        'primary',
        'user_id'
    ];

    public function addressBook(){
        return $this->belongsTo(User::class);
    }
}

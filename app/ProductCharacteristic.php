<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCharacteristic extends Model
{
    protected $fillable = [
        'boolean_value', 'number_value', 'string_value', 'product_id', 'characteristic_id'
    ];

    public function characteristic()
    {
        return $this->belongsTo('App\Characteristic');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}

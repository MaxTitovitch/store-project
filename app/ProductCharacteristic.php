<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCharacteristic extends Model
{
    public function characteristic()
    {
        return $this->belongsTo('App\Characteristic');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}

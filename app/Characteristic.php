<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Characteristic extends Model
{
    protected $fillable = [
        'name', 'type', 'int_value_start', 'int_value_end'
    ];

    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }

    public function characteristicValues()
    {
        return $this->hasMany('App\CharacteristicValue');
    }

    public function productCharacteristics()
    {
        return $this->hasMany('App\ProductCharacteristic');
    }
}

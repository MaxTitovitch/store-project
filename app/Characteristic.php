<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Characteristic extends Model
{
    protected $fillable = [
        'name', 'type', 'int_value_start', 'int_value_end'
    ];

    public function updateExtremum($value)
    {
        if($this->int_value_start > $value ) {
            $this->int_value_start = $value;
            $this->save();
        } else if ($this->int_value_end < $value) {
            $this->int_value_end = $value;
            $this->save();
        }
    }

    public static function findByNameAndCategory($name, $category) {
        if($category != null) {
            foreach ($category->characteristics as $characteristic) {
                if($characteristic->name == $name) {
                    return $characteristic;
                }
            }
        }
        return false;
    }

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

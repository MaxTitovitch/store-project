<?php

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class MassUpload extends Facade
{
    protected static function getFacadeAccessor() {
        return 'mass-upload';
    }
}
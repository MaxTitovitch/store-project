<?php

namespace App\Providers;

use App\Services\MassParsing\ParsingClient;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class MassUploadProvider extends ServiceProvider
{
    public function register()
    {
        App::bind('mass-upload', function()
        {
            return new ParsingClient();
        });
    }

    public function boot()
    {
        //
    }
}

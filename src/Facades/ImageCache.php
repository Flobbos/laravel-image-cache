<?php

namespace Flobbos\LaravelImageCache\Facades;

use Illuminate\Support\Facades\Facade;

class ImageCache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imagecache';
    }
}

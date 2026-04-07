<?php

namespace Flobbos\LaravelImageCache\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Intervention\Image\Interfaces\ImageInterface|string cache(string $source, \Closure $callback, ?int $lifetime = null, ?bool $returnObject = null, ?string $templateName = null)
 * @method static \Intervention\Image\Interfaces\ImageInterface|string template(string $source, string $templateName, ?int $lifetime = null, ?bool $returnObject = null)
 *
 * @see \Flobbos\LaravelImageCache\ImageCache
 */
class ImageCache extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'imagecache';
    }
}

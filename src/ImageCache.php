<?php

namespace Flobbos\LaravelImageCache;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Illuminate\Cache\Repository as Cache;
use Closure;
use InvalidArgumentException;

class ImageCache
{
    protected $manager;
    protected $cache;

    public function __construct(ImageManager $manager, Cache $cache)
    {
        $this->manager = $manager;
        $this->cache = $cache;
    }

    public function cache($source, Closure $callback, $lifetime = null, $returnObject = null, $templateName = null)
    {
        $lifetime = $lifetime ?? config('imagecache.lifetime', 10);
        $returnObject = $returnObject ?? config('imagecache.return_object', false);

        $key = $this->generateCacheKey($source, $callback, $templateName);

        if ($this->cache->has($key)) {
            $cached = $this->cache->get($key);
            return $returnObject ? $this->manager->read($cached) : $cached;
        }

        $image = $this->manager->read($source);
        $callback($image);
        $output = $this->encodeImage($image, config('imagecache.format', 'jpg'));

        $this->cache->put($key, $output, $lifetime * 60);

        return $returnObject ? $image : $output;
    }

    public function template($source, string $templateName, $lifetime = null, $returnObject = null)
    {
        $templates = config('imagecache.templates', []);
        if (!isset($templates[$templateName])) {
            throw new InvalidArgumentException("Template '$templateName' not found.");
        }

        $templateClass = $templates[$templateName];
        $template = new $templateClass();

        return $this->cache($source, function ($image) use ($template) {
            $template->build($image);
        }, $lifetime, $returnObject, $templateName);
    }

    protected function generateCacheKey($source, Closure $callback, $templateName = null)
    {
        $image = $this->manager->read($source);
        $callback($image);
        $base = $templateName ? $source . $templateName : $source . serialize($image);
        return md5($base);
    }

    protected function encodeImage(ImageInterface $image, string $format)
    {
        return match ($format) {
            'png' => $image->toPng(),
            'gif' => $image->toGif(),
            'webp' => $image->toWebp(),
            default => $image->toJpeg(),
        };
    }

    public function __call($method, $arguments)
    {
        return $this->manager->$method(...$arguments);
    }
}

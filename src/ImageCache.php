<?php

namespace Flobbos\LaravelImageCache;

use Closure;
use Illuminate\Cache\Repository as Cache;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use InvalidArgumentException;

class ImageCache
{
    protected ImageManager $manager;
    protected Cache $cache;

    public function __construct(ImageManager $manager, Cache $cache)
    {
        $this->manager = $manager;
        $this->cache = $cache;
    }

    /**
     * Cache an image after applying the given callback.
     */
    public function cache(string $source, Closure $callback, ?int $lifetime = null, ?bool $returnObject = null, ?string $templateName = null): ImageInterface|string
    {
        $lifetime = $lifetime ?? config('imagecache.lifetime', 10);
        $returnObject = $returnObject ?? config('imagecache.return_object', false);

        $key = $this->generateCacheKey($source, $templateName);

        if ($this->cache->has($key)) {
            $cached = $this->cache->get($key);

            if ($returnObject) {
                return $this->manager->read($cached);
            }

            return $cached;
        }

        $image = $this->manager->read($source);
        $callback($image);

        $format = config('imagecache.format', 'jpg');
        $output = (string) $this->encodeImage($image, $format);

        $this->cache->put($key, $output, now()->addMinutes($lifetime));

        return $returnObject ? $image : $output;
    }

    /**
     * Apply a named template to the given image source.
     */
    public function template(string $source, string $templateName, ?int $lifetime = null, ?bool $returnObject = null): ImageInterface|string
    {
        $templates = config('imagecache.templates', []);

        if (! isset($templates[$templateName])) {
            throw new InvalidArgumentException("Template '{$templateName}' not found.");
        }

        $templateClass = $templates[$templateName];
        $template = app($templateClass);

        return $this->cache($source, function (ImageInterface $image) use ($template): void {
            $template->build($image);
        }, $lifetime, $returnObject, $templateName);
    }

    /**
     * Generate a cache key based on the source path, its modification time,
     * and the template name. No image read required.
     */
    protected function generateCacheKey(string $source, ?string $templateName = null): string
    {
        $mtime = file_exists($source) ? filemtime($source) : 0;
        $base = $source . '|' . $mtime;

        if ($templateName !== null) {
            $base .= '|' . $templateName;
        }

        return 'imagecache:' . md5($base);
    }

    /**
     * Encode an image to the specified format.
     */
    protected function encodeImage(ImageInterface $image, string $format): mixed
    {
        return match ($format) {
            'png' => $image->toPng(),
            'gif' => $image->toGif(),
            'webp' => $image->toWebp(),
            'avif' => $image->toAvif(),
            default => $image->toJpeg(),
        };
    }
}

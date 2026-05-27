<?php

namespace Flobbos\LaravelImageCache;

use Closure;
use Flobbos\LaravelImageCache\Support\Format;
use Illuminate\Cache\Repository as Cache;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\ImageManager;
use InvalidArgumentException;

class ImageCache
{
    public function __construct(
        protected ImageManager $manager,
        protected Cache $cache,
    ) {
    }

    /**
     * Cache an image after applying the given callback.
     */
    public function cache(string $source, Closure $callback, ?int $lifetime = null, ?bool $returnObject = null, ?string $templateName = null): ImageInterface|string
    {
        $lifetime = $lifetime ?? (int) config('imagecache.lifetime', 10);
        $returnObject = $returnObject ?? (bool) config('imagecache.return_object', false);

        $key = $this->generateCacheKey($source, $templateName);

        $cached = $this->cache->get($key);

        if ($cached !== null) {
            return $returnObject ? $this->manager->read($cached) : $cached;
        }

        $image = $this->manager->read($source);
        $callback($image);

        $format = Format::normalize((string) config('imagecache.format', 'jpg'));
        $output = (string) $this->encodeImage($image, $format);

        if ($lifetime <= 0) {
            $this->cache->forever($key, $output);
        } else {
            $this->cache->put($key, $output, now()->addMinutes($lifetime));
        }

        return $returnObject ? $image : $output;
    }

    /**
     * Apply a named template to the given image source.
     */
    public function template(string $source, string $templateName, ?int $lifetime = null, ?bool $returnObject = null): ImageInterface|string
    {
        $templates = (array) config('imagecache.templates', []);

        if (! isset($templates[$templateName])) {
            throw new InvalidArgumentException("Template '{$templateName}' not found.");
        }

        $template = app($templates[$templateName]);

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
     * Encode an image to the specified format using the configured quality.
     */
    protected function encodeImage(ImageInterface $image, string $format): mixed
    {
        $quality = config('imagecache.quality');

        return match ($format) {
            'png' => $image->toPng(),
            'gif' => $image->toGif(),
            'webp' => $quality !== null ? $image->toWebp($quality) : $image->toWebp(),
            'avif' => $quality !== null ? $image->toAvif($quality) : $image->toAvif(),
            default => $quality !== null ? $image->toJpeg($quality) : $image->toJpeg(),
        };
    }
}

<?php

namespace Flobbos\LaravelImageCache\Http\Controllers;

use Flobbos\LaravelImageCache\ImageCache;
use Flobbos\LaravelImageCache\Support\Format;
use Illuminate\Http\Response;

class ServeImageController
{
    public function __construct(protected ImageCache $imageCache)
    {
    }

    public function __invoke(string $template, string $path): Response
    {
        $fullPath = $this->resolveImagePath($path);

        if ($fullPath === null) {
            abort(404, 'Image not found.');
        }

        $image = $this->imageCache->template($fullPath, $template);

        $format = config('imagecache.format', 'jpg');
        $lifetime = (int) config('imagecache.lifetime', 10);

        $response = response($image)->header('Content-Type', Format::mime($format));

        if ($lifetime > 0) {
            $response->header('Cache-Control', 'public, max-age=' . ($lifetime * 60));
        }

        $mtime = @filemtime($fullPath);
        if ($mtime !== false) {
            $response->header('Last-Modified', gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
            $response->header('ETag', '"' . md5($fullPath . '|' . $mtime . '|' . $template) . '"');
        }

        return $response;
    }

    protected function resolveImagePath(string $path): ?string
    {
        $paths = (array) config('imagecache.paths', []);

        foreach ($paths as $basePath) {
            $baseReal = realpath($basePath);

            if ($baseReal === false) {
                continue;
            }

            $candidate = $baseReal . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
            $candidateReal = realpath($candidate);

            if ($candidateReal === false) {
                continue;
            }

            if (! str_starts_with($candidateReal, $baseReal . DIRECTORY_SEPARATOR)) {
                continue;
            }

            if (! is_file($candidateReal)) {
                continue;
            }

            return $candidateReal;
        }

        return null;
    }
}

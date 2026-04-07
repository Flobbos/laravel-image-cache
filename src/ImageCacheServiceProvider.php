<?php

namespace Flobbos\LaravelImageCache;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Libvips\Driver as LibvipsDriver;
use Intervention\Image\ImageManager;

class ImageCacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/imagecache.php',
            'imagecache'
        );

        $this->app->singleton('imagecache', function ($app) {
            $driver = config('imagecache.driver', 'gd');

            $driverClass = match ($driver) {
                'imagick' => ImagickDriver::class,
                'libvips' => LibvipsDriver::class,
                default => GdDriver::class,
            };

            $manager = new ImageManager(new $driverClass());

            $store = config('imagecache.store');
            $cache = $store ? $app['cache']->store($store) : $app['cache']->store();

            return new ImageCache($manager, $cache);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/imagecache.php' => config_path('imagecache.php'),
        ], 'config');

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $route = config('imagecache.dynamic_route', 'images');

        Route::get('/' . $route . '/{template}/{path}', function (string $template, string $path) {
            $paths = config('imagecache.paths', []);
            $fullPath = null;

            foreach ($paths as $basePath) {
                $candidate = rtrim($basePath, DIRECTORY_SEPARATOR)
                    . DIRECTORY_SEPARATOR
                    . ltrim($path, DIRECTORY_SEPARATOR);

                if (file_exists($candidate)) {
                    $fullPath = $candidate;
                    break;
                }
            }

            if (! $fullPath) {
                abort(404, 'Image not found.');
            }

            $image = app('imagecache')->template($fullPath, $template);

            $format = config('imagecache.format', 'jpg');
            $mime = match ($format) {
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'avif' => 'image/avif',
                default => 'image/jpeg',
            };

            return response($image)->header('Content-Type', $mime);
        })
            ->where('path', '.*')
            ->name('imagecache.serve');
    }
}

<?php

namespace Flobbos\LaravelImageCache;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Libvips\Driver as LibvipsDriver;
use Illuminate\Support\Facades\Route;

class ImageCacheServiceProvider extends ServiceProvider
{
    public function register()
    {
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

    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/imagecache.php' => config_path('imagecache.php'),
        ], 'config');

        $route = config('imagecache.dynamic_route', 'images');

        // Register dynamic route
        Route::get('/' . $route . '/{template}/{path}', function ($template, $path) {
            $paths = config('imagecache.paths', []);
            $fullPath = null;

            foreach ($paths as $basePath) {
                $candidate = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
                if (file_exists($candidate)) {
                    $fullPath = $candidate;
                    break;
                }
            }

            if (!$fullPath) {
                abort(404, 'Image not found');
            }

            $image = app('imagecache')->template($fullPath, $template);
            $format = config('imagecache.format', 'jpg');
            $mime = match ($format) {
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                default => 'image/jpeg',
            };

            return response($image)->header('Content-Type', $mime);
        })->where('path', '.*');
    }
}

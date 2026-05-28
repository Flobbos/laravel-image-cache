<?php

namespace Flobbos\LaravelImageCache;

use Flobbos\LaravelImageCache\Http\Controllers\ServeImageController;
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

        $this->app->alias('imagecache', ImageCache::class);
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

        Route::get('/' . trim($route, '/') . '/{template}/{path}', ServeImageController::class)
            ->where('path', '.*')
            ->name('imagecache.serve');
    }
}

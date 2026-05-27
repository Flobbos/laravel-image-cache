<?php

namespace Flobbos\LaravelImageCache\Tests;

use Flobbos\LaravelImageCache\ImageCacheServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ImageCacheServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('cache.default', 'array');
        $app['config']->set('imagecache.paths', [$this->fixturesPath()]);
    }

    protected function fixturesPath(): string
    {
        return __DIR__ . '/fixtures';
    }

    protected function createFixtureImage(string $relativePath = 'test.jpg'): string
    {
        $fixturesDir = $this->fixturesPath();

        if (! is_dir($fixturesDir)) {
            mkdir($fixturesDir, 0777, true);
        }

        $path = $fixturesDir . DIRECTORY_SEPARATOR . $relativePath;
        $dir = dirname($path);

        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (! file_exists($path)) {
            $image = imagecreatetruecolor(20, 20);
            imagefilledrectangle($image, 0, 0, 19, 19, imagecolorallocate($image, 255, 0, 0));
            imagejpeg($image, $path);
            imagedestroy($image);
        }

        return $path;
    }
}

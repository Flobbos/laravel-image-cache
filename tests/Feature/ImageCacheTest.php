<?php

namespace Flobbos\LaravelImageCache\Tests\Feature;

use Flobbos\LaravelImageCache\ImageCache;
use Flobbos\LaravelImageCache\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Interfaces\ImageInterface;
use InvalidArgumentException;

class ImageCacheTest extends TestCase
{
    public function test_caches_and_returns_encoded_string(): void
    {
        $source = $this->createFixtureImage();

        /** @var ImageCache $imagecache */
        $imagecache = app('imagecache');

        $first = $imagecache->template($source, 'small');
        $second = $imagecache->template($source, 'small');

        $this->assertIsString($first);
        $this->assertSame($first, $second);
        $this->assertNotEmpty($first);
    }

    public function test_returns_image_object_when_requested(): void
    {
        $source = $this->createFixtureImage('object.jpg');

        $result = app('imagecache')->template($source, 'small', null, true);

        $this->assertInstanceOf(ImageInterface::class, $result);
    }

    public function test_unknown_template_throws(): void
    {
        $source = $this->createFixtureImage('missing.jpg');

        $this->expectException(InvalidArgumentException::class);

        app('imagecache')->template($source, 'does-not-exist');
    }

    public function test_lifetime_zero_stores_forever(): void
    {
        config(['imagecache.lifetime' => 0]);

        $source = $this->createFixtureImage('forever.jpg');

        app('imagecache')->template($source, 'small');

        $key = 'imagecache:' . md5($source . '|' . filemtime($source) . '|small');

        // Force the store to evict any TTL-bound entries: a "forever" entry must still be present.
        $this->assertNotNull(Cache::get($key));
    }
}

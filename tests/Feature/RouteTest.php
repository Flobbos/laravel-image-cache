<?php

namespace Flobbos\LaravelImageCache\Tests\Feature;

use Flobbos\LaravelImageCache\Tests\TestCase;

class RouteTest extends TestCase
{
    public function test_serves_image_with_cache_headers(): void
    {
        $this->createFixtureImage('served.jpg');

        $response = $this->get('/images/small/served.jpg');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/jpeg');

        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertNotNull($cacheControl);
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age=600', $cacheControl);

        $this->assertNotEmpty($response->headers->get('ETag'));
        $this->assertNotEmpty($response->headers->get('Last-Modified'));
    }

    public function test_returns_404_when_image_missing(): void
    {
        $response = $this->get('/images/small/does-not-exist.jpg');

        $response->assertNotFound();
    }

    public function test_refuses_path_traversal(): void
    {
        // Place the "secret" file as a sibling of the fixtures dir so a `..` traversal can resolve to it.
        $outsideDir = dirname($this->fixturesPath()) . '/outside-fixtures';

        if (! is_dir($outsideDir)) {
            mkdir($outsideDir, 0777, true);
        }

        $secret = $outsideDir . '/secret.jpg';

        $image = imagecreatetruecolor(5, 5);
        imagejpeg($image, $secret);
        imagedestroy($image);

        // The configured base path is tests/fixtures; this resolves to tests/outside-fixtures/secret.jpg,
        // which exists but is outside the base — must be refused.
        $response = $this->get('/images/small/' . rawurlencode('../outside-fixtures/secret.jpg'));

        $response->assertNotFound();

        @unlink($secret);
        @rmdir($outsideDir);
    }

    public function test_named_route_exists(): void
    {
        $this->assertTrue($this->app['router']->has('imagecache.serve'));
    }
}

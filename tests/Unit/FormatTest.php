<?php

namespace Flobbos\LaravelImageCache\Tests\Unit;

use Flobbos\LaravelImageCache\Support\Format;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function test_maps_known_formats_to_mime_types(): void
    {
        $this->assertSame('image/jpeg', Format::mime('jpg'));
        $this->assertSame('image/jpeg', Format::mime('jpeg'));
        $this->assertSame('image/png', Format::mime('png'));
        $this->assertSame('image/gif', Format::mime('gif'));
        $this->assertSame('image/webp', Format::mime('webp'));
        $this->assertSame('image/avif', Format::mime('avif'));
    }

    public function test_unknown_format_defaults_to_jpeg(): void
    {
        $this->assertSame('image/jpeg', Format::mime('bmp'));
    }

    public function test_format_lookup_is_case_insensitive(): void
    {
        $this->assertSame('image/webp', Format::mime('WEBP'));
    }

    public function test_normalize_collapses_jpeg_to_jpg(): void
    {
        $this->assertSame('jpg', Format::normalize('jpeg'));
        $this->assertSame('jpg', Format::normalize('JPEG'));
        $this->assertSame('png', Format::normalize('PNG'));
    }
}

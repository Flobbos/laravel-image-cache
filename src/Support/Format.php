<?php

namespace Flobbos\LaravelImageCache\Support;

class Format
{
    public const MIME_TYPES = [
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'avif' => 'image/avif',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
    ];

    public static function mime(string $format): string
    {
        return self::MIME_TYPES[strtolower($format)] ?? 'image/jpeg';
    }

    public static function normalize(string $format): string
    {
        $format = strtolower($format);

        return $format === 'jpeg' ? 'jpg' : $format;
    }
}

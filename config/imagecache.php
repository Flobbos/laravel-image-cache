<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache Lifetime
    |--------------------------------------------------------------------------
    |
    | Default lifetime for cached images in minutes. Set to 0 for no expiration.
    |
    */
    'lifetime' => 10,

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | The driver to use for image processing. Options: 'gd', 'imagick', 'libvips'.
    | Ensure the selected driver is installed and supported on your system.
    |
    */
    'driver' => 'gd',

    /*
    |--------------------------------------------------------------------------
    | Cache Store
    |--------------------------------------------------------------------------
    |
    | The Laravel cache store to use (e.g., 'file', 'redis', 'memcached').
    | Leave null to use the default store defined in config/cache.php.
    |
    */
    'store' => null,

    /*
    |--------------------------------------------------------------------------
    | Default Output Format
    |--------------------------------------------------------------------------
    |
    | The default format for cached images if not specified.
    | Options: 'jpg', 'png', 'gif', 'webp', etc.
    |
    */
    'format' => 'jpg',

    /*
    |--------------------------------------------------------------------------
    | Return Object
    |--------------------------------------------------------------------------
    |
    | Whether to return an Image object by default instead of encoded data.
    |
    */
    'return_object' => false,

    /*
    |--------------------------------------------------------------------------
    | Templates
    |--------------------------------------------------------------------------
    |
    | Define image manipulation templates as class names. Each class should
    | implement a `build` method that accepts an ImageInterface instance.
    |
    */
    'templates' => [
        'small' => \Flobbos\LaravelImageCache\Templates\Small::class,
        'medium' => \Flobbos\LaravelImageCache\Templates\Medium::class,
        // Add more templates here
    ],

    'paths' => [
        storage_path('app/public/photos'),
    ],
];

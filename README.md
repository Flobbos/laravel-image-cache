# Laravel Image Cache

Laravel Image Cache is a package that provides an easy-to-use caching mechanism for image manipulation in Laravel. It serves as a replacement for the now-abandoned imagecache package by Intervention.

## Features

-   Cache manipulated images for improved performance.
-   Support for multiple image drivers: GD, Imagick, and Libvips.
-   Define custom image manipulation templates.
-   Configurable cache lifetime and output formats.
-   Dynamic routing for serving cached images.

## Installation

1. Install the package via Composer:

    ```bash
    composer require flobbos/laravel-image-cache
    ```

2. Publish the configuration file:

    ```bash
    php artisan vendor:publish --tag=config --provider="Flobbos\LaravelImageCache\ImageCacheServiceProvider"
    ```

3. Configure the `config/imagecache.php` file as needed.

## Usage

### Basic Example

To use the package, you can define templates in the configuration file and use the `ImageCache` facade to generate cached images.

```php
use Flobbos\LaravelImageCache\Facades\ImageCache;

// Example: Apply the "small" template to an image
$cachedImage = ImageCache::template('path/to/image.jpg', 'small');
```

### Dynamic Image Routes

The package provides a dynamic route for serving cached images. For example:

```
/images/{template}/{path}
```

-   `template`: The name of the template defined in the configuration file.
-   `path`: The relative path to the image in the `public/images` directory.

Example URL:

```
http://your-app.test/images/small/example.jpg
```

### Custom Templates

You can define custom templates by creating a class that implements a `build` method. For example:

```php
namespace App\ImageTemplates;

use Intervention\Image\Interfaces\ImageInterface;

class CustomTemplate
{
    public function build(ImageInterface $image)
    {
        $image->resize(800, 600);
    }
}
```

Then, register the template in the `config/imagecache.php` file:

```php
'templates' => [
    'custom' => \App\ImageTemplates\CustomTemplate::class,
],
```

## Configuration

The configuration file `config/imagecache.php` allows you to customize:

-   Cache lifetime
-   Image processing driver
-   Cache store
-   Default output format
-   Return object behavior
-   Image manipulation templates
-   **Paths to search for images**

### Paths

You can specify one or more directories where the package should look for images.  
Example from `config/imagecache.php`:

```php
'paths' => [
    public_path('images'),
    storage_path('app/public/images'),
],
```

When a request is made, the package will search these directories for the requested image file in the order listed.

## Requirements

-   PHP 8.1 or higher
-   Laravel 10 or higher
-   Intervention Image 3.0 or higher

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

-   Developed by Alexej Krzewitzki ([alexej@helloo.it](mailto:alexej@helloo.it))
-   Inspired by the original imagecache package by Intervention

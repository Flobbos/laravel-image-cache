# Laravel Image Cache

Laravel Image Cache is a package that provides an easy-to-use caching mechanism for image manipulation in Laravel. It serves as a replacement for the now-abandoned imagecache package by Intervention.

## Features

-   Cache manipulated images for improved performance.
-   Support for multiple image drivers: GD, Imagick, and Libvips.
-   Define custom image manipulation templates via a clean interface.
-   Configurable cache lifetime and output formats (JPEG, PNG, GIF, WebP, AVIF).
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

// Apply the "small" template to an image
$cachedImage = ImageCache::template('path/to/image.jpg', 'small');
```

### Dynamic Image Routes

The package provides a dynamic route for serving cached images. For example:

```
/images/{template}/{path}
```

-   `template`: The name of the template defined in the configuration file.
-   `path`: The relative path to the image in the configured paths.

Example URL:

```
http://your-app.test/images/small/example.jpg
```

### Custom Templates

Create a class that implements `TemplateInterface`:

```php
namespace App\ImageTemplates;

use Flobbos\LaravelImageCache\Contracts\TemplateInterface;
use Intervention\Image\Interfaces\ImageInterface;

class CustomTemplate implements TemplateInterface
{
    public function build(ImageInterface $image): void
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

-   **lifetime**: Cache lifetime in minutes. Set to `0` to cache forever.
-   **driver**: Image processing driver (`gd`, `imagick`, or `libvips`).
-   **store**: Cache store to use (from your Laravel cache config).
-   **format**: Default output format (`jpg`, `png`, `gif`, `webp`, `avif`).
-   **quality**: Encoder quality (1–100) for JPEG/WebP/AVIF. `null` uses Intervention defaults.
-   **return_object**: Whether to return an Intervention Image object or the encoded bytes.
-   **templates**: Image manipulation templates (must implement `TemplateInterface`).
-   **paths**: Directories to search for images (see below).
-   **dynamic_route**: The URL prefix for the dynamic image route.

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

### Dynamic Image Routes

By default, the package registers a dynamic route for serving cached images:

```
/images/{template}/{path}
```

-   `template`: The name of the template defined in your configuration.
-   `path`: The relative path to the image file.

**Example URL:**

```
http://your-app.test/images/small/example.jpg
```

#### Customizing the Route

You can change the route prefix by editing the `route` option in `config/imagecache.php`:

```php
'dynamic_route' => 'imgcache',
```

This will make your URLs look like:

```
http://your-app.test/imgcache/small/example.jpg
```

The route is named `imagecache.serve`, so you can also generate URLs with:

```php
route('imagecache.serve', ['template' => 'small', 'path' => 'example.jpg']);
```

## Security

The dynamic route resolves the requested image against the configured `paths`
using `realpath()` and refuses any request whose canonical path escapes the
configured base directories. Path traversal attempts (e.g. `../../etc/passwd`)
return a 404.

## Requirements

-   PHP 8.2 or higher
-   Laravel 10, 11, 12, or 13
-   Intervention Image 3.0 or higher

### Supported versions

The package declares broad version constraints, but the actual PHP × Laravel
matrix exercised in CI is:

| PHP | Laravel 10 | Laravel 11 | Laravel 12 | Laravel 13 |
| --- | :---: | :---: | :---: | :---: |
| 8.2 | ✓ | ✓ | ✓ | — (L13 requires PHP 8.3+) |
| 8.3 | ✓ | ✓ | ✓ | ✓ |
| 8.4 | — (L10 predates PHP 8.4) | ✓ | ✓ | ✓ |

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

-   Developed by Alexej Krzewitzki ([alexej@helloo.it](mailto:alexej@helloo.it))
-   Inspired by the original imagecache package by Intervention

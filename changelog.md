## Version History

### v2.0.0

**Breaking**

-   Minimum PHP version bumped from `^8.1` to `^8.2`. This only affects Laravel 10 consumers running on PHP 8.1; Laravel 11+ already required 8.2.

**Security**

-   Dynamic route now refuses path traversal — resolved paths must stay within a configured base directory. Previously, a crafted URL could read files outside the configured `paths`. Upgrading is recommended.

**Fixes**

-   `lifetime = 0` now caches forever instead of expiring immediately.
-   Removed `has()` + `get()` race condition; a single `get()` + null check is used instead.

**Improvements**

-   Dynamic route is now an invokable controller (`ServeImageController`), so `php artisan route:cache` works.
-   Cached responses now send `Cache-Control`, `Last-Modified`, and `ETag` headers.
-   New `quality` config option for JPEG/WebP/AVIF encoders.
-   Format / MIME-type mapping centralized in `Support\Format` (no more duplication).
-   Container binding `imagecache` is aliased to the `ImageCache` class for constructor injection.

**DX**

-   Added PHPUnit + Testbench test suite and GitHub Actions CI matrix (PHP 8.2–8.4 × Laravel 10–13).
-   Fixed README drift (`return_obj` → `return_object`, `route` → `dynamic_route`) and added a supported-versions matrix.

### v1.0.0

-   Laravel 13 support
-   Added `TemplateInterface` contract for templates
-   Fixed critical performance bug in cache key generation (no longer reads image for key)
-   Removed base64 encoding overhead from cache storage
-   Added AVIF format support
-   Added typed properties and return types throughout
-   Added `mergeConfigFrom()` for zero-config usage
-   Added route name `imagecache.serve`
-   Removed undocumented `__call` magic proxy
-   Added facade docblock annotations for IDE support
-   Fixed `composer.json` structure (type, autoload, stability)
-   Templates are now resolved via the container

### v0.0.3

-   Added dynamic route option
-   Updated Readme

### v0.0.2

-   First actual working release

### v0.0.1

-   Initial release

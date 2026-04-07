## Version History

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

<?php

namespace Flobbos\LaravelImageCache\Templates;

use Intervention\Image\Interfaces\ImageInterface;

class Small
{
    public function build(ImageInterface $image)
    {
        $image->scale(300, 200); // Example: Resize to 300x200
    }
}

<?php

namespace Flobbos\LaravelImageCache\Templates;

use Intervention\Image\Interfaces\ImageInterface;

class Medium
{
    public function build(ImageInterface $image)
    {
        $image->scale(600, 400); // Example: Resize to 600x400
    }
}

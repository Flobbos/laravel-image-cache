<?php

namespace Flobbos\LaravelImageCache\Templates;

use Flobbos\LaravelImageCache\Contracts\TemplateInterface;
use Intervention\Image\Interfaces\ImageInterface;

class Small implements TemplateInterface
{
    public function build(ImageInterface $image): void
    {
        $image->scale(300, 200);
    }
}

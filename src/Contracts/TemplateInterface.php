<?php

namespace Flobbos\LaravelImageCache\Contracts;

use Intervention\Image\Interfaces\ImageInterface;

interface TemplateInterface
{
    /**
     * Apply image manipulations to the given image instance.
     */
    public function build(ImageInterface $image): void;
}

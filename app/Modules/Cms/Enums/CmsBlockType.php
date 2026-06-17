<?php

namespace App\Modules\Cms\Enums;

enum CmsBlockType: string
{
    case Heading = 'heading';
    case Hero = 'hero';
    case Image = 'image';
    case Text = 'text';
}

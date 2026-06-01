<?php

namespace App\Modules\Cms\Enums;

enum CmsPageStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}

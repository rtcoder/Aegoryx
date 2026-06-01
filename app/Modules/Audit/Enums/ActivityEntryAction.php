<?php

namespace App\Modules\Audit\Enums;

enum ActivityEntryAction: string
{
    case CmsPageCreated = 'cms_page_created';
    case CmsPagePublished = 'cms_page_published';
    case CmsPageUnpublished = 'cms_page_unpublished';
    case CmsPageUpdated = 'cms_page_updated';
}

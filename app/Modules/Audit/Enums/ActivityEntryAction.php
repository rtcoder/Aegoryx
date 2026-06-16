<?php

namespace App\Modules\Audit\Enums;

enum ActivityEntryAction: string
{
    case CmsPageCreated = 'cms_page_created';
    case CmsPagePublished = 'cms_page_published';
    case CmsPageUnpublished = 'cms_page_unpublished';
    case CmsPageUpdated = 'cms_page_updated';
    case CrmCompanyCreated = 'crm_company_created';
    case CrmCompanyDeleted = 'crm_company_deleted';
    case CrmCompanyUpdated = 'crm_company_updated';
    case CrmContactCreated = 'crm_contact_created';
    case CrmContactDeleted = 'crm_contact_deleted';
    case CrmContactUpdated = 'crm_contact_updated';
    case CrmDealCreated = 'crm_deal_created';
    case CrmDealDeleted = 'crm_deal_deleted';
    case CrmDealUpdated = 'crm_deal_updated';
}

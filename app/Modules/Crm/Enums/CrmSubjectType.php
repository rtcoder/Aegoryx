<?php

namespace App\Modules\Crm\Enums;

use App\Models\Tenant\CrmCompany;
use App\Models\Tenant\CrmContact;
use App\Models\Tenant\CrmDeal;

enum CrmSubjectType: string
{
    case Company = 'company';
    case Contact = 'contact';
    case Deal = 'deal';

    public function modelClass(): string
    {
        return match ($this) {
            self::Company => CrmCompany::class,
            self::Contact => CrmContact::class,
            self::Deal => CrmDeal::class,
        };
    }

    public function table(): string
    {
        return match ($this) {
            self::Company => 'crm_companies',
            self::Contact => 'crm_contacts',
            self::Deal => 'crm_deals',
        };
    }
}

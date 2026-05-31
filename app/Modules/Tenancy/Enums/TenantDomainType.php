<?php

namespace App\Modules\Tenancy\Enums;

enum TenantDomainType: string
{
    case Primary = 'primary';
    case Alias = 'alias';
}

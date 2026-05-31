<?php

namespace App\Modules\Tenancy\Enums;

enum TenantDomainStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Disabled = 'disabled';
}

<?php

namespace App\Modules\Tenancy\Enums;

enum TenantBillingModel: string
{
    case Subscription = 'subscription';
    case Perpetual = 'perpetual';
    case Internal = 'internal';
}

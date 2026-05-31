<?php

namespace App\Modules\Tenancy\Enums;

enum TenantLicenseType: string
{
    case SaasSubscription = 'saas_subscription';
    case SelfHostedSubscription = 'self_hosted_subscription';
    case SelfHostedPerpetual = 'self_hosted_perpetual';
}

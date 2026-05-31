<?php

namespace App\Modules\Tenancy\Enums;

enum TenantDeploymentType: string
{
    case Saas = 'saas';
    case SelfHosted = 'self_hosted';
}

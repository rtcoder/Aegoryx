<?php

namespace App\Modules\Entitlements\Enums;

enum TenantFeatureSource: string
{
    case Plan = 'plan';
    case License = 'license';
    case Manual = 'manual';
    case Trial = 'trial';
    case System = 'system';
}

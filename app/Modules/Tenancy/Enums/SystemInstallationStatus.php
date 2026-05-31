<?php

namespace App\Modules\Tenancy\Enums;

enum SystemInstallationStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}

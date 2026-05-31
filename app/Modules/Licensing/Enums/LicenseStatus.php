<?php

namespace App\Modules\Licensing\Enums;

enum LicenseStatus: string
{
    case Inactive = 'inactive';
    case Active = 'active';
    case Grace = 'grace';
    case Expired = 'expired';
    case Suspended = 'suspended';
}

<?php

namespace App\Modules\Identity\Enums;

enum IdentityStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Disabled = 'disabled';
}

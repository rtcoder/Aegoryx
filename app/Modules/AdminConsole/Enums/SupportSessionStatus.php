<?php

namespace App\Modules\AdminConsole\Enums;

enum SupportSessionStatus: string
{
    case Active = 'active';
    case Ended = 'ended';
    case Expired = 'expired';
}

<?php

namespace App\Modules\Billing\Enums;

enum BillingProvider: string
{
    case Paddle = 'paddle';
    case Manual = 'manual';
}

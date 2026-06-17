<?php

namespace App\Modules\Billing\Enums;

enum BillingEventStatus: string
{
    case Processed = 'processed';
    case Duplicate = 'duplicate';
    case Failed = 'failed';
}

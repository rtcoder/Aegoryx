<?php

namespace App\Modules\Crm\Enums;

enum CrmTaskStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}

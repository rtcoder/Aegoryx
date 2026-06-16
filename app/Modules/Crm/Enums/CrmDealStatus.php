<?php

namespace App\Modules\Crm\Enums;

enum CrmDealStatus: string
{
    case Open = 'open';
    case Won = 'won';
    case Lost = 'lost';
}

<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('landlord.dashboard');
    }
}

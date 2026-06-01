<?php

namespace App\Modules\TenantPanel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('tenant.dashboard', [
            'tenant' => $request->attributes->get('tenant'),
        ]);
    }
}

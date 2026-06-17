<?php

namespace App\Modules\TenantPanel\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class SecurityController extends Controller
{
    public function index(): View
    {
        return view('tenant.security.index');
    }
}

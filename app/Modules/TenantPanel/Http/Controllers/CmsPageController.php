<?php

namespace App\Modules\TenantPanel\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;

final class CmsPageController extends Controller
{
    public function index(): View
    {
        return view('tenant.cms.pages.index');
    }
}

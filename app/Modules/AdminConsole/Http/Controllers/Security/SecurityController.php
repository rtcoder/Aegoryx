<?php

namespace App\Modules\AdminConsole\Http\Controllers\Security;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

final class SecurityController extends Controller
{
    public function index(): View
    {
        return view('landlord.security.index');
    }
}

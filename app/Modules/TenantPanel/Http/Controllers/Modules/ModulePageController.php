<?php

namespace App\Modules\TenantPanel\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ModulePageController extends Controller
{
    public function cms(Request $request): View
    {
        return $this->module($request, 'CMS', 'Pages, publishing, revisions.');
    }

    public function crm(Request $request): View
    {
        return $this->module($request, 'CRM', 'Contacts, companies, deals.');
    }

    public function files(Request $request): View
    {
        return $this->module($request, 'Files', 'Private storage and downloads.');
    }

    public function settings(Request $request): View
    {
        return $this->module($request, 'Settings', 'Workspace configuration.');
    }

    private function module(Request $request, string $title, string $description): View
    {
        return view('tenant.modules.placeholder', [
            'tenant' => $request->attributes->get('tenant'),
            'title' => $title,
            'description' => $description,
        ]);
    }
}

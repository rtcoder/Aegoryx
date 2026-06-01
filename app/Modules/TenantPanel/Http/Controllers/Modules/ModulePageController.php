<?php

namespace App\Modules\TenantPanel\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ModulePageController extends Controller
{
    public function cms(Request $request): View
    {
        return $this->module($request, __('tenant_panel.nav.cms'), __('tenant_panel.nav.cms_description'));
    }

    public function crm(Request $request): View
    {
        return $this->module($request, __('tenant_panel.nav.crm'), __('tenant_panel.nav.crm_description'));
    }

    public function files(Request $request): View
    {
        return $this->module($request, __('tenant_panel.nav.files'), __('tenant_panel.nav.files_description'));
    }

    public function settings(Request $request): View
    {
        return $this->module($request, __('tenant_panel.nav.settings'), __('tenant_panel.nav.settings_description'));
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

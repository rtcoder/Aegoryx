<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class SectionController extends Controller
{
    public function tenants(): View
    {
        return $this->section('Tenants', 'Manage tenant accounts, domains, deployment state, and support entry points.');
    }

    public function features(): View
    {
        return $this->section('Features', 'Manage global features and tenant-specific feature overrides.');
    }

    public function licenses(): View
    {
        return $this->section('Licenses', 'Review license state, verification status, and self-hosted access.');
    }

    public function billing(): View
    {
        return $this->section('Billing', 'Inspect plans, subscriptions, billing state, and provider sync status.');
    }

    public function support(): View
    {
        return $this->section('Support', 'Start audited support sessions and review support access history.');
    }

    private function section(string $title, string $description): View
    {
        return view('landlord.section', [
            'title' => $title,
            'description' => $description,
        ]);
    }
}

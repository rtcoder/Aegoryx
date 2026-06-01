<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class SectionController extends Controller
{
    public function tenants(): View
    {
        return $this->section(__('common.tenants'), __('landlord.sections.tenants'));
    }

    public function features(): View
    {
        return $this->section(__('common.features'), __('landlord.sections.features'));
    }

    public function licenses(): View
    {
        return $this->section(__('common.licenses'), __('landlord.sections.licenses'));
    }

    public function billing(): View
    {
        return $this->section(__('common.billing'), __('landlord.sections.billing'));
    }

    public function support(): View
    {
        return view('landlord.support.index');
    }

    private function section(string $title, string $description): View
    {
        return view('landlord.section', [
            'title' => $title,
            'description' => $description,
        ]);
    }
}

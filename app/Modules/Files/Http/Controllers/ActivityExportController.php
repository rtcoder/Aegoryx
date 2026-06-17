<?php

namespace App\Modules\Files\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Files\Actions\CreateActivityExportAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ActivityExportController extends Controller
{
    public function store(Request $request, CreateActivityExportAction $createExport): RedirectResponse
    {
        $createExport->handle($request->user());

        return redirect()
            ->route('tenant.files.index')
            ->with('success', __('flash.activity_export_created'));
    }
}

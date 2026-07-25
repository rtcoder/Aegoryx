<?php

namespace App\Modules\AdminConsole\Http\Controllers;

use App\Models\System\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $action = $request->string('action')->toString();

        return view('admin.audit.index', [
            'action' => $action,
            'entries' => AuditLog::query()
                ->when($action !== '', fn ($query) => $query->where('action', $action))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }
}

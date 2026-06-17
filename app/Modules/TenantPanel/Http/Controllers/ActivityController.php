<?php

namespace App\Modules\TenantPanel\Http\Controllers;

use App\Models\Tenant\ActivityEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

final class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ActivityEntry::class);

        $action = $request->string('action')->toString();

        return view('tenant.activity.index', [
            'action' => $action,
            'entries' => ActivityEntry::query()
                ->when($action !== '', fn ($query) => $query->where('action', $action))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }
}

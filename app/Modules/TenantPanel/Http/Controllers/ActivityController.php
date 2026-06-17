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
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $sortColumns = [
            'created_at' => 'created_at',
            'action' => 'action',
            'actor' => 'actor_id',
            'subject' => 'subject_type',
        ];
        $sortColumn = $sortColumns[$sort] ?? 'created_at';

        return view('tenant.activity.index', [
            'action' => $action,
            'sort' => array_key_exists($sort, $sortColumns) ? $sort : 'created_at',
            'direction' => $direction,
            'entries' => ActivityEntry::query()
                ->when($action !== '', fn ($query) => $query->where('action', $action))
                ->orderBy($sortColumn, $direction)
                ->paginate(20)
                ->withQueryString(),
        ]);
    }
}

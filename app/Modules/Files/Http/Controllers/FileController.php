<?php

namespace App\Modules\Files\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantFile;
use App\Modules\Files\Actions\DeleteFileMetadataAction;
use App\Modules\Files\Actions\DownloadPrivateFileAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FileController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TenantFile::class);

        return view('tenant.files.index', [
            'tenant' => $request->attributes->get('tenant'),
            'files' => TenantFile::query()
                ->with('owner')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function download(Request $request, TenantFile $file, DownloadPrivateFileAction $download): StreamedResponse
    {
        return $download->handle($file, $request->user());
    }

    public function destroy(Request $request, TenantFile $file, DeleteFileMetadataAction $deleteFile): RedirectResponse
    {
        $deleteFile->handle($file, $request->user());

        return redirect()
            ->route('tenant.files.index')
            ->with('success', __('flash.file_deleted'));
    }
}

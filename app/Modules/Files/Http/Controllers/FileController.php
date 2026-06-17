<?php

namespace App\Modules\Files\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantFile;
use App\Modules\Files\Actions\DeleteFileMetadataAction;
use App\Modules\Files\Actions\DownloadPrivateFileAction;
use App\Modules\Files\Actions\RegisterFileMetadataAction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class FileController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', TenantFile::class);

        $search = trim($request->string('search')->toString());

        return view('tenant.files.index', [
            'search' => $search,
            'tenant' => $request->attributes->get('tenant'),
            'files' => TenantFile::query()
                ->with('owner')
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                    $query
                        ->where('original_name', 'like', "%{$search}%")
                        ->orWhere('mime_type', 'like', "%{$search}%");
                }))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ]);
    }

    public function download(Request $request, TenantFile $file, DownloadPrivateFileAction $download): StreamedResponse
    {
        return $download->handle($file, $request->user());
    }

    public function store(Request $request, RegisterFileMetadataAction $registerFile): RedirectResponse
    {
        Gate::authorize('create', TenantFile::class);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $uploadedFile = $validated['file'];
        $tenant = $request->attributes->get('tenant');
        $directory = 'tenant/'.$tenant->slug.'/uploads';
        $path = $uploadedFile->storeAs(
            $directory,
            Str::uuid()->toString().'.'.$uploadedFile->getClientOriginalExtension(),
            'local',
        );

        $registerFile->handle(
            disk: 'local',
            path: $path,
            originalName: $uploadedFile->getClientOriginalName(),
            mimeType: $uploadedFile->getClientMimeType(),
            ownerId: $request->user()->id,
            actor: $request->user(),
        );

        return redirect()
            ->route('tenant.files.index')
            ->with('success', __('files.uploaded'));
    }

    public function destroy(Request $request, TenantFile $file, DeleteFileMetadataAction $deleteFile): RedirectResponse
    {
        $deleteFile->handle($file, $request->user());

        return redirect()
            ->route('tenant.files.index')
            ->with('success', __('flash.file_deleted'));
    }
}

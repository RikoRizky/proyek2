<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Module;
use App\Models\SupportingDocument;
use App\Services\SupportingDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ManagesSupportingDocuments
{
    use SendsSubmissionFile;

    abstract protected function archiveRoutePrefix(): string;

    protected function supportingDocumentService(): SupportingDocumentService
    {
        return app(SupportingDocumentService::class);
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $prefix = $this->archiveRoutePrefix();
        $service = $this->supportingDocumentService();

        $documents = $service->visibleQueryFor($user)
            ->with(['perti.user', 'prodi.user', 'uploader', 'requirement.module'])
            ->latest()
            ->get();

        $formattedDocuments = $documents->map(function (SupportingDocument $doc) use ($user, $prefix, $service) {
            $files = $service->formatFilesForRecord($doc, $prefix);
            $links = is_array($doc->google_drive_links) ? array_values($doc->google_drive_links) : [];
            $primaryFile = $files[0] ?? null;

            $reqLabel = null;
            if ($doc->requirement) {
                $reqLabel = $doc->requirement->module?->shortLabel()
                    ? $doc->requirement->module->shortLabel().' · '.$doc->requirement->title
                    : $doc->requirement->title;
            }

            $fileNamesText = implode(' ', array_map(fn ($f) => $f['filename'] ?? '', $files));
            $linkNamesText = implode(' ', array_map(fn ($l) => ($l['name'] ?? '').' '.($l['url'] ?? ''), $links));

            return [
                'id' => $doc->id,
                'title' => $doc->title,
                'category' => $doc->category,
                'academic_year' => $doc->academic_year ?? '',
                'description' => $doc->description ?? '',
                'prodi_id' => $doc->prodi_id ? (string) $doc->prodi_id : 'shared',
                'scope_label' => $doc->scopeLabel(),
                'is_pt_shared' => $doc->isPtShared(),
                'uploader_name' => $doc->uploader?->name ?? 'Unit Akreditasi',
                'requirement_id' => $doc->requirement_id ? (string) $doc->requirement_id : '',
                'requirement_label' => $reqLabel,
                'table_code' => $doc->table_code ?? '',
                'updated_label' => $doc->updated_at?->diffForHumans() ?? '-',
                'updated_iso' => $doc->updated_at?->toIso8601String() ?? '',
                'can_edit' => $doc->canBeEditedBy($user),
                'files' => $files,
                'google_drive_links' => $links,
                'update_url' => route("{$prefix}.archives.update", $doc),
                'destroy_url' => route("{$prefix}.archives.destroy", $doc),
                'view_url' => route("{$prefix}.archives.view", $doc),
                'primary_view_url' => $primaryFile['view_url'] ?? route("{$prefix}.archives.view", $doc),
                'primary_download_url' => $primaryFile['download_url'] ?? null,
                'search_text' => mb_strtolower(implode(' ', array_filter([
                    $doc->title,
                    $doc->category,
                    $doc->academic_year,
                    $doc->description,
                    $doc->table_code ? 'Tabel '.$doc->table_code : null,
                    $doc->table_code,
                    $doc->scopeLabel(),
                    $reqLabel,
                    $fileNamesText,
                    $linkNamesText,
                ]))),
            ];
        })->values();

        $modules = Module::query()
            ->orderBy('sort_order')
            ->with(['requirements' => fn ($q) => $q->orderBy('sort_order')])
            ->get();

        $tableOptions = $modules->flatMap(function (Module $module) {
            return $module->requirements
                ->filter(fn ($req) => $req->isTable() && ! empty($req->table_code))
                ->map(fn ($req) => [
                    'table_code' => $req->table_code,
                    'label' => 'Tabel '.$req->table_code.': '.$req->title,
                    'module_name' => $module->shortLabel(),
                ]);
        })->values();

        $requirementOptions = $modules->flatMap(function (Module $module) {
            return $module->requirements->map(fn ($req) => [
                'id' => (string) $req->id,
                'label' => $module->shortLabel().' · '.$req->title,
                'table_code' => $req->isTable() ? ($req->table_code ?? '') : '',
            ]);
        })->values();

        $prodis = collect();
        if ($user->isPerti() && $user->pertiProfile) {
            $prodis = $user->pertiProfile->prodis()
                ->with('user')
                ->get()
                ->sortBy(fn ($p) => mb_strtolower($p->name))
                ->map(fn ($p) => [
                    'id' => (string) $p->id,
                    'name' => $p->name,
                ])
                ->values();
        }

        return view('archives.index', [
            'routePrefix' => $prefix,
            'isPerti' => $user->isPerti(),
            'isStarterPackage' => $user->effective_package === 'Starter',
            'documents' => $formattedDocuments,
            'categories' => $service->availableCategories($user),
            'defaultCategories' => SupportingDocument::DEFAULT_CATEGORIES,
            'academicYears' => SupportingDocument::ACADEMIC_YEARS,
            'presets' => SupportingDocument::PRESETS,
            'prodis' => $prodis,
            'tableOptions' => $tableOptions,
            'requirementOptions' => $requirementOptions,
            'initialQuery' => (string) $request->query('q', ''),
            'initialCategory' => (string) $request->query('category', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateArchivePayload($request);
        $uploadedFiles = array_values(array_filter(
            (array) $request->file('documents', []),
            fn ($file) => $file !== null
        ));

        $document = $this->supportingDocumentService()->storeForUser(
            $request->user(),
            $validated,
            $uploadedFiles
        );

        return redirect()
            ->route($this->archiveRoutePrefix().'.archives.index')
            ->with('status', 'Dokumen tambahan «'.$document->title.'» berhasil ditambahkan ke arsip.');
    }

    public function update(Request $request, SupportingDocument $supportingDocument): RedirectResponse
    {
        $validated = $this->validateArchivePayload($request);
        $uploadedFiles = array_values(array_filter(
            (array) $request->file('documents', []),
            fn ($file) => $file !== null
        ));
        $keepFiles = array_values(array_filter(
            (array) $request->input('keep_files', []),
            fn ($path) => is_string($path) && $path !== ''
        ));

        $document = $this->supportingDocumentService()->updateForUser(
            $supportingDocument,
            $request->user(),
            $validated,
            $uploadedFiles,
            $keepFiles
        );

        return redirect()
            ->route($this->archiveRoutePrefix().'.archives.index')
            ->with('status', 'Dokumen tambahan «'.$document->title.'» berhasil diperbarui.');
    }

    public function destroy(Request $request, SupportingDocument $supportingDocument): RedirectResponse
    {
        $title = $supportingDocument->title;
        $this->supportingDocumentService()->deleteDocument($supportingDocument, $request->user());

        return redirect()
            ->route($this->archiveRoutePrefix().'.archives.index')
            ->with('status', 'Dokumen tambahan «'.$title.'» berhasil dihapus dari arsip.');
    }

    public function viewer(Request $request, SupportingDocument $supportingDocument): View
    {
        abort_unless($supportingDocument->canBeViewedBy($request->user()), 403);

        $supportingDocument->load(['perti.user', 'prodi.user', 'uploader', 'requirement.module']);
        $prefix = $this->archiveRoutePrefix();
        $service = $this->supportingDocumentService();
        $files = $service->formatFilesForRecord($supportingDocument, $prefix);

        $activeFileIndex = max(0, (int) $request->query('file', 0));
        if (! isset($files[$activeFileIndex])) {
            $activeFileIndex = 0;
        }

        $activeFile = $files[$activeFileIndex] ?? null;

        return view('archives.viewer', [
            'document' => $supportingDocument,
            'routePrefix' => $prefix,
            'files' => $files,
            'driveLinks' => is_array($supportingDocument->google_drive_links)
                ? array_values($supportingDocument->google_drive_links)
                : [],
            'activeFileIndex' => $activeFileIndex,
            'activeFile' => $activeFile,
            'inlineUrl' => $activeFile
                ? route("{$prefix}.archives.inline", [$supportingDocument, 'file' => $activeFileIndex])
                : null,
            'downloadUrl' => $activeFile
                ? route("{$prefix}.archives.download", [$supportingDocument, 'file' => $activeFileIndex])
                : null,
            'inlineUrlTemplate' => route("{$prefix}.archives.inline", $supportingDocument),
            'backUrl' => route("{$prefix}.archives.index"),
        ]);
    }

    public function inline(Request $request, SupportingDocument $supportingDocument): StreamedResponse
    {
        abort_unless($supportingDocument->canBeViewedBy($request->user()), 403);

        $files = $this->supportingDocumentService()->extractStoredFiles($supportingDocument);
        $fileIndex = max(0, (int) $request->query('file', 0));

        return $this->storedFileInlineResponse($files, $fileIndex);
    }

    public function download(Request $request, SupportingDocument $supportingDocument): StreamedResponse
    {
        abort_unless($supportingDocument->canBeViewedBy($request->user()), 403);

        $files = $this->supportingDocumentService()->extractStoredFiles($supportingDocument);
        $fileIndex = max(0, (int) $request->query('file', 0));

        return $this->storedFileDownloadResponse($files, $fileIndex);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateArchivePayload(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:120'],
            'custom_category' => ['nullable', 'string', 'max:120'],
            'academic_year' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:2000'],
            'prodi_id' => ['nullable', 'string', 'max:32'],
            'requirement_id' => ['nullable', 'integer', 'exists:requirements,id'],
            'table_code' => ['nullable', 'string', 'max:30'],
            'google_drive_links' => ['nullable', 'array'],
            'google_drive_links.*.name' => ['nullable', 'string', 'max:255'],
            'google_drive_links.*.url' => ['nullable', 'string', 'max:1000'],
            'documents' => ['nullable', 'array'],
        ], [
            'title.required' => 'Judul dokumen tambahan wajib diisi.',
            'category.required' => 'Kategori dokumen wajib dipilih atau diisi.',
        ]);
    }
}

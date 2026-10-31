<?php

namespace App\Http\Controllers\UnitKerja;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Concerns\SendsSubmissionFile;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\User;
use App\Support\AccreditationUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    use SendsSubmissionFile;

    public function index(): RedirectResponse
    {
        return redirect()->route('dashboard');
    }

    public function module(Module $module): View
    {
        $this->authorizeUnitKerja();

        $module->load(['requirements' => function ($q) {
            $q->orderBy('sort_order')
                ->with(['submissions' => function ($s) {
                    $s->where('user_id', auth()->id())
                        ->latestForUnit();
                }]);
        }]);

        return view('unit.submissions.module', [
            'module' => $module,
        ]);
    }

    public function batchStore(Request $request, Module $module): RedirectResponse
    {
        $this->authorizeUnitKerja();

        if (auth()->user()->effective_package === 'Starter') {
            return redirect()->route('unit.submissions.module', $module)
                ->withErrors(['files' => 'Paket Starter tidak mendukung unggah file langsung. Gunakan link Google Drive.']);
        }

        $module->load(['requirements' => fn ($q) => $q->orderBy('sort_order')]);
        $requirements = $module->requirements->keyBy('id');

        if ($requirements->isEmpty()) {
            return redirect()->route('unit.submissions.module', $module)
                ->with('status', 'Modul ini belum memiliki persyaratan.');
        }

        $files = $request->file('files') ?? [];
        $expectedCount = (int) $request->input('expected_file_count', 0);

        if ($truncatedMessage = AccreditationUpload::truncatedBatchMessage($expectedCount, $files)) {
            return redirect()->route('unit.submissions.module', $module)
                ->withErrors(['files' => $truncatedMessage])
                ->with('upload_partial_failure', true);
        }

        $errors = [];
        $saved = 0;

        DB::transaction(function () use ($files, $requirements, $module, $request, &$errors, &$saved) {
            $user = $request->user();

            foreach ($files as $requirementId => $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }

                if ($file->getError() === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                $requirementId = (int) $requirementId;
                $requirement = $requirements->get($requirementId);

                if (! $requirement) {
                    continue;
                }

                $field = 'files.'.$requirementId;

                if (! $file->isValid()) {
                    $errors[$field] = AccreditationUpload::fieldErrorMessage(
                        $module,
                        $requirement,
                        $file,
                        AccreditationUpload::uploadErrorMessage($file)
                    );

                    continue;
                }

                $validationMessage = AccreditationUpload::validateFile($file);

                if ($validationMessage !== null) {
                    $errors[$field] = AccreditationUpload::fieldErrorMessage(
                        $module,
                        $requirement,
                        $file,
                        $validationMessage
                    );

                    continue;
                }

                $this->persistSubmission($user, $requirement, $file);
                $saved++;
            }
        });

        if ($saved === 0 && $errors === []) {
            return redirect()->route('unit.submissions.module', $module)
                ->withErrors(['files' => 'Pilih minimal satu berkas untuk diunggah pada modul ini.']);
        }

        $redirect = redirect()->route('unit.submissions.module', $module);

        if ($saved > 0) {
            $redirect->with('status', $saved.' berkas modul «'.$module->name.'» berhasil disimpan.');
        }

        if ($errors !== []) {
            $redirect
                ->withErrors($errors)
                ->with('upload_partial_failure', true);
        }

        return $redirect;
    }

    public function store(Request $request, Requirement $requirement): RedirectResponse|JsonResponse
    {
        $this->authorizeUnitKerja();
        $requirement->load('module');

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'google_drive_links' => 'array',
            'google_drive_links.*.name' => 'required_with:google_drive_links.*.url|nullable|string|max:255',
            'google_drive_links.*.url' => 'required_with:google_drive_links.*.name|nullable|url',
            'documents' => 'array',
            'documents.*' => 'file|mimes:pdf,xlsx,xls|max:' . AccreditationUpload::maxUploadKb(),
            'keep_files' => 'array',
        ], [
            'google_drive_links.*.name.required_with' => 'Nama dokumen wajib diisi jika link Google Drive diisi.',
            'google_drive_links.*.url.required_with' => 'Link Google Drive wajib diisi jika nama dokumen diisi.',
            'google_drive_links.*.url.url' => 'Format link Google Drive tidak valid.',
            'documents.*.mimes' => 'Berkas dokumen harus berupa PDF atau Excel (xlsx/xls).',
            'documents.*.max' => 'Berkas dokumen tidak boleh lebih dari ' . AccreditationUpload::maxUploadMb() . ' MB.',
        ]);

        // Filter out empty links
        $driveLinks = collect($request->input('google_drive_links', []))
            ->filter(fn ($link) => !empty($link['name']) || !empty($link['url']))
            ->values()
            ->all();

        // Support single file upload "document" parameter for backward compatibility / tests
        $filesList = $request->file('documents', []);
        if (empty($filesList) && $request->hasFile('document')) {
            $filesList = [$request->file('document')];
        }

        $keepFiles = [];
        foreach ($request->input('keep_files', []) as $keepJson) {
            $decoded = json_decode($keepJson, true);
            if ($decoded && isset($decoded['file_path'])) {
                $keepFiles[] = $decoded;
            }
        }

        if (empty($driveLinks) && empty($filesList) && empty($keepFiles)) {
            $validator->errors()->add('google_drive_links', 'Harap isi minimal satu link Google Drive atau unggah/simpan berkas dokumen.');
        }

        if (auth()->user()->effective_package === 'Starter' && (!empty($filesList) || $request->hasFile('document'))) {
            $validator->errors()->add('documents', 'Paket Starter tidak mendukung unggah file langsung. Gunakan link Google Drive.');
        }

        if ($validator->fails() || (empty($driveLinks) && empty($filesList) && empty($keepFiles))) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $validator->errors()->first() ?: 'Harap isi minimal satu link Google Drive atau unggah/simpan berkas dokumen.'], 422);
            }

            // Collect names of files that were too large so the modal can warn the user
            $oversizedFiles = [];
            $maxKb = AccreditationUpload::maxUploadKb();
            foreach ($request->file('documents', []) as $uploadedFile) {
                if ($uploadedFile && $uploadedFile->getSize() > $maxKb * 1024) {
                    $oversizedFiles[] = $uploadedFile->getClientOriginalName();
                }
            }

            return redirect()->route('unit.submissions.module', $requirement->module)
                ->withErrors($validator)
                ->withInput()
                ->with('failed_requirement_id', $requirement->id)
                ->with('oversized_files', $oversizedFiles);
        }


        $user = $request->user();
        $submission = $this->persistSubmission($user, $requirement, $driveLinks, $filesList, $keepFiles);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Dokumen berhasil diunggah (versi '.$submission->version.').',
                'submission' => [
                    'id' => $submission->id,
                    'version' => $submission->version,
                    'requirement_id' => $requirement->id,
                ],
            ]);
        }

        return redirect()->route('unit.submissions.module', $requirement->module)
            ->with('status', 'Dokumen berhasil diunggah (versi '.$submission->version.').');
    }

    public function show(Submission $submission): View
    {
        $this->authorizeSubmission($submission);

        $history = Submission::query()
            ->where('requirement_id', $submission->requirement_id)
            ->where('user_id', $submission->user_id)
            ->orderByDesc('version')
            ->get();

        $submission->load(['requirement.module']);

        return view('unit.submissions.show', compact('submission', 'history'));
    }

    public function viewer(Submission $submission): View
    {
        $this->authorizeSubmission($submission);

        $submission->load('requirement.module');

        $fileIndex = request()->query('file');
        $activeFileIndex = 0;

        if ($fileIndex !== null) {
            $activeFileIndex = (int) $fileIndex;
        }

        $files = $submission->files ?? [];
        $activeFile = $files[$activeFileIndex] ?? null;

        $filename = $activeFile ? $activeFile['original_filename'] : $submission->original_filename;

        return view('submissions.viewer', [
            'submission' => $submission,
            'title' => $submission->requirement->title,
            'filename' => $filename,
            'activeFileIndex' => $activeFileIndex,
            'routePrefix' => 'unit',
            'inlineUrl' => route('unit.submissions.inline', [$submission, 'file' => $activeFileIndex]),
            'downloadUrl' => route('unit.submissions.download', [$submission, 'file' => $activeFileIndex]),
            'backUrl' => route('unit.submissions.module', $submission->requirement->module),

        ]);
    }

    public function inline(Submission $submission): StreamedResponse
    {
        $this->authorizeSubmission($submission);

        return $this->submissionInlineResponse($submission);
    }

    public function download(Submission $submission): StreamedResponse
    {
        $this->authorizeSubmission($submission);

        return $this->submissionDownloadResponse($submission);
    }

    private function authorizeUnitKerja(): void
    {
        abort_unless(auth()->user()?->role === UserRole::Prodi, 403);
    }

    private function authorizeSubmission(Submission $submission): void
    {
        $user = auth()->user();
        abort_unless($user && $submission->user_id === $user->id, 403);
    }

    private function persistSubmission(
        User $user,
        Requirement $requirement,
        array|UploadedFile $driveLinksOrFile = [],
        array $filesList = [],
        array $keepFiles = []
    ): Submission {
        $driveLinks = [];
        
        if ($driveLinksOrFile instanceof UploadedFile) {
            $filesList = [$driveLinksOrFile];
        } else {
            $driveLinks = $driveLinksOrFile;
        }

        $latest = Submission::query()
            ->where('requirement_id', $requirement->id)
            ->where('user_id', $user->id)
            ->orderByDesc('version')
            ->first();

        $nextVersion = $latest ? $latest->version + 1 : 1;

        if ($latest) {
            $latest->update(['is_latest' => false]);
        }

        $savedFiles = [];

        // Pertama, masukkan file lama yang ingin dipertahankan (retensi berkas)
        foreach ($keepFiles as $fileData) {
            $savedFiles[] = [
                'file_path' => $fileData['file_path'],
                'original_filename' => $fileData['original_filename'],
                'mime_type' => $fileData['mime_type'] ?? null,
                'file_size' => $fileData['file_size'] ?? null,
            ];
        }

        // Kedua, masukkan file baru yang diunggah
        foreach ($filesList as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $path = $file->store("accreditation/{$user->id}/{$requirement->id}", 'local');
                $savedFiles[] = [
                    'file_path' => $path,
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ];
            }
        }

        $firstFile = $savedFiles[0] ?? null;

        return Submission::query()->create([
            'requirement_id' => $requirement->id,
            'user_id' => $user->id,
            'file_path' => $firstFile ? $firstFile['file_path'] : null,
            'original_filename' => $firstFile ? $firstFile['original_filename'] : null,
            'mime_type' => $firstFile ? $firstFile['mime_type'] : null,
            'file_size' => $firstFile ? $firstFile['file_size'] : null,
            'status' => SubmissionStatus::Uploaded,
            'version' => $nextVersion,
            'is_latest' => true,
            'google_drive_links' => $driveLinks,
            'files' => $savedFiles,
        ]);
    }
}

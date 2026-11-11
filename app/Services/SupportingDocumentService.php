<?php

namespace App\Services;

use App\Models\Prodi;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\SupportingDocument;
use App\Models\User;
use App\Support\AccreditationUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SupportingDocumentService
{
    public function visibleQueryFor(User $user): Builder
    {
        if ($user->isPerti()) {
            $perti = $user->pertiProfile;
            abort_unless($perti, 403);

            return SupportingDocument::query()
                ->where('perti_id', $perti->id);
        }

        if ($user->isProdi()) {
            $prodi = $user->prodiProfile;

            return SupportingDocument::query()->where(function (Builder $query) use ($user, $prodi) {
                $query->where('uploaded_by', $user->id);

                if ($prodi) {
                    $query->orWhere('prodi_id', $prodi->id);

                    if ($prodi->perti_id) {
                        $query->orWhere(function (Builder $shared) use ($prodi) {
                            $shared->where('perti_id', $prodi->perti_id)
                                ->whereNull('prodi_id');
                        });
                    }
                }
            });
        }

        abort(403);
    }

    /**
     * @return list<string>
     */
    public function availableCategories(User $user): array
    {
        $existing = $this->visibleQueryFor($user)
            ->select('category')
            ->distinct()
            ->pluck('category')
            ->filter(fn ($cat) => is_string($cat) && trim($cat) !== '')
            ->values()
            ->all();

        return array_values(array_unique(array_merge(
            SupportingDocument::DEFAULT_CATEGORIES,
            $existing
        )));
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<int, UploadedFile>  $uploadedFiles
     */
    public function storeForUser(User $user, array $validated, array $uploadedFiles = []): SupportingDocument
    {
        $this->assertCanUploadFiles($user, $uploadedFiles);
        $driveLinks = $this->sanitizeDriveLinks($validated['google_drive_links'] ?? []);

        if (empty($uploadedFiles) && empty($driveLinks)) {
            throw ValidationException::withMessages([
                'documents' => 'Unggah minimal satu berkas atau tambahkan minimal satu tautan Google Drive.',
            ]);
        }

        [$pertiId, $prodiId] = $this->resolveOwnership($user, $validated['prodi_id'] ?? null);
        $category = $this->resolveCategory($validated);
        [$requirementId, $tableCode] = $this->resolveLinkage($validated);

        return DB::transaction(function () use ($user, $pertiId, $prodiId, $category, $validated, $requirementId, $tableCode, $uploadedFiles, $driveLinks) {
            $document = SupportingDocument::query()->create([
                'perti_id' => $pertiId,
                'prodi_id' => $prodiId,
                'uploaded_by' => $user->id,
                'category' => $category,
                'title' => trim((string) $validated['title']),
                'academic_year' => ! empty($validated['academic_year']) ? trim((string) $validated['academic_year']) : null,
                'description' => ! empty($validated['description']) ? trim((string) $validated['description']) : null,
                'requirement_id' => $requirementId,
                'table_code' => $tableCode,
                'files' => [],
                'google_drive_links' => $driveLinks,
            ]);

            $savedFiles = $this->storeUploadedFiles($document, $uploadedFiles);
            $document->update(['files' => $savedFiles]);

            return $document->fresh(['perti.user', 'prodi.user', 'uploader', 'requirement.module']);
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<int, UploadedFile>  $uploadedFiles
     * @param  array<int, string>  $keepFilePaths
     */
    public function updateForUser(
        SupportingDocument $document,
        User $user,
        array $validated,
        array $uploadedFiles = [],
        array $keepFilePaths = []
    ): SupportingDocument {
        abort_unless($document->canBeEditedBy($user), 403);
        $this->assertCanUploadFiles($user, $uploadedFiles);

        $driveLinks = $this->sanitizeDriveLinks($validated['google_drive_links'] ?? []);

        $existingFiles = is_array($document->files) ? $document->files : [];
        $keepLookup = array_flip(array_filter($keepFilePaths, fn ($p) => is_string($p) && $p !== ''));

        $keptFiles = [];
        $removedPaths = [];

        foreach ($existingFiles as $file) {
            $path = $file['file_path'] ?? '';
            if ($path !== '' && isset($keepLookup[$path])) {
                $keptFiles[] = $file;
            } elseif ($path !== '') {
                $removedPaths[] = $path;
            }
        }

        if (empty($keptFiles) && empty($uploadedFiles) && empty($driveLinks)) {
            throw ValidationException::withMessages([
                'documents' => 'Dokumen harus memiliki minimal satu berkas atau satu tautan Google Drive.',
            ]);
        }

        [$pertiId, $prodiId] = $user->isPerti()
            ? $this->resolveOwnership($user, $validated['prodi_id'] ?? null)
            : [$document->perti_id, $document->prodi_id];

        $category = $this->resolveCategory($validated);
        [$requirementId, $tableCode] = $this->resolveLinkage($validated);

        $updated = DB::transaction(function () use (
            $document,
            $pertiId,
            $prodiId,
            $category,
            $validated,
            $requirementId,
            $tableCode,
            $keptFiles,
            $uploadedFiles,
            $driveLinks
        ) {
            $newFiles = $this->storeUploadedFiles($document, $uploadedFiles);

            $document->update([
                'perti_id' => $pertiId,
                'prodi_id' => $prodiId,
                'category' => $category,
                'title' => trim((string) $validated['title']),
                'academic_year' => ! empty($validated['academic_year']) ? trim((string) $validated['academic_year']) : null,
                'description' => ! empty($validated['description']) ? trim((string) $validated['description']) : null,
                'requirement_id' => $requirementId,
                'table_code' => $tableCode,
                'files' => array_values(array_merge($keptFiles, $newFiles)),
                'google_drive_links' => $driveLinks,
            ]);

            return $document->fresh(['perti.user', 'prodi.user', 'uploader', 'requirement.module']);
        });

        foreach ($removedPaths as $path) {
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }

        return $updated;
    }

    public function deleteDocument(SupportingDocument $document, User $user): void
    {
        abort_unless($document->canBeEditedBy($user), 403);

        $files = is_array($document->files) ? $document->files : [];
        $paths = array_filter(array_map(fn ($f) => $f['file_path'] ?? null, $files));

        $document->delete();

        foreach ($paths as $path) {
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }
        }
    }

    /**
     * Format stored files for UI & search cards with direct preview/download links.
     *
     * @return list<array<string, mixed>>
     */
    public function formatFilesForRecord(SupportingDocument|Submission $record, string $routePrefix): array
    {
        $rawFiles = $this->extractStoredFiles($record);
        $isArchive = $record instanceof SupportingDocument;
        $viewRoute = $isArchive ? "{$routePrefix}.archives.view" : "{$routePrefix}.submissions.view";
        $downloadRoute = $isArchive ? "{$routePrefix}.archives.download" : "{$routePrefix}.submissions.download";

        $formatted = [];
        foreach ($rawFiles as $index => $file) {
            $filename = (string) ($file['original_filename'] ?? 'Dokumen');
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $sizeBytes = isset($file['file_size']) ? (int) $file['file_size'] : null;

            $formatted[] = [
                'index' => $index,
                'file_path' => $file['file_path'] ?? '',
                'filename' => $filename,
                'ext' => $ext ?: 'file',
                'size_bytes' => $sizeBytes,
                'size_label' => AccreditationUpload::formatFileSize($sizeBytes),
                'preview_kind' => AccreditationUpload::previewKind($filename),
                'view_url' => route($viewRoute, [$record, 'file' => $index]),
                'download_url' => route($downloadRoute, [$record, 'file' => $index]),
            ];
        }

        return $formatted;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function extractStoredFiles(SupportingDocument|Submission $record): array
    {
        $files = is_array($record->files) ? $record->files : [];

        if (empty($files) && $record instanceof Submission && ! empty($record->file_path)) {
            $files = [[
                'file_path' => $record->file_path,
                'original_filename' => $record->original_filename ?? basename($record->file_path),
                'mime_type' => $record->mime_type,
                'file_size' => $record->file_size,
            ]];
        }

        return array_values($files);
    }

    /**
     * @param  array<int, array<string, mixed>>  $rawLinks
     * @return list<array{name: string, url: string}>
     */
    public function sanitizeDriveLinks(array $rawLinks): array
    {
        $sanitized = [];
        $counter = 1;

        foreach ($rawLinks as $link) {
            if (! is_array($link)) {
                continue;
            }

            $url = trim((string) ($link['url'] ?? ''));
            if ($url === '') {
                continue;
            }

            if (! preg_match('#^https?://#i', $url)) {
                $url = 'https://'.$url;
            }

            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                throw ValidationException::withMessages([
                    'google_drive_links' => 'Tautan URL "'.$url.'" tidak valid. Pastikan format URL benar.',
                ]);
            }

            $name = trim((string) ($link['name'] ?? ''));
            $sanitized[] = [
                'name' => $name !== '' ? $name : 'Tautan Dokumen #'.$counter,
                'url' => $url,
            ];
            $counter++;
        }

        return $sanitized;
    }

    /**
     * @param  array<int, UploadedFile>  $uploadedFiles
     */
    private function assertCanUploadFiles(User $user, array $uploadedFiles): void
    {
        if (empty($uploadedFiles)) {
            return;
        }

        if ($user->effective_package === 'Starter') {
            throw ValidationException::withMessages([
                'documents' => 'Paket Starter tidak mendukung unggah file langsung. Gunakan tautan Google Drive atau upgrade paket.',
            ]);
        }

        foreach ($uploadedFiles as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            if (! $file->isValid()) {
                throw ValidationException::withMessages([
                    'documents' => AccreditationUpload::uploadErrorMessage($file),
                ]);
            }

            if ($error = AccreditationUpload::validateArchiveFile($file)) {
                throw ValidationException::withMessages([
                    'documents' => $error,
                ]);
            }
        }
    }

    /**
     * @param  array<int, UploadedFile>  $uploadedFiles
     * @return list<array<string, mixed>>
     */
    private function storeUploadedFiles(SupportingDocument $document, array $uploadedFiles): array
    {
        $saved = [];

        foreach ($uploadedFiles as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $path = $file->store("supporting-documents/{$document->id}", 'local');
            $saved[] = [
                'file_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ];
        }

        return $saved;
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private function resolveOwnership(User $user, mixed $requestedProdiId): array
    {
        if ($user->isPerti()) {
            $perti = $user->pertiProfile;
            abort_unless($perti, 403);

            $prodiId = null;
            if ($requestedProdiId !== null && $requestedProdiId !== '' && $requestedProdiId !== 'shared') {
                $prodi = Prodi::query()
                    ->where('id', (int) $requestedProdiId)
                    ->where('perti_id', $perti->id)
                    ->first();

                if (! $prodi) {
                    throw ValidationException::withMessages([
                        'prodi_id' => 'Program Studi yang dipilih tidak valid atau bukan bagian dari perguruan tinggi Anda.',
                    ]);
                }

                $prodiId = $prodi->id;
            }

            return [$perti->id, $prodiId];
        }

        $prodi = $user->prodiProfile;

        return [$prodi?->perti_id, $prodi?->id];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveCategory(array $validated): string
    {
        $category = trim((string) ($validated['category'] ?? ''));
        $custom = trim((string) ($validated['custom_category'] ?? ''));

        if ($category === '__custom__' || ($category === 'Dokumen Lainnya' && $custom !== '')) {
            return $custom !== '' ? $custom : 'Dokumen Lainnya';
        }

        if ($custom !== '' && $category === '') {
            return $custom;
        }

        return $category !== '' ? $category : 'Dokumen Lainnya';
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{0: int|null, 1: string|null}
     */
    private function resolveLinkage(array $validated): array
    {
        $requirementId = ! empty($validated['requirement_id']) ? (int) $validated['requirement_id'] : null;
        $tableCode = ! empty($validated['table_code']) ? trim((string) $validated['table_code']) : null;

        if ($requirementId) {
            $requirement = Requirement::query()->find($requirementId);
            if ($requirement && ! $tableCode && $requirement->isTable()) {
                $tableCode = $requirement->table_code;
            }
        }

        return [$requirementId, $tableCode];
    }
}

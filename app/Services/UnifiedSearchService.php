<?php

namespace App\Services;

use App\Enums\SubmissionStatus;
use App\Models\LkpsSubmission;
use App\Models\Module;
use App\Models\Prodi;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\SupportingDocument;
use App\Models\User;

class UnifiedSearchService
{
    public function __construct(
        protected SupportingDocumentService $supportingDocuments
    ) {}

    /**
     * Build search dataset for a Program Studi (Unit Kerja) user.
     *
     * @return array<string, mixed>
     */
    public function buildForProdi(User $user): array
    {
        $modules = Module::query()
            ->orderBy('sort_order')
            ->with([
                'requirements' => function ($q) use ($user) {
                    $q->orderBy('sort_order')
                        ->with([
                            'submissions' => fn ($s) => $s->where('user_id', $user->id)->where('is_latest', true),
                            'lkpsSubmissions' => fn ($ls) => $ls->where('user_id', $user->id),
                        ]);
                },
            ])
            ->get();

        $lkpsByTable = LkpsSubmission::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('table_code');

        $items = collect();

        foreach ($modules as $module) {
            foreach ($module->requirements as $req) {
                $tableCode = $req->table_code ?: 'TABLE';
                $sub = $req->submissions->first();
                $lkps = $req->lkpsSubmissions->first() ?? $lkpsByTable->get($tableCode);

                $items->push($this->mapRequirementItem('unit', $module, $req, $sub, $lkps, null));
            }
        }

        $archives = $this->supportingDocuments->visibleQueryFor($user)
            ->with(['perti.user', 'prodi.user', 'uploader', 'requirement.module'])
            ->latest()
            ->get();

        foreach ($archives as $doc) {
            $items->push($this->mapArchiveItem($doc, 'unit'));
        }

        return [
            'items' => $items->values(),
            'moduleOptions' => $this->mapModuleOptions($modules),
            'archiveCategories' => $this->supportingDocuments->availableCategories($user),
        ];
    }

    /**
     * Build search dataset for a Perguruan Tinggi (Perti) user.
     *
     * @return array<string, mixed>
     */
    public function buildForPerti(User $user, ?string $selectedProdiId): array
    {
        $pertiProfile = $user->pertiProfile;
        abort_if(is_null($pertiProfile), 403);

        $prodis = Prodi::query()
            ->where('perti_id', $pertiProfile->id)
            ->with('user')
            ->orderBy('id')
            ->get();

        $modules = Module::query()
            ->orderBy('sort_order')
            ->with(['requirements' => fn ($q) => $q->orderBy('sort_order')])
            ->get();

        if ($selectedProdiId !== 'all' && ! $prodis->contains('id', (int) $selectedProdiId)) {
            $selectedProdiId = $prodis->first()?->id ? (string) $prodis->first()->id : 'all';
        }

        $targetProdis = ($selectedProdiId === 'all')
            ? $prodis
            : $prodis->where('id', (int) $selectedProdiId);

        $targetUserIds = $targetProdis->pluck('user_id')->filter()->all();
        $targetProdiIds = $targetProdis->pluck('id')->filter()->all();

        $submissionsByReqAndUser = Submission::query()
            ->whereIn('user_id', $targetUserIds)
            ->where('is_latest', true)
            ->get()
            ->groupBy(fn ($s) => $s->requirement_id.'_'.$s->user_id);

        $lkpsByUserAndTable = LkpsSubmission::query()
            ->whereIn('user_id', $targetUserIds)
            ->get()
            ->groupBy(fn ($l) => $l->table_code.'_'.$l->user_id);

        $items = collect();

        foreach ($targetProdis as $prodi) {
            $pUserId = $prodi->user_id;

            foreach ($modules as $module) {
                foreach ($module->requirements as $req) {
                    $tableCode = $req->table_code ?: 'TABLE';
                    $sub = $submissionsByReqAndUser->get($req->id.'_'.$pUserId)?->first();
                    $lkps = $lkpsByUserAndTable->get($tableCode.'_'.$pUserId)?->first();

                    $items->push($this->mapRequirementItem('perti', $module, $req, $sub, $lkps, $prodi));
                }
            }
        }

        $archiveQuery = SupportingDocument::query()
            ->where('perti_id', $pertiProfile->id)
            ->with(['perti.user', 'prodi.user', 'uploader', 'requirement.module'])
            ->latest();

        if ($selectedProdiId !== 'all') {
            $archiveQuery->where(function ($q) use ($targetProdiIds) {
                $q->whereIn('prodi_id', $targetProdiIds)
                    ->orWhereNull('prodi_id');
            });
        }

        foreach ($archiveQuery->get() as $doc) {
            $items->push($this->mapArchiveItem($doc, 'perti'));
        }

        return [
            'items' => $items->values(),
            'prodis' => $prodis,
            'selectedProdiId' => $selectedProdiId,
            'moduleOptions' => $this->mapModuleOptions($modules),
            'archiveCategories' => $this->supportingDocuments->availableCategories($user),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapRequirementItem(
        string $routePrefix,
        Module $module,
        Requirement $req,
        ?Submission $sub,
        ?LkpsSubmission $lkps,
        ?Prodi $prodi
    ): array {
        $isTable = $req->isTable();
        $isPerti = $routePrefix === 'perti';
        $tableCode = $req->table_code ?: 'TABLE';
        $files = [];
        $driveLinks = [];
        $exportUrl = null;

        if ($isTable) {
            $hasData = $lkps && ! empty($lkps->table_rows);
            if ($hasData) {
                $rawStatus = $lkps->status;
                $statusKey = in_array($rawStatus, ['submitted', 'uploaded']) ? 'uploaded' : $rawStatus;
                $statusLabel = match ($rawStatus) {
                    'approved' => 'Sesuai',
                    'revision' => 'Perlu Revisi',
                    'submitted', 'uploaded' => 'Menunggu Validasi',
                    'draft' => 'Draft Tersimpan',
                    default => 'Menunggu Validasi',
                };
            } else {
                $statusKey = 'unsubmitted';
                $statusLabel = 'Belum Diisi';
            }

            $notes = $lkps?->validation_notes ?? '';
            $actionUrl = $isPerti && $prodi
                ? route('perti.prodis.lkps.viewer', [$prodi->id, $tableCode])
                : route('unit.lkps.viewer', $tableCode);
            $actionLabel = $isPerti ? 'Periksa & Validasi Tabel' : 'Buka & Isi Tabel LKPS';
            if ($hasData) {
                $exportUrl = $isPerti && $prodi
                    ? route('perti.prodis.lkps.export', [$prodi->id, $tableCode])
                    : route('unit.lkps.export', $tableCode);
            }
            $fileCount = 0;
            $rowCount = $hasData && is_array($lkps->table_rows) ? count($lkps->table_rows) : 0;
        } else {
            $hasData = $sub && $sub->status !== SubmissionStatus::Pending;
            if ($hasData) {
                $statusKey = $sub->status->value;
                $statusLabel = match ($sub->status) {
                    SubmissionStatus::Approved => 'Sesuai',
                    SubmissionStatus::Revision => 'Perlu Revisi',
                    SubmissionStatus::Uploaded => 'Menunggu Validasi',
                    default => 'Belum Diunggah',
                };
            } else {
                $statusKey = 'unsubmitted';
                $statusLabel = 'Belum Diunggah';
            }

            $notes = $sub?->validation_notes ?? '';
            $actionUrl = $isPerti && $prodi
                ? route('perti.prodis.modul', [$prodi->id, $module->id, 'tab' => 'led']).'#req-'.$req->id
                : route('unit.submissions.module', ['module' => $module->id, 'tab' => 'led']).'#req-'.$req->id;
            $actionLabel = $isPerti ? 'Periksa & Validasi Dokumen' : 'Buka di Modul Dokumen';

            if ($sub) {
                $files = $this->supportingDocuments->formatFilesForRecord($sub, $routePrefix);
                $driveLinks = is_array($sub->google_drive_links) ? array_values($sub->google_drive_links) : [];
            }

            $fileCount = count($files) + count($driveLinks);
            $rowCount = 0;
        }

        $fileNamesText = implode(' ', array_map(fn ($f) => $f['filename'] ?? '', $files));
        $linkNamesText = implode(' ', array_map(fn ($l) => ($l['name'] ?? '').' '.($l['url'] ?? ''), $driveLinks));

        $searchText = mb_strtolower(implode(' ', array_filter([
            $prodi?->name,
            $module->name,
            $module->shortLabel(),
            'kiteria '.$module->sort_order,
            $req->title,
            $req->description,
            $req->table_code,
            $isTable ? 'tabel kinerja lkps data matriks excel kuantitatif' : 'dokumen led evaluasi diri ppepp berkas',
            $statusLabel,
            $notes,
            $fileNamesText,
            $linkNamesText,
        ])));

        return [
            'unique_key' => 'req-'.$req->id.'-'.($prodi?->id ?? 'u'),
            'id' => $req->id,
            'prodi_id' => $prodi ? (string) $prodi->id : null,
            'prodi_name' => $prodi?->name,
            'module_id' => $module->id,
            'module_name' => $module->name,
            'module_short' => $module->shortLabel(),
            'module_order' => $module->sort_order,
            'category' => '',
            'academic_year' => '',
            'title' => $req->title,
            'description' => $req->description ?? '',
            'is_table' => $isTable,
            'is_archive' => false,
            'type_key' => $isTable ? 'lkps' : 'led',
            'type_badge' => $isTable ? 'Tabel LKPS' : 'Dokumen LED',
            'type_label' => $isTable ? 'Tabel Kinerja LKPS' : 'Dokumen LED (PPEPP)',
            'table_code' => $req->table_code ?? '',
            'status' => $statusKey,
            'status_label' => $statusLabel,
            'validation_notes' => $notes,
            'has_notes' => ! empty($notes),
            'has_data' => $hasData,
            'file_count' => $fileCount,
            'row_count' => $rowCount,
            'files' => $files,
            'drive_links' => $driveLinks,
            'export_url' => $exportUrl,
            'action_url' => $actionUrl,
            'action_label' => $actionLabel,
            'archive_index_url' => null,
            'search_text' => $searchText,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function mapArchiveItem(SupportingDocument $doc, string $routePrefix): array
    {
        $files = $this->supportingDocuments->formatFilesForRecord($doc, $routePrefix);
        $driveLinks = is_array($doc->google_drive_links) ? array_values($doc->google_drive_links) : [];
        $linkedModule = $doc->requirement?->module;

        $moduleOrder = $linkedModule?->sort_order;
        if ($moduleOrder === null && $doc->table_code && preg_match('/^(\d+)\./', $doc->table_code, $m)) {
            $moduleOrder = (int) $m[1];
        }

        $fileNamesText = implode(' ', array_map(fn ($f) => $f['filename'] ?? '', $files));
        $linkNamesText = implode(' ', array_map(fn ($l) => ($l['name'] ?? '').' '.($l['url'] ?? ''), $driveLinks));

        $searchText = mb_strtolower(implode(' ', array_filter([
            $doc->scopeLabel(),
            $doc->title,
            $doc->category,
            $doc->academic_year,
            $doc->description,
            $doc->table_code ? 'Tabel '.$doc->table_code : null,
            $doc->table_code,
            $linkedModule?->name,
            $linkedModule?->shortLabel(),
            $moduleOrder ? 'kriteria '.$moduleOrder.' kiteria '.$moduleOrder : null,
            'arsip dokumen tambahan pendukung mahasiswa nilai sk',
            $fileNamesText,
            $linkNamesText,
        ])));

        return [
            'unique_key' => 'arc-'.$doc->id,
            'id' => $doc->id,
            'prodi_id' => $doc->prodi_id ? (string) $doc->prodi_id : 'shared',
            'prodi_name' => $doc->scopeLabel(),
            'module_id' => $linkedModule?->id,
            'module_name' => $linkedModule?->name ?? $doc->scopeLabel(),
            'module_short' => $linkedModule?->shortLabel() ?? ($moduleOrder ? 'Kriteria '.$moduleOrder : 'Arsip Tambahan'),
            'module_order' => $moduleOrder,
            'category' => $doc->category,
            'academic_year' => $doc->academic_year ?? '',
            'title' => $doc->title,
            'description' => $doc->description ?? '',
            'is_table' => false,
            'is_archive' => true,
            'type_key' => 'archive',
            'type_badge' => 'Arsip Tambahan',
            'type_label' => 'Arsip & Dokumen Tambahan',
            'table_code' => $doc->table_code ?? '',
            'status' => 'archive',
            'status_label' => 'Tersedia di Arsip',
            'validation_notes' => '',
            'has_notes' => false,
            'has_data' => true,
            'file_count' => count($files) + count($driveLinks),
            'row_count' => 0,
            'files' => $files,
            'drive_links' => $driveLinks,
            'export_url' => null,
            'action_url' => $files[0]['view_url'] ?? route("{$routePrefix}.archives.view", $doc),
            'action_label' => 'Buka di Viewer',
            'archive_index_url' => route("{$routePrefix}.archives.index", ['q' => $doc->title]),
            'search_text' => $searchText,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Module>  $modules
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function mapModuleOptions($modules)
    {
        return $modules->map(fn ($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'short' => $m->shortLabel(),
        ]);
    }
}

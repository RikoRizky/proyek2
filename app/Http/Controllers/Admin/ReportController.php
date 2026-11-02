<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\LkpsSubmission;
use App\Models\Module;
use App\Models\Submission;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function pdf(Request $request)
    {
        $filters = $this->resolveTargetFilters($request);

        if (isset($filters['selectionRequired']) && $filters['selectionRequired']) {
            $pertis = User::query()
                ->where('role', UserRole::Perti)
                ->orderBy('name')
                ->get();
            return view('admin.reports.pdf-select', compact('pertis'));
        }

        $summaries = $this->summariesForExport($filters['onlyUnit'], $filters['pertiId']);

        $filename = 'laporan-ringkasan-akreditasi';
        if (!empty($filters['pertiName'])) {
            $filename .= '-' . \Illuminate\Support\Str::slug($filters['pertiName']);
        }
        $filename .= '.pdf';

        return Pdf::loadView('reports.accreditation-summary', [
            'summaries' => $summaries,
            'generatedAt' => now(),
            'pertiName' => $filters['pertiName'] ?? null,
        ])->setPaper('a4', 'portrait')->download($filename);
    }

    private function resolveTargetFilters(Request $request): array
    {
        $user = $request->user();

        if ($user->role === UserRole::Admin) {
            if ($request->filled('user_id')) {
                $unit = User::query()
                    ->where('id', $request->integer('user_id'))
                    ->where('role', UserRole::Prodi)
                    ->firstOrFail();
                return ['onlyUnit' => $unit, 'pertiId' => null, 'pertiName' => null];
            }

            if ($request->filled('perti_id')) {
                $perti = User::query()
                    ->where('id', $request->integer('perti_id'))
                    ->where('role', UserRole::Perti)
                    ->firstOrFail();
                $pertiProfile = $perti->pertiProfile;
                return ['onlyUnit' => null, 'pertiId' => $pertiProfile?->id, 'pertiName' => $perti->name];
            }

            return ['selectionRequired' => true];
        }

        if ($user->role === UserRole::Perti) {
            $pertiProfile = $user->pertiProfile;
            return ['onlyUnit' => null, 'pertiId' => $pertiProfile?->id, 'pertiName' => $user->name];
        }

        abort_unless($user->role === UserRole::Prodi, 403);

        return ['onlyUnit' => $user, 'pertiId' => null, 'pertiName' => null];
    }

    /**
     * @return list<array{user: User, modules: Collection, uploadedCount: int, totalRequirements: int, progressPercent: int}>
     */
    private function summariesForExport(?User $onlyUnit, ?int $pertiId = null): array
    {
        $units = User::query()
            ->where('role', UserRole::Prodi)
            ->when($onlyUnit, fn ($q) => $q->where('id', $onlyUnit->id))
            ->when($pertiId, function ($q) use ($pertiId) {
                // Filter berdasarkan perti_id di tabel prodis
                $prodiUserIds = \App\Models\Prodi::query()
                    ->where('perti_id', $pertiId)
                    ->pluck('user_id');
                $q->whereIn('id', $prodiUserIds);
            })
            ->orderBy('name')
            ->get();

        $modules = Module::query()->with('requirements')->orderBy('sort_order')->get();
        $totalRequirements = $modules->sum(fn (Module $m) => $m->requirements->count());

        return $units->map(function (User $unit) use ($modules, $totalRequirements) {
            $approvedTotalCount = 0;
            $revisionTotalCount = 0;
            $pendingValidationTotalCount = 0;

            $latestSubmissions = Submission::query()
                ->where('user_id', $unit->id)
                ->latestForUnit()
                ->get()
                ->keyBy('requirement_id');

            $lkpsSubmissions = LkpsSubmission::query()
                ->where('user_id', $unit->id)
                ->get();

            $lkpsByCode = $lkpsSubmissions->whereNotNull('table_code')->keyBy('table_code');
            $lkpsByReqId = $lkpsSubmissions->whereNotNull('requirement_id')->keyBy('requirement_id');

            $moduleRows = $modules->map(function (Module $module) use (
                $latestSubmissions,
                $lkpsByCode,
                $lkpsByReqId,
                &$approvedTotalCount,
                &$revisionTotalCount,
                &$pendingValidationTotalCount
            ) {
                $uploaded = 0;
                $approved = 0;
                $revision = 0;
                $pendingValidation = 0;
                $total = $module->requirements->count();

                $requirementDetails = [];

                foreach ($module->requirements as $requirement) {
                    $tableCode = $requirement->table_code;
                    $isTableReq = $requirement->isTable() || !empty($tableCode);

                    $lkps = $isTableReq
                        ? (($tableCode ? $lkpsByCode->get($tableCode) : null) ?? $lkpsByReqId->get($requirement->id))
                        : null;

                    $submission = $latestSubmissions->get($requirement->id);

                    $hasLkps = $lkps && !empty($lkps->table_rows);
                    $hasDoc = $submission && $submission->status !== SubmissionStatus::Pending;

                    $statusLabel = 'Belum Diunggah';
                    $statusKey = 'pending';
                    $validationNotes = null;
                    $validatedAt = null;
                    $detailInfo = null;

                    // Tentukan data utama berdasarkan tipe persyaratan
                    $primaryType = $isTableReq
                        ? ($hasLkps ? 'lkps' : ($hasDoc ? 'doc' : null))
                        : ($hasDoc ? 'doc' : ($hasLkps ? 'lkps' : null));

                    if ($primaryType === 'lkps') {
                        $uploaded++;
                        $rowCount = count($lkps->table_rows);
                        $detailInfo = "{$rowCount} baris data terisi";
                        $validationNotes = $lkps->validation_notes;
                        $validatedAt = $lkps->validated_at;

                        if ($lkps->status === 'approved') {
                            $approved++;
                            $approvedTotalCount++;
                            $statusLabel = 'Sesuai (Divalidasi)';
                            $statusKey = 'approved';
                        } elseif ($lkps->status === 'revision') {
                            $revision++;
                            $revisionTotalCount++;
                            $statusLabel = 'Perlu Revisi';
                            $statusKey = 'revision';
                        } else {
                            $pendingValidation++;
                            $pendingValidationTotalCount++;
                            $statusLabel = 'Menunggu Validasi';
                            $statusKey = 'uploaded';
                        }
                    } elseif ($primaryType === 'doc') {
                        $uploaded++;
                        $validationNotes = $submission->validation_notes;
                        $validatedAt = $submission->validated_at;

                        if ($submission->status === SubmissionStatus::Approved) {
                            $approved++;
                            $approvedTotalCount++;
                            $statusLabel = 'Sesuai (Divalidasi)';
                            $statusKey = 'approved';
                        } elseif ($submission->status === SubmissionStatus::Revision) {
                            $revision++;
                            $revisionTotalCount++;
                            $statusLabel = 'Perlu Revisi';
                            $statusKey = 'revision';
                        } else {
                            $pendingValidation++;
                            $pendingValidationTotalCount++;
                            $statusLabel = 'Menunggu Validasi';
                            $statusKey = 'uploaded';
                        }
                    }

                    $requirementDetails[] = [
                        'title'            => $requirement->title,
                        'status_key'       => $statusKey,
                        'status_label'     => $statusLabel,
                        'validation_notes' => $validationNotes,
                        'validated_at'     => $validatedAt,
                        'detail_info'      => $detailInfo,
                    ];
                }

                return [
                    'module'            => $module,
                    'uploaded'          => $uploaded,
                    'approved'          => $approved,
                    'revision'          => $revision,
                    'pendingValidation' => $pendingValidation,
                    'total'             => $total,
                    'progressPercent'   => $total > 0 ? round(($uploaded / $total) * 100) : 0,
                    'requirements'      => $requirementDetails,
                ];
            });

            $uploadedCount = (int) $moduleRows->sum('uploaded');

            return [
                'user'                   => $unit,
                'modules'                => $moduleRows,
                'uploadedCount'          => $uploadedCount,
                'approvedCount'          => $approvedTotalCount,
                'revisionCount'          => $revisionTotalCount,
                'pendingValidationCount' => $pendingValidationTotalCount,
                'totalRequirements'      => $totalRequirements,
                'progressPercent'        => $totalRequirements > 0 ? round(($uploadedCount / $totalRequirements) * 100) : 0,
            ];
        })->all();
    }
}


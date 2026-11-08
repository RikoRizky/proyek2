<?php

namespace App\Http\Controllers\UnitKerja;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\LkpsSubmission;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Tampilkan halaman khusus pencarian semua kriteria, dokumen LED, dan tabel LKPS untuk Unit Kerja (Prodi).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        abort_unless($user && $user->role === UserRole::Prodi, 403);

        $modules = Module::query()
            ->orderBy('sort_order')
            ->with([
                'requirements' => function ($q) use ($user) {
                    $q->orderBy('sort_order')
                        ->with([
                            'submissions' => function ($s) use ($user) {
                                $s->where('user_id', $user->id)
                                    ->where('is_latest', true);
                            },
                            'lkpsSubmissions' => function ($ls) use ($user) {
                                $ls->where('user_id', $user->id);
                            },
                        ]);
                },
            ])
            ->get();

        $items = collect();

        foreach ($modules as $module) {
            foreach ($module->requirements as $req) {
                $isTable = $req->isTable();
                $sub = $req->submissions->first();
                $tableCode = $req->table_code ?: 'TABLE';
                $lkps = $req->lkpsSubmissions->first()
                    ?? LkpsSubmission::where('user_id', $user->id)->where('table_code', $tableCode)->first();

                if ($isTable) {
                    $hasData = $lkps && !empty($lkps->table_rows);
                    if ($hasData) {
                        $rawStatus = $lkps->status; // approved, revision, submitted, uploaded, draft
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
                    $actionUrl = route('unit.lkps.show', $tableCode);
                    $actionLabel = 'Buka & Isi Tabel LKPS';
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
                    $actionUrl = route('unit.submissions.module', ['module' => $module->id, 'tab' => 'led']) . '#req-' . $req->id;
                    $actionLabel = 'Buka di Modul Dokumen';
                    $fileCount = ($sub ? count($sub->files ?? []) : 0) + ($sub ? count($sub->google_drive_links ?? []) : 0);
                    $rowCount = 0;
                }

                $searchText = mb_strtolower(implode(' ', array_filter([
                    $module->name,
                    $module->shortLabel(),
                    'kiteria ' . $module->sort_order, // handle typo 'kiteria'
                    $req->title,
                    $req->description,
                    $req->table_code,
                    $isTable ? 'tabel kinerja lkps data matriks excel kuantitatif' : 'dokumen led evaluasi diri ppepp berkas',
                    $statusLabel,
                    $notes,
                ])));

                $items->push([
                    'id'               => $req->id,
                    'module_id'        => $module->id,
                    'module_name'      => $module->name,
                    'module_short'     => $module->shortLabel(),
                    'module_order'     => $module->sort_order,
                    'title'            => $req->title,
                    'description'      => $req->description ?? '',
                    'is_table'         => $isTable,
                    'type_key'         => $isTable ? 'lkps' : 'led',
                    'type_badge'       => $isTable ? 'Tabel LKPS' : 'Dokumen LED',
                    'type_label'       => $isTable ? 'Tabel Kinerja LKPS' : 'Dokumen LED (PPEPP)',
                    'table_code'       => $req->table_code ?? '',
                    'status'           => $statusKey,
                    'status_label'     => $statusLabel,
                    'validation_notes' => $notes,
                    'has_notes'        => !empty($notes),
                    'has_data'         => $hasData,
                    'file_count'       => $fileCount,
                    'row_count'        => $rowCount,
                    'action_url'       => $actionUrl,
                    'action_label'     => $actionLabel,
                    'search_text'      => $searchText,
                ]);
            }
        }

        $moduleOptions = $modules->map(fn ($m) => [
            'id'    => $m->id,
            'name'  => $m->name,
            'short' => $m->shortLabel(),
        ]);

        return view('unit.search', [
            'items'         => $items,
            'moduleOptions' => $moduleOptions,
            'initialQuery'  => (string) $request->input('q', ''),
        ]);
    }
}

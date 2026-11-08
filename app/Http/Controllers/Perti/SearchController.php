<?php

namespace App\Http\Controllers\Perti;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\LkpsSubmission;
use App\Models\Module;
use App\Models\Prodi;
use App\Models\Requirement;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Tampilkan halaman khusus pencarian semua kriteria, dokumen LED, dan tabel LKPS untuk Perguruan Tinggi (Perti).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        abort_unless($user && $user->role === UserRole::Perti, 403);

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

        $selectedProdiId = $request->input('prodi_id');
        // Jika tidak diisi atau prodi_id tidak valid, default ke prodi pertama (atau 'all' jika dipilih)
        if ($selectedProdiId !== 'all' && !$prodis->contains('id', (int) $selectedProdiId)) {
            $selectedProdiId = $prodis->first()?->id ? (string) $prodis->first()->id : 'all';
        }

        // Tentukan daftar prodi yang akan dimuat datanya
        $targetProdis = ($selectedProdiId === 'all')
            ? $prodis
            : $prodis->where('id', (int) $selectedProdiId);

        $targetUserIds = $targetProdis->pluck('user_id')->filter()->all();

        // Eager load submissions & lkpsSubmissions untuk target users
        $submissionsByReqAndUser = Submission::query()
            ->whereIn('user_id', $targetUserIds)
            ->where('is_latest', true)
            ->get()
            ->groupBy(fn ($s) => $s->requirement_id . '_' . $s->user_id);

        $lkpsByUserAndTable = LkpsSubmission::query()
            ->whereIn('user_id', $targetUserIds)
            ->get()
            ->groupBy(fn ($l) => $l->table_code . '_' . $l->user_id);

        $items = collect();

        foreach ($targetProdis as $p) {
            $pUserId = $p->user_id;

            foreach ($modules as $module) {
                foreach ($module->requirements as $req) {
                    $isTable = $req->isTable();
                    $tableCode = $req->table_code ?: 'TABLE';

                    $sub = $submissionsByReqAndUser->get($req->id . '_' . $pUserId)?->first();
                    $lkps = $lkpsByUserAndTable->get($tableCode . '_' . $pUserId)?->first();

                    if ($isTable) {
                        $hasData = $lkps && !empty($lkps->table_rows);
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
                        $actionUrl = route('perti.prodis.lkps.viewer', [$p->id, $tableCode]);
                        $actionLabel = 'Periksa & Validasi Tabel';
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
                        $actionUrl = route('perti.prodis.modul', [$p->id, $module->id, 'tab' => 'led']) . '#req-' . $req->id;
                        $actionLabel = 'Periksa & Validasi Dokumen';
                        $fileCount = ($sub ? count($sub->files ?? []) : 0) + ($sub ? count($sub->google_drive_links ?? []) : 0);
                        $rowCount = 0;
                    }

                    $searchText = mb_strtolower(implode(' ', array_filter([
                        $p->name,
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
                        'prodi_id'         => $p->id,
                        'prodi_name'       => $p->name,
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
        }

        $moduleOptions = $modules->map(fn ($m) => [
            'id'    => $m->id,
            'name'  => $m->name,
            'short' => $m->shortLabel(),
        ]);

        return view('perti.search', [
            'items'           => $items,
            'prodis'          => $prodis,
            'selectedProdiId' => $selectedProdiId,
            'moduleOptions'   => $moduleOptions,
            'initialQuery'    => (string) $request->input('q', ''),
        ]);
    }
}

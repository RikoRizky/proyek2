<?php

namespace App\Http\Controllers\Perti;

use App\Http\Controllers\Controller;
use App\Models\LkpsSubmission;
use App\Models\Prodi;
use App\Services\LkpsExcelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LkpsReviewController extends Controller
{
    public function __construct(
        protected LkpsExcelService $excelService
    ) {}

    /**
     * Get LKPS table submission data for review.
     */
    public function show(Prodi $prodi, string $tableCode): JsonResponse
    {
        $this->authorizePerti($prodi);

        $submission = LkpsSubmission::where('user_id', $prodi->user_id)
            ->where('table_code', $tableCode)
            ->first();

        return response()->json([
            'success' => true,
            'table_code' => $tableCode,
            'submission' => $submission,
        ]);
    }

    /**
     * Dedicated Full Viewer for an LKPS table submission.
     */
    public function viewer(Prodi $prodi, string $tableCode): \Illuminate\View\View
    {
        $this->authorizePerti($prodi);

        $submission = LkpsSubmission::where('user_id', $prodi->user_id)
            ->where('table_code', $tableCode)
            ->first();

        $requirement = \App\Models\Requirement::where('table_code', $tableCode)->first();

        $backUrl = $requirement?->module_id
            ? route('perti.prodis.modul', [$prodi->id, $requirement->module_id, 'tab' => 'lkps'])
            : route('perti.prodis.index');

        return view('lkps.viewer', [
            'prodi'       => $prodi,
            'tableCode'   => $tableCode,
            'submission'  => $submission,
            'requirement' => $requirement,
            'backUrl'     => $backUrl,
            'routePrefix' => 'perti',
        ]);
    }

    /**
     * Validate an LKPS table submission (Approve / Revisi).
     */
    public function validateTable(Request $request, Prodi $prodi, string $tableCode): RedirectResponse|JsonResponse
    {
        $this->authorizePerti($prodi);

        $request->validate([
            'status' => ['required', 'in:approved,revision'],
            'validation_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $submission = LkpsSubmission::where('user_id', $prodi->user_id)
            ->where('table_code', $tableCode)
            ->firstOrFail();

        $submission->update([
            'status' => $request->input('status'),
            'validation_notes' => $request->input('validation_notes'),
            'validated_at' => now(),
            'validated_by' => auth()->id(),
        ]);

        $statusText = $submission->status === 'approved' ? 'disetujui' : 'diminta revisi';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Tabel {$tableCode} berhasil {$statusText}.",
                'submission' => $submission,
            ]);
        }

        return back()->with('status', "Tabel {$tableCode} berhasil {$statusText}.");
    }

    /**
     * Export prodi's LKPS table to Excel.
     */
    public function export(Prodi $prodi, string $tableCode): StreamedResponse|RedirectResponse
    {
        $this->authorizePerti($prodi);

        $submission = LkpsSubmission::where('user_id', $prodi->user_id)
            ->where('table_code', $tableCode)
            ->first();

        if (!$submission || empty($submission->table_rows)) {
            return back()->withErrors(['export' => "Belum ada data untuk Tabel {$tableCode} yang dapat diunduh."]);
        }

        $headers = $submission->dynamic_headers ?? [];
        if (empty($headers) && !empty($submission->table_rows)) {
            $headers = array_keys($submission->table_rows[0] ?? []);
        }

        return $this->excelService->exportToExcel(
            $tableCode,
            $submission->table_name ?? "Tabel {$tableCode}",
            $headers,
            $submission->table_rows,
            $submission->summary_data
        );
    }

    private function authorizePerti(Prodi $prodi): void
    {
        $perti = auth()->user()->pertiProfile;
        if (!$perti || $prodi->perti_id !== $perti->id) {
            abort(403, 'Anda tidak berhak mengakses data Program Studi ini.');
        }
    }
}


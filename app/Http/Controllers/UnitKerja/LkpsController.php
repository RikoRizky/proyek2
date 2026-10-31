<?php

namespace App\Http\Controllers\UnitKerja;

use App\Http\Controllers\Controller;
use App\Models\LkpsSubmission;
use App\Models\Requirement;
use App\Services\LkpsExcelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LkpsController extends Controller
{
    public function __construct(
        protected LkpsExcelService $excelService
    ) {}

    /**
     * Get LKPS table submission data for a table code.
     */
    public function show(string $tableCode): JsonResponse
    {
        $userId = auth()->id();
        $sub = LkpsSubmission::where('user_id', $userId)
            ->where('table_code', $tableCode)
            ->first();

        return response()->json([
            'success' => true,
            'table_code' => $tableCode,
            'submission' => $sub,
        ]);
    }

    /**
     * Dedicated Full Viewer for an LKPS table submission (Prodi view).
     */
    public function viewer(string $tableCode): \Illuminate\View\View
    {
        $userId = auth()->id();
        $submission = LkpsSubmission::where('user_id', $userId)
            ->where('table_code', $tableCode)
            ->first();

        $requirement = Requirement::where('table_code', $tableCode)->first();

        $backUrl = $requirement?->module_id
            ? route('unit.submissions.module', [$requirement->module_id, 'tab' => 'lkps'])
            : route('unit.submissions.index');

        return view('lkps.viewer', [
            'prodi'       => auth()->user()->prodiProfile,
            'tableCode'   => $tableCode,
            'submission'  => $submission,
            'requirement' => $requirement,
            'backUrl'     => $backUrl,
            'routePrefix' => 'unit',
        ]);
    }

    /**
     * Save web table data directly (inline/form editing).
     */
    public function save(Request $request, string $tableCode): JsonResponse|RedirectResponse
    {
        $userId = auth()->id();
        $rows = $request->input('rows', []);
        $dynamicHeaders = $request->input('dynamic_headers', []);
        $summary = $request->input('summary_data', []);
        $tableName = $request->input('table_name', "Tabel {$tableCode}");

        $requirement = Requirement::where('table_code', $tableCode)->first();

        $submission = LkpsSubmission::updateOrCreate(
            [
                'user_id' => $userId,
                'table_code' => $tableCode,
            ],
            [
                'requirement_id' => $requirement?->id,
                'table_name' => $tableName,
                'dynamic_headers' => $dynamicHeaders,
                'table_rows' => $rows,
                'summary_data' => $summary,
                'status' => 'submitted',
            ]
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data {$tableCode} berhasil disimpan.",
                'submission' => $submission,
            ]);
        }

        return back()->with('status', "Data {$tableCode} berhasil disimpan.");
    }

    /**
     * Import sheet from uploaded Excel file.
     */
    public function import(Request $request, string $tableCode): JsonResponse|RedirectResponse
    {
        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls,bin'],
        ], [
            'excel_file.required' => 'Pilih file Excel terlebih dahulu.',
            'excel_file.mimes' => 'Format berkas harus berupa Excel (.xlsx atau .xls).',
        ]);

        $file = $request->file('excel_file');

        try {
            $parsed = $this->excelService->parseSheet($file, $tableCode);
        } catch (\Throwable $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membaca sheet Excel: ' . $e->getMessage(),
                ], 422);
            }
            return back()->withErrors(['excel_file' => 'Gagal membaca sheet Excel: ' . $e->getMessage()]);
        }

        $userId = auth()->id();
        $requirement = Requirement::where('table_code', $tableCode)->first();

        $submission = LkpsSubmission::updateOrCreate(
            [
                'user_id' => $userId,
                'table_code' => $tableCode,
            ],
            [
                'requirement_id' => $requirement?->id,
                'table_name' => $requirement?->title ?? "Tabel {$tableCode}",
                'dynamic_headers' => $parsed['dynamic_headers'] ?? [],
                'table_rows' => $parsed['table_rows'] ?? [],
                'summary_data' => $parsed['summary_data'] ?? [],
                'status' => 'submitted',
            ]
        );

        $rowCount = count($parsed['table_rows'] ?? []);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengimpor {$rowCount} baris data untuk Tabel {$tableCode}.",
                'submission' => $submission,
            ]);
        }

        return back()->with('status', "Berhasil mengimpor {$rowCount} baris data untuk Tabel {$tableCode}.");
    }

    /**
     * Export LKPS table data to Excel.
     */
    public function export(string $tableCode): StreamedResponse|RedirectResponse
    {
        $userId = auth()->id();
        $submission = LkpsSubmission::where('user_id', $userId)
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
            $submission->table_rows
        );
    }
}


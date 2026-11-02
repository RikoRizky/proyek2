<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\LkpsSubmission;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\User;
use App\Services\LkpsExcelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class LkpsMergedTableTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Requirement $requirement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => UserRole::Prodi,
        ]);

        $module = Module::create([
            'name' => 'Kriteria 1: Budaya Mutu',
            'sort_order' => 1,
        ]);

        $this->requirement = Requirement::create([
            'module_id' => $module->id,
            'title' => 'Tabel 1.A.5: Kualifikasi Tenaga Kependidikan',
            'input_type' => 'table',
            'table_code' => '1.A.5',
            'sort_order' => 10,
        ]);
    }

    public function test_lkps_service_parses_table_1a5_with_merged_headers_and_totals(): void
    {
        $filePath = storage_path('app/LKPS_LAM_INFOKOM_Status_Unggul_S1Tr.xlsx');
        if (!file_exists($filePath)) {
            $this->markTestSkipped('Sample file not found in storage/app.');
        }

        $service = new LkpsExcelService();
        $file = new UploadedFile($filePath, 'LKPS_LAM_INFOKOM_Status_Unggul_S1Tr.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $parsed = $service->parseSheet($file, '1.A.5');

        $this->assertIsArray($parsed);
        $this->assertArrayHasKey('dynamic_headers', $parsed);
        $this->assertArrayHasKey('table_rows', $parsed);
        $this->assertArrayHasKey('summary_data', $parsed);

        // 11 leaf headers
        $this->assertCount(11, $parsed['dynamic_headers']);
        $this->assertContains('Jenis Tenaga Kependidikan', $parsed['dynamic_headers']);
        $this->assertContains('S3', $parsed['dynamic_headers']);
        $this->assertContains('SMA/SMK/MA', $parsed['dynamic_headers']);
        $this->assertContains('Unit Kerja', $parsed['dynamic_headers']);

        // 4 rows of data (Pustakawan, Laboran/Teknisi, Administrasi, Lainnya)
        $this->assertCount(4, $parsed['table_rows']);
        $this->assertStringContainsString('Pustakawan', (string)$parsed['table_rows'][0]['cells'][1]);

        // Multi-level header structure
        $this->assertArrayHasKey('header_structure', $parsed['summary_data']);
        $this->assertCount(2, $parsed['summary_data']['header_structure']);
        $row1 = $parsed['summary_data']['header_structure'][0];
        $mergedGroup = null;
        foreach ($row1 as $col) {
            if (isset($col['label']) && str_contains($col['label'], 'Jumlah Tenaga Kependidikan')) {
                $mergedGroup = $col;
                break;
            }
        }
        $this->assertNotNull($mergedGroup, 'Should find merged header column in row 1');
        $this->assertEquals(8, $mergedGroup['colspan']);

        // Check total configuration & note
        $this->assertTrue($parsed['summary_data']['has_total']);
        $this->assertNotEmpty($parsed['summary_data']['note']);
        $this->assertStringContainsString('Pustakawan', $parsed['summary_data']['note']);
    }

    public function test_prodi_can_save_table_with_merged_header_structure(): void
    {
        $headers = ['No', 'Jenis Tenaga Kependidikan', 'S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK/MA', 'Unit Kerja'];
        $rows = [
            [
                'cells' => ['1', 'Pustakawan *)', '0', '1', '2', '0', '0', '0', '0', '0', 'Perpustakaan'],
                'No' => '1',
                'Jenis Tenaga Kependidikan' => 'Pustakawan *)',
            ],
            [
                'cells' => ['2', 'Laboran/Teknisi', '0', '0', '3', '2', '1', '0', '0', '0', 'Lab Komputer'],
                'No' => '2',
                'Jenis Tenaga Kependidikan' => 'Laboran/Teknisi',
            ],
        ];
        $headerStructure = [
            [
                ['label' => 'No', 'rowspan' => 2, 'colspan' => 1],
                ['label' => 'Jenis Tenaga Kependidikan', 'rowspan' => 2, 'colspan' => 1],
                ['label' => 'Jumlah Tenaga Kependidikan dengan Pendidikan Terakhir', 'rowspan' => 1, 'colspan' => 8],
                ['label' => 'Unit Kerja', 'rowspan' => 2, 'colspan' => 1],
            ],
            [
                ['label' => 'S3'], ['label' => 'S2'], ['label' => 'S1'], ['label' => 'D4'],
                ['label' => 'D3'], ['label' => 'D2'], ['label' => 'D1'], ['label' => 'SMA/SMK/MA'],
            ]
        ];

        $response = $this->actingAs($this->user)->postJson(route('unit.lkps.save', '1.A.5'), [
            'table_code' => '1.A.5',
            'dynamic_headers' => $headers,
            'rows' => $rows,
            'header_structure' => $headerStructure,
            'has_total' => true,
            'total_label_col' => 1,
            'sum_cols' => [2, 3, 4, 5, 6, 7, 8, 9],
            'note' => 'Catatan staf kependidikan',
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $submission = LkpsSubmission::where('user_id', $this->user->id)
            ->where('table_code', '1.A.5')
            ->first();

        $this->assertNotNull($submission);
        $this->assertIsArray($submission->summary_data);
        $this->assertArrayHasKey('header_structure', $submission->summary_data);
        $this->assertEquals(8, $submission->summary_data['header_structure'][0][2]['colspan']);
        $this->assertTrue($submission->summary_data['has_total']);
        $this->assertEquals('Catatan staf kependidikan', $submission->summary_data['note']);
    }

    public function test_viewer_displays_merged_headers_and_total_row(): void
    {
        $headers = ['No', 'Jenis Tenaga Kependidikan', 'S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK/MA', 'Unit Kerja'];
        $rows = [
            [
                'cells' => ['1', 'Pustakawan *)', '0', '1', '2', '0', '0', '0', '0', '0', 'Perpustakaan'],
                'No' => '1',
                'Jenis Tenaga Kependidikan' => 'Pustakawan *)',
            ],
        ];
        $headerStructure = [
            [
                ['label' => 'No', 'rowspan' => 2, 'colspan' => 1],
                ['label' => 'Jenis Tenaga Kependidikan', 'rowspan' => 2, 'colspan' => 1],
                ['label' => 'Jumlah Tenaga Kependidikan dengan Pendidikan Terakhir', 'rowspan' => 1, 'colspan' => 8],
                ['label' => 'Unit Kerja', 'rowspan' => 2, 'colspan' => 1],
            ],
            [
                ['label' => 'S3'], ['label' => 'S2'], ['label' => 'S1'], ['label' => 'D4'],
                ['label' => 'D3'], ['label' => 'D2'], ['label' => 'D1'], ['label' => 'SMA/SMK/MA'],
            ]
        ];

        LkpsSubmission::create([
            'user_id' => $this->user->id,
            'table_code' => '1.A.5',
            'status' => 'submitted',
            'dynamic_headers' => $headers,
            'table_rows' => $rows,
            'summary_data' => [
                'header_structure' => $headerStructure,
                'has_total' => true,
                'total_label_col' => 1,
                'sum_cols' => [2, 3, 4, 5, 6, 7, 8, 9],
                'note' => 'Catatan verifikasi staf',
            ],
        ]);

        $response = $this->actingAs($this->user)->get(route('unit.lkps.viewer', '1.A.5'));

        $response->assertOk();
        $response->assertSee('Jumlah Tenaga Kependidikan dengan Pendidikan Terakhir');
        $response->assertSee('colspan="8"', false);
        $response->assertSee('Pustakawan *)');
        $response->assertSee('Total');
        $response->assertSee('Catatan verifikasi staf');
    }

    public function test_export_to_excel_includes_merged_cells_matching_template(): void
    {
        $headers = ['No', 'Jenis Tenaga Kependidikan', 'S3', 'S2', 'S1', 'D4', 'D3', 'D2', 'D1', 'SMA/SMK/MA', 'Unit Kerja'];
        $rows = [
            [
                'cells' => ['1', 'Pustakawan *)', '0', '1', '2', '0', '0', '0', '0', '0', 'Perpustakaan'],
            ],
            [
                'cells' => ['2', 'Laboran/Teknisi', '0', '0', '3', '2', '1', '0', '0', '0', 'Lab Komputer'],
            ],
        ];
        $headerStructure = [
            [
                ['label' => 'No', 'rowspan' => 2, 'colspan' => 1],
                ['label' => 'Jenis Tenaga Kependidikan', 'rowspan' => 2, 'colspan' => 1],
                ['label' => 'Jumlah Tenaga Kependidikan dengan Pendidikan Terakhir', 'rowspan' => 1, 'colspan' => 8],
                ['label' => 'Unit Kerja', 'rowspan' => 2, 'colspan' => 1],
            ],
            [
                ['label' => 'S3'], ['label' => 'S2'], ['label' => 'S1'], ['label' => 'D4'],
                ['label' => 'D3'], ['label' => 'D2'], ['label' => 'D1'], ['label' => 'SMA/SMK/MA'],
            ]
        ];

        LkpsSubmission::create([
            'user_id' => $this->user->id,
            'table_code' => '1.A.5',
            'status' => 'submitted',
            'dynamic_headers' => $headers,
            'table_rows' => $rows,
            'summary_data' => [
                'header_structure' => $headerStructure,
                'has_total' => true,
                'total_label_col' => 1,
                'sum_cols' => [2, 3, 4, 5, 6, 7, 8, 9],
                'note' => 'Catatan staf perpustakaan',
            ],
        ]);

        $response = $this->actingAs($this->user)->get(route('unit.lkps.export', '1.A.5'));

        $response->assertOk();
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $response->headers->get('content-type'));

        // Save content to temp file and inspect with PhpSpreadsheet
        $tempPath = tempnam(sys_get_temp_dir(), 'lkps_test_') . '.xlsx';
        file_put_contents($tempPath, $response->streamedContent());

        $spreadsheet = IOFactory::load($tempPath);
        $sheet = $spreadsheet->getActiveSheet();
        $mergedCells = $sheet->getMergeCells();

        // Must have merged cells (e.g. C2:J2 or C3:J3, A2:A3, etc.)
        $this->assertNotEmpty($mergedCells);

        // Find the C2:J2 or C3:J3 merge range
        $hasEducationMergedHeader = false;
        foreach (array_keys($mergedCells) as $range) {
            if (preg_match('/C\d+:J\d+/', $range)) {
                $hasEducationMergedHeader = true;
                break;
            }
        }
        $this->assertTrue($hasEducationMergedHeader, 'Expected C...:J... merge range for header in exported Excel');

        @unlink($tempPath);
    }

    public function test_prodi_can_save_and_export_table_with_multi_row_and_multi_column_merges(): void
    {
        $headers = ['No', 'Unit Kerja', 'S3', 'S2', 'S1'];
        $rows = [
            ['cells' => ['1', 'Fakultas Terapan', '1', '2', '3']],
            ['cells' => ['2', 'Fakultas Terapan', '0', '1', '4']],
            ['cells' => ['3', 'Fakultas Terapan', '2', '0', '1']],
            ['cells' => ['4', 'Fakultas Rekayasa', '1', '1', '2']],
        ];
        $headerStructure = [
            [
                ['label' => 'No', 'rowspan' => 2, 'colspan' => 1],
                ['label' => 'Unit Kerja', 'rowspan' => 2, 'colspan' => 1],
                ['label' => 'Pendidikan Terakhir', 'rowspan' => 1, 'colspan' => 3],
            ],
            [
                ['label' => 'S3'], ['label' => 'S2'], ['label' => 'S1'],
            ]
        ];
        $rowMerges = [
            [
                'colIdx' => 1,
                'colName' => 'Unit Kerja',
                'startRow' => 0,
                'endRow' => 2,
                'rowspan' => 3,
                'value' => 'Fakultas Terapan'
            ]
        ];

        // 1. Test Saving
        $response = $this->actingAs($this->user)->postJson(route('unit.lkps.save', '1.A.5'), [
            'table_code' => '1.A.5',
            'dynamic_headers' => $headers,
            'rows' => $rows,
            'header_structure' => $headerStructure,
            'row_merges' => $rowMerges,
            'has_total' => true,
            'total_label_col' => 1,
            'sum_cols' => [2, 3, 4],
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $submission = LkpsSubmission::where('user_id', $this->user->id)->where('table_code', '1.A.5')->first();
        $this->assertNotNull($submission);
        $this->assertArrayHasKey('row_merges', $submission->summary_data);
        $this->assertEquals(3, $submission->summary_data['row_merges'][0]['rowspan']);

        // 2. Test Export to Excel
        $exportResponse = $this->actingAs($this->user)->get(route('unit.lkps.export', '1.A.5'));
        $exportResponse->assertOk();

        $tempPath = tempnam(sys_get_temp_dir(), 'lkps_row_merge_') . '.xlsx';
        file_put_contents($tempPath, $exportResponse->streamedContent());

        $spreadsheet = IOFactory::load($tempPath);
        $sheet = $spreadsheet->getActiveSheet();
        $mergedCells = $sheet->getMergeCells();

        // Must find vertical row merge on column B (B4:B6)
        $hasRowMerge = false;
        foreach (array_keys($mergedCells) as $range) {
            if (preg_match('/B4:B6/', $range)) {
                $hasRowMerge = true;
                break;
            }
        }
        $this->assertTrue($hasRowMerge, 'Expected B4:B6 vertical row merge in exported Excel');

        @unlink($tempPath);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\LkpsSubmission;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportLkpsTest extends TestCase
{
    use RefreshDatabase;

    public function test_prodi_pdf_export_reflects_lkps_tables_and_document_submissions(): void
    {
        $prodi = User::factory()->create([
            'role' => UserRole::Prodi,
            'name' => 'Program Studi Informatika',
            'email' => 'informatika@gmail.com',
        ]);

        $module = Module::create([
            'name' => 'Kriteria 1: Budaya Mutu',
            'sort_order' => 1,
        ]);

        // 5 dokumen LED
        $reqDoc1 = Requirement::create(['module_id' => $module->id, 'title' => '1.1 [PENETAPAN]', 'input_type' => 'document', 'sort_order' => 1]);
        $reqDoc2 = Requirement::create(['module_id' => $module->id, 'title' => '1.2 [PELAKSANAAN]', 'input_type' => 'document', 'sort_order' => 2]);
        $reqDoc3 = Requirement::create(['module_id' => $module->id, 'title' => '1.3 [EVALUASI]', 'input_type' => 'document', 'sort_order' => 3]);
        $reqDoc4 = Requirement::create(['module_id' => $module->id, 'title' => '1.4 [PENGENDALIAN]', 'input_type' => 'document', 'sort_order' => 4]);
        $reqDoc5 = Requirement::create(['module_id' => $module->id, 'title' => '1.5 [PENINGKATAN]', 'input_type' => 'document', 'sort_order' => 5]);

        // 6 tabel LKPS
        $reqTable1 = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.A.1', 'input_type' => 'table', 'table_code' => '1.A.1', 'sort_order' => 6]);
        $reqTable2 = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.A.2', 'input_type' => 'table', 'table_code' => '1.A.2', 'sort_order' => 7]);
        $reqTable3 = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.A.3', 'input_type' => 'table', 'table_code' => '1.A.3', 'sort_order' => 8]);
        $reqTable4 = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.A.4', 'input_type' => 'table', 'table_code' => '1.A.4', 'sort_order' => 9]);
        $reqTable5 = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.A.5', 'input_type' => 'table', 'table_code' => '1.A.5', 'sort_order' => 10]);
        $reqTable6 = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.B', 'input_type' => 'table', 'table_code' => '1.B', 'sort_order' => 11]);

        // Dokumen 1.1 diunggah (1 dokumen terunggah)
        Submission::create([
            'requirement_id' => $reqDoc1->id,
            'user_id' => $prodi->id,
            'status' => SubmissionStatus::Uploaded,
            'is_latest' => true,
            'version' => 1,
            'file_path' => 'submissions/test.pdf',
            'original_filename' => 'test.pdf',
        ]);

        // Tabel 1.A.1 diisi 1 baris data (Menunggu validasi)
        LkpsSubmission::create([
            'user_id' => $prodi->id,
            'requirement_id' => $reqTable1->id,
            'table_code' => '1.A.1',
            'table_name' => 'Tabel 1.A.1: Pimpinan',
            'table_rows' => [['col1' => 'Pimpinan', 'col2' => 'Dr. Budi']],
            'status' => 'submitted',
        ]);

        // Tabel 1.A.2 diisi 3 baris data (Menunggu validasi)
        LkpsSubmission::create([
            'user_id' => $prodi->id,
            'requirement_id' => $reqTable2->id,
            'table_code' => '1.A.2',
            'table_name' => 'Tabel 1.A.2: Pendanaan',
            'table_rows' => [
                ['sumber' => 'SPP', 'dana' => 100],
                ['sumber' => 'Yayasan', 'dana' => 200],
                ['sumber' => 'Hibah', 'dana' => 50],
            ],
            'status' => 'submitted',
        ]);

        // Tabel 1.A.3 diisi 2 baris data dan disetujui (Approved)
        LkpsSubmission::create([
            'user_id' => $prodi->id,
            'requirement_id' => $reqTable3->id,
            'table_code' => '1.A.3',
            'table_name' => 'Tabel 1.A.3: Penggunaan Dana',
            'table_rows' => [
                ['pos' => 'Pendidikan', 'dana' => 80],
                ['pos' => 'Penelitian', 'dana' => 40],
            ],
            'status' => 'approved',
            'validated_at' => now(),
        ]);

        // Total terunggah seharusnya: 1 dokumen + 3 tabel = 4/11 (36%)
        // Approved: 1
        // Pending validation: 3 (1 dokumen + 2 tabel)

        $response = $this->actingAs($prodi)
            ->get(route('unit.reports.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=laporan-ringkasan-akreditasi.pdf');
    }

    public function test_summaries_calculation_includes_lkps_data_correctly(): void
    {
        $prodi = User::factory()->create([
            'role' => UserRole::Prodi,
            'name' => 'Program Studi Informatika',
        ]);

        $module = Module::create([
            'name' => 'Kriteria 1: Budaya Mutu',
            'sort_order' => 1,
        ]);

        $reqDoc = Requirement::create(['module_id' => $module->id, 'title' => '1.1 Dokumen', 'input_type' => 'document', 'sort_order' => 1]);
        $reqTable = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.A.1', 'input_type' => 'table', 'table_code' => '1.A.1', 'sort_order' => 2]);

        // Dokumen diunggah
        Submission::create([
            'requirement_id' => $reqDoc->id,
            'user_id' => $prodi->id,
            'status' => SubmissionStatus::Uploaded,
            'is_latest' => true,
            'version' => 1,
            'file_path' => 'submissions/test.pdf',
            'original_filename' => 'test.pdf',
        ]);

        // LKPS diisi 1 baris
        LkpsSubmission::create([
            'user_id' => $prodi->id,
            'requirement_id' => $reqTable->id,
            'table_code' => '1.A.1',
            'table_rows' => [['val' => 'abc']],
            'status' => 'submitted',
        ]);

        // Panggil controller lewat reflection atau request
        $controller = new \App\Http\Controllers\Admin\ReportController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('summariesForExport');
        $method->setAccessible(true);

        $summaries = $method->invoke($controller, $prodi, null);

        $this->assertCount(1, $summaries);
        $summary = $summaries[0];

        $this->assertEquals(2, $summary['uploadedCount']);
        $this->assertEquals(2, $summary['totalRequirements']);
        $this->assertEquals(100, $summary['progressPercent']);

        $moduleRow = $summary['modules'][0];
        $this->assertEquals(2, $moduleRow['uploaded']);
        $this->assertEquals(2, $moduleRow['total']);
        $this->assertEquals(2, $moduleRow['pendingValidation']);

        // Check requirement details
        $docDetail = $moduleRow['requirements'][0];
        $this->assertEquals('uploaded', $docDetail['status_key']);

        $tableDetail = $moduleRow['requirements'][1];
        $this->assertEquals('uploaded', $tableDetail['status_key']);
        $this->assertEquals('1 baris data terisi', $tableDetail['detail_info']);
    }

    public function test_lkps_revision_and_approved_statuses_with_notes(): void
    {
        $prodi = User::factory()->create([
            'role' => UserRole::Prodi,
            'name' => 'Program Studi Informatika',
        ]);

        $module = Module::create([
            'name' => 'Kriteria 1: Budaya Mutu',
            'sort_order' => 1,
        ]);

        $reqApproved = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.A.1', 'input_type' => 'table', 'table_code' => '1.A.1', 'sort_order' => 1]);
        $reqRevision = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.A.2', 'input_type' => 'table', 'table_code' => '1.A.2', 'sort_order' => 2]);
        $reqEmpty = Requirement::create(['module_id' => $module->id, 'title' => 'Tabel 1.A.3', 'input_type' => 'table', 'table_code' => '1.A.3', 'sort_order' => 3]);

        // Tabel 1 Approved
        LkpsSubmission::create([
            'user_id' => $prodi->id,
            'requirement_id' => $reqApproved->id,
            'table_code' => '1.A.1',
            'table_rows' => [['val' => 'abc']],
            'status' => 'approved',
            'validation_notes' => 'Data sudah lengkap dan valid.',
        ]);

        // Tabel 2 Revision
        LkpsSubmission::create([
            'user_id' => $prodi->id,
            'requirement_id' => $reqRevision->id,
            'table_code' => '1.A.2',
            'table_rows' => [['val' => 'xyz']],
            'status' => 'revision',
            'validation_notes' => 'Perbaiki kolom periode jabatan.',
        ]);

        // Tabel 3 Empty (Belum Diisi)
        // Tidak ada record LkpsSubmission atau kosong

        $controller = new \App\Http\Controllers\Admin\ReportController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('summariesForExport');
        $method->setAccessible(true);

        $summaries = $method->invoke($controller, $prodi, null);

        $summary = $summaries[0];
        $this->assertEquals(2, $summary['uploadedCount']);
        $this->assertEquals(1, $summary['approvedCount']);
        $this->assertEquals(1, $summary['revisionCount']);
        $this->assertEquals(0, $summary['pendingValidationCount']);
        $this->assertEquals(3, $summary['totalRequirements']);
        $this->assertEquals(67, $summary['progressPercent']);

        $reqs = $summary['modules'][0]['requirements'];

        // Req 1 (Approved)
        $this->assertEquals('approved', $reqs[0]['status_key']);
        $this->assertEquals('Sesuai (Divalidasi)', $reqs[0]['status_label']);
        $this->assertEquals('Data sudah lengkap dan valid.', $reqs[0]['validation_notes']);

        // Req 2 (Revision)
        $this->assertEquals('revision', $reqs[1]['status_key']);
        $this->assertEquals('Perlu Revisi', $reqs[1]['status_label']);
        $this->assertEquals('Perbaiki kolom periode jabatan.', $reqs[1]['validation_notes']);

        // Req 3 (Belum Diunggah)
        $this->assertEquals('pending', $reqs[2]['status_key']);
        $this->assertEquals('Belum Diunggah', $reqs[2]['status_label']);
        $this->assertNull($reqs[2]['detail_info']);
    }
}

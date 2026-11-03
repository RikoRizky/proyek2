<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\LkpsSubmission;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LkpsViewerEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_prodi_can_access_full_size_lkps_viewer(): void
    {
        $prodi = User::factory()->create([
            'role' => UserRole::Prodi,
        ]);

        $module = Module::create([
            'name' => 'Kriteria 1: Budaya Mutu',
            'sort_order' => 1,
        ]);

        $requirement = Requirement::create([
            'module_id' => $module->id,
            'title' => 'Tabel 1.A.4: Beban DTPR',
            'input_type' => 'table',
            'table_code' => '1.A.4',
            'sort_order' => 9,
        ]);

        $response = $this->actingAs($prodi)
            ->get(route('unit.lkps.viewer', '1.A.4'));

        $response->assertOk();
        $response->assertViewIs('lkps.viewer');
        $response->assertViewHas('tableCode', '1.A.4');
        $response->assertSee('Tabel 1.A.4');
        $response->assertSee('Mode Edit');
    }

    public function test_prodi_can_access_full_size_lkps_edit_mode(): void
    {
        $prodi = User::factory()->create([
            'role' => UserRole::Prodi,
        ]);

        $module = Module::create([
            'name' => 'Kriteria 1: Budaya Mutu',
            'sort_order' => 1,
        ]);

        Requirement::create([
            'module_id' => $module->id,
            'title' => 'Tabel 1.A.4: Beban DTPR',
            'input_type' => 'table',
            'table_code' => '1.A.4',
            'sort_order' => 9,
        ]);

        $response = $this->actingAs($prodi)
            ->get(route('unit.lkps.edit', '1.A.4'));

        $response->assertOk();
        $response->assertViewIs('lkps.viewer');
        $response->assertSee('Simpan & Ajukan Data Tabel');
        $response->assertSee('Format Standar');
        $response->assertSee('Gabung Kolom');
        $response->assertSee('+ Baris Total');
        $response->assertSee('function lkpsTableEditor', false);
        $response->assertSee('TABLE_SCHEMAS', false);
    }

    public function test_prodi_can_save_table_data_from_editor(): void
    {
        $prodi = User::factory()->create([
            'role' => UserRole::Prodi,
        ]);

        $module = Module::create([
            'name' => 'Kriteria 1: Budaya Mutu',
            'sort_order' => 1,
        ]);

        $requirement = Requirement::create([
            'module_id' => $module->id,
            'title' => 'Tabel 1.A.4: Beban DTPR',
            'input_type' => 'table',
            'table_code' => '1.A.4',
            'sort_order' => 9,
        ]);

        $rows = [
            [
                'cells' => ['Dr. Andi', '6', '2', '0', '4', '2', '0', '0', '14'],
                'Nama DTPR' => 'Dr. Andi',
                'Total SKS' => '14',
            ],
            [
                'cells' => ['Dr. Budi', '8', '0', '0', '3', '3', '2', '0', '16'],
                'Nama DTPR' => 'Dr. Budi',
                'Total SKS' => '16',
            ]
        ];

        $headers = [
            'Nama DTPR',
            'SKS Pengajaran PS Sendiri',
            'SKS PS Lain PT Sendiri',
            'SKS PT Lain',
            'SKS Penelitian',
            'SKS PkM',
            'SKS Manajemen PT Sendiri',
            'SKS Manajemen PT Lain',
            'Total SKS'
        ];

        $response = $this->actingAs($prodi)
            ->postJson(route('unit.lkps.save', '1.A.4'), [
                'table_code' => '1.A.4',
                'dynamic_headers' => $headers,
                'rows' => $rows,
                'summary_data' => ['total_dtpr' => 2],
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('lkps_submissions', [
            'user_id' => $prodi->id,
            'table_code' => '1.A.4',
            'status' => 'submitted',
        ]);

        $sub = LkpsSubmission::where('user_id', $prodi->id)->where('table_code', '1.A.4')->first();
        $this->assertCount(2, $sub->table_rows);
        $this->assertEquals(9, count($sub->dynamic_headers));
    }

    public function test_viewer_has_excel_paste_and_navigation_features(): void
    {
        $prodi = User::factory()->create([
            'role' => UserRole::Prodi,
        ]);

        $module = Module::create([
            'name' => 'Kriteria 1: Budaya Mutu',
            'sort_order' => 1,
        ]);

        Requirement::create([
            'module_id' => $module->id,
            'title' => 'Tabel 1.A.5: Tenaga Kependidikan',
            'input_type' => 'table',
            'table_code' => '1.A.5',
            'sort_order' => 10,
        ]);

        $response = $this->actingAs($prodi)
            ->get(route('unit.lkps.edit', '1.A.5'));

        $response->assertOk();
        $response->assertSee('handleExcelPaste', false);
        $response->assertSee('handleKeyNavigation', false);
        $response->assertSee('rebuildHeaderStructure', false);
        $response->assertSee('columnGroups', false);
        $response->assertSee('applyMultiColumnMerge', false);
        $response->assertSee('applyMultiRowMerge', false);
    }
}

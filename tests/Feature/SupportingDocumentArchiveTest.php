<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Module;
use App\Models\Perti;
use App\Models\Prodi;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\SupportingDocument;
use App\Models\User;
use App\Support\UploadProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SupportingDocumentArchiveTest extends TestCase
{
    use RefreshDatabase;

    private function createPertiAndProdi(string $suffix = '1'): array
    {
        $pertiUser = User::factory()->create([
            'name' => "Universitas Uji {$suffix}",
            'role' => UserRole::Perti,
        ]);
        $perti = $pertiUser->pertiProfile()->firstOrFail();

        $prodiUser = User::factory()->create([
            'name' => "Prodi Teknik Informatika {$suffix}",
            'role' => UserRole::Prodi,
        ]);
        $prodi = $prodiUser->prodiProfile()->firstOrFail();
        $prodi->update(['perti_id' => $perti->id]);

        return [$pertiUser, $perti->fresh(), $prodiUser, $prodi->fresh()];
    }

    public function test_prodi_can_store_view_inline_download_update_and_delete_supporting_documents(): void
    {
        Storage::fake('local');
        [, $perti, $prodiUser, $prodi] = $this->createPertiAndProdi('1');

        $response = $this->actingAs($prodiUser)->post(route('unit.archives.store'), [
            'title' => 'Kumpulan Data Mahasiswa Aktif 2025',
            'category' => '__custom__',
            'custom_category' => 'Data Mahasiswa Khusus',
            'academic_year' => 'TS',
            'description' => 'Rekapitulasi mahasiswa aktif semester ganjil dan transkrip.',
            'table_code' => '2.A.1',
            'documents' => [
                UploadedFile::fake()->create('data_mahasiswa.xlsx', 128, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
                UploadedFile::fake()->create('bagan_organisasi.png', 64, 'image/png'),
                UploadedFile::fake()->create('daftar_nilai.docx', 90, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
            ],
            'google_drive_links' => [
                ['name' => 'Folder Backup Drive', 'url' => 'drive.google.com/drive/folders/abc'],
            ],
        ]);

        $response->assertRedirect(route('unit.archives.index'));
        $response->assertSessionHasNoErrors();

        $doc = SupportingDocument::query()->first();
        $this->assertNotNull($doc);
        $this->assertSame($perti->id, $doc->perti_id);
        $this->assertSame($prodi->id, $doc->prodi_id);
        $this->assertSame('Data Mahasiswa Khusus', $doc->category);
        $this->assertSame('TS', $doc->academic_year);
        $this->assertSame('2.A.1', $doc->table_code);
        $this->assertCount(3, $doc->files);
        $this->assertSame('https://drive.google.com/drive/folders/abc', $doc->google_drive_links[0]['url']);

        foreach ($doc->files as $fileMeta) {
            Storage::disk('local')->assertExists($fileMeta['file_path']);
        }

        $this->actingAs($prodiUser)->get(route('unit.archives.index'))
            ->assertOk()
            ->assertSee('Kumpulan Data Mahasiswa Aktif 2025')
            ->assertSee('Data Mahasiswa Khusus');

        $this->actingAs($prodiUser)->get(route('unit.archives.view', [$doc, 'file' => 1]))
            ->assertOk()
            ->assertSee('bagan_organisasi.png')
            ->assertSee('archive-viewer-split')
            ->assertSee('archive-image-preview');

        $this->actingAs($prodiUser)->get(route('unit.archives.inline', [$doc, 'file' => 0]))
            ->assertOk();

        $this->actingAs($prodiUser)->get(route('unit.archives.inline', [$doc, 'file' => 1]))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/png');

        $this->actingAs($prodiUser)->get(route('unit.archives.download', [$doc, 'file' => 2]))
            ->assertOk()
            ->assertDownload('daftar_nilai.docx');

        // Keep only file index 0 and add 1 new CSV
        $keptPath = $doc->files[0]['file_path'];
        $removedPath1 = $doc->files[1]['file_path'];
        $removedPath2 = $doc->files[2]['file_path'];

        $updateResponse = $this->actingAs($prodiUser)->put(route('unit.archives.update', $doc), [
            'title' => 'Kumpulan Data Mahasiswa & Nilai Revisi',
            'category' => 'Data Nilai & CPL',
            'academic_year' => 'TS-1',
            'keep_files' => [$keptPath],
            'documents' => [
                UploadedFile::fake()->create('nilai_cpl.csv', 40, 'text/csv'),
            ],
            'google_drive_links' => [],
        ]);

        $updateResponse->assertRedirect(route('unit.archives.index'));
        $updateResponse->assertSessionHasNoErrors();

        $doc->refresh();
        $this->assertSame('Kumpulan Data Mahasiswa & Nilai Revisi', $doc->title);
        $this->assertSame('Data Nilai & CPL', $doc->category);
        $this->assertCount(2, $doc->files);
        Storage::disk('local')->assertExists($keptPath);
        Storage::disk('local')->assertMissing($removedPath1);
        Storage::disk('local')->assertMissing($removedPath2);
        Storage::disk('local')->assertExists($doc->files[1]['file_path']);

        $newCsvPath = $doc->files[1]['file_path'];

        $this->actingAs($prodiUser)->delete(route('unit.archives.destroy', $doc))
            ->assertRedirect(route('unit.archives.index'));

        $this->assertDatabaseMissing('supporting_documents', ['id' => $doc->id]);
        Storage::disk('local')->assertMissing($keptPath);
        Storage::disk('local')->assertMissing($newCsvPath);
    }

    public function test_perti_and_prodi_authorization_and_shared_documents(): void
    {
        Storage::fake('local');
        [$pertiUser1, $perti1, $prodiUser1, $prodi1] = $this->createPertiAndProdi('1');
        [$pertiUser2, , $prodiUser2] = $this->createPertiAndProdi('2');

        // Perti 1 uploads PT/UPPS-wide shared document
        $this->actingAs($pertiUser1)->post(route('perti.archives.store'), [
            'title' => 'SK Pendirian Perguruan Tinggi',
            'category' => 'SK & Legalitas Identitas Pengusul',
            'academic_year' => 'Umum',
            'prodi_id' => 'shared',
            'documents' => [
                UploadedFile::fake()->create('sk_pendirian_pt.pdf', 120, 'application/pdf'),
            ],
        ])->assertRedirect(route('perti.archives.index'));

        $sharedDoc = SupportingDocument::query()->firstOrFail();
        $this->assertNull($sharedDoc->prodi_id);

        // Prodi under Perti 1 can view & download shared doc, but cannot edit or delete it
        $this->actingAs($prodiUser1)->get(route('unit.archives.index'))
            ->assertOk()
            ->assertSee('SK Pendirian Perguruan Tinggi');
        $this->actingAs($prodiUser1)->get(route('unit.archives.view', $sharedDoc))
            ->assertOk();
        $this->actingAs($prodiUser1)->get(route('unit.archives.download', $sharedDoc))
            ->assertOk();

        $this->actingAs($prodiUser1)->put(route('unit.archives.update', $sharedDoc), [
            'title' => 'Attempt Hijack',
            'category' => 'Dokumen Lainnya',
            'keep_files' => [$sharedDoc->files[0]['file_path']],
        ])->assertForbidden();

        $this->actingAs($prodiUser1)->delete(route('unit.archives.destroy', $sharedDoc))
            ->assertForbidden();

        // Another Perti or another Prodi outside Perti 1 cannot view or download
        $this->actingAs($pertiUser2)->get(route('perti.archives.view', $sharedDoc))->assertForbidden();
        $this->actingAs($pertiUser2)->get(route('perti.archives.download', $sharedDoc))->assertForbidden();
        $this->actingAs($prodiUser2)->get(route('unit.archives.view', $sharedDoc))->assertForbidden();

        // Prodi 1 uploads its own doc; Perti 1 can view/download/edit it
        $this->actingAs($prodiUser1)->post(route('unit.archives.store'), [
            'title' => 'Data Nilai Semester Prodi 1',
            'category' => 'Data Nilai & CPL',
            'documents' => [
                UploadedFile::fake()->create('nilai_prodi1.pdf', 100, 'application/pdf'),
            ],
        ])->assertRedirect(route('unit.archives.index'));

        $prodiDoc = SupportingDocument::query()->where('prodi_id', $prodi1->id)->firstOrFail();
        $this->actingAs($pertiUser1)->get(route('perti.archives.view', $prodiDoc))
            ->assertOk()
            ->assertSee('archive-pdf-frame')
            ->assertSee(route('perti.archives.inline', [$prodiDoc, 'file' => 0]), false);
        $this->actingAs($pertiUser1)->get(route('perti.archives.inline', [$prodiDoc, 'file' => 0]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
        $this->actingAs($pertiUser1)->get(route('perti.archives.download', $prodiDoc))->assertOk();

        // Perti 1 can update & delete its own shared document
        $this->actingAs($pertiUser1)->put(route('perti.archives.update', $sharedDoc), [
            'title' => 'SK Pendirian PT Diperbarui',
            'category' => 'SK & Legalitas Identitas Pengusul',
            'academic_year' => 'Umum',
            'prodi_id' => 'shared',
            'keep_files' => [$sharedDoc->files[0]['file_path']],
        ])->assertRedirect(route('perti.archives.index'));

        $this->assertDatabaseHas('supporting_documents', [
            'id' => $sharedDoc->id,
            'title' => 'SK Pendirian PT Diperbarui',
        ]);

        $this->actingAs($pertiUser1)->delete(route('perti.archives.destroy', $sharedDoc))
            ->assertRedirect(route('perti.archives.index'));

        $this->assertDatabaseMissing('supporting_documents', ['id' => $sharedDoc->id]);
    }

    public function test_supporting_documents_do_not_distort_mandatory_upload_progress(): void
    {
        Storage::fake('local');
        [, , $prodiUser] = $this->createPertiAndProdi('1');

        $module = Module::query()->create(['name' => 'Kriteria 1: Budaya Mutu', 'sort_order' => 1]);
        Requirement::query()->create(['module_id' => $module->id, 'title' => 'LED 1.1', 'sort_order' => 1, 'input_type' => 'document']);
        Requirement::query()->create(['module_id' => $module->id, 'title' => 'Tabel 1.A.1', 'sort_order' => 2, 'input_type' => 'table', 'table_code' => '1.A.1']);

        $this->actingAs($prodiUser)->post(route('unit.archives.store'), [
            'title' => 'Data Mahasiswa Tambahan',
            'category' => 'Dokumen Mahasiswa',
            'documents' => [
                UploadedFile::fake()->create('mhs.xlsx', 50, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            ],
        ])->assertRedirect(route('unit.archives.index'));

        $progress = UploadProgress::forUnit($prodiUser);
        $this->assertSame(2, $progress['total']);
        $this->assertSame(0, $progress['uploaded']);
        $this->assertSame(0, $progress['percent']);
    }

    public function test_starter_package_blocks_archive_file_upload_but_allows_drive_links(): void
    {
        Storage::fake('local');
        [$pertiUser, , $prodiUser] = $this->createPertiAndProdi('1');
        $pertiUser->update(['active_package' => 'Starter']);

        $this->actingAs($prodiUser)->post(route('unit.archives.store'), [
            'title' => 'Data Mahasiswa Starter File',
            'category' => 'Dokumen Mahasiswa',
            'documents' => [
                UploadedFile::fake()->create('mhs.pdf', 50, 'application/pdf'),
            ],
        ])->assertSessionHasErrors('documents');

        $this->assertDatabaseCount('supporting_documents', 0);

        $this->actingAs($prodiUser)->post(route('unit.archives.store'), [
            'title' => 'Data Mahasiswa Starter Drive',
            'category' => 'Dokumen Mahasiswa',
            'google_drive_links' => [
                ['name' => 'Drive Mahasiswa', 'url' => 'https://drive.google.com/file/d/123/view'],
            ],
        ])->assertRedirect(route('unit.archives.index'));

        $this->assertDatabaseCount('supporting_documents', 1);
    }

    public function test_unified_search_for_prodi_and_perti_indexes_archives_and_submission_filenames(): void
    {
        Storage::fake('local');
        [$pertiUser, , $prodiUser, $prodi] = $this->createPertiAndProdi('1');

        $module = Module::query()->create(['name' => 'Kriteria 2: Relevansi Pendidikan', 'sort_order' => 2]);
        $ledReq = Requirement::query()->create([
            'module_id' => $module->id,
            'title' => '[PENETAPAN] Kurikulum & Pembelajaran',
            'sort_order' => 1,
            'input_type' => 'document',
        ]);
        Requirement::query()->create([
            'module_id' => $module->id,
            'title' => 'Mahasiswa Aktif',
            'sort_order' => 2,
            'input_type' => 'table',
            'table_code' => '2.A.1',
        ]);

        $ledPath = UploadedFile::fake()->create('SK_Kurikulum_Prodi.pdf', 100, 'application/pdf')
            ->store("accreditation/{$prodiUser->id}/{$ledReq->id}", 'local');

        $submission = Submission::query()->create([
            'requirement_id' => $ledReq->id,
            'user_id' => $prodiUser->id,
            'file_path' => $ledPath,
            'original_filename' => 'SK_Kurikulum_Prodi.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 102400,
            'status' => SubmissionStatus::Uploaded,
            'version' => 1,
            'is_latest' => true,
            'files' => [[
                'file_path' => $ledPath,
                'original_filename' => 'SK_Kurikulum_Prodi.pdf',
                'mime_type' => 'application/pdf',
                'file_size' => 102400,
            ]],
        ]);

        $this->actingAs($prodiUser)->post(route('unit.archives.store'), [
            'title' => 'Kumpulan Data Mahasiswa & Nilai',
            'category' => 'Dokumen Mahasiswa',
            'academic_year' => 'TS',
            'documents' => [
                UploadedFile::fake()->create('rekap_mahasiswa_ts.xlsx', 120, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
                UploadedFile::fake()->create('rekap_nilai_cpl.csv', 45, 'text/csv'),
            ],
        ])->assertRedirect(route('unit.archives.index'));

        $archive = SupportingDocument::query()->firstOrFail();

        $prodiSearchResponse = $this->actingAs($prodiUser)->get(route('unit.search'))
            ->assertOk();

        $prodiItems = $prodiSearchResponse->viewData('items');
        $prodiArchiveItem = $prodiItems->firstWhere('unique_key', 'arc-'.$archive->id);
        $prodiLedItem = $prodiItems->firstWhere('unique_key', 'req-'.$ledReq->id.'-u');

        $this->assertNotNull($prodiArchiveItem);
        $this->assertCount(2, $prodiArchiveItem['files']);
        $this->assertSame('rekap_mahasiswa_ts.xlsx', $prodiArchiveItem['files'][0]['filename']);
        $this->assertSame('rekap_nilai_cpl.csv', $prodiArchiveItem['files'][1]['filename']);
        $this->assertSame(route('unit.archives.view', [$archive, 'file' => 0]), $prodiArchiveItem['files'][0]['view_url']);
        $this->assertSame(route('unit.archives.download', [$archive, 'file' => 0]), $prodiArchiveItem['files'][0]['download_url']);

        $this->assertNotNull($prodiLedItem);
        $this->assertStringContainsString('sk_kurikulum_prodi.pdf', $prodiLedItem['search_text']);
        $this->assertSame(route('unit.submissions.view', [$submission, 'file' => 0]), $prodiLedItem['files'][0]['view_url']);
        $this->assertSame(route('unit.submissions.download', [$submission, 'file' => 0]), $prodiLedItem['files'][0]['download_url']);

        $pertiSearchResponse = $this->actingAs($pertiUser)->get(route('perti.search', ['prodi_id' => $prodi->id]))
            ->assertOk();

        $pertiItems = $pertiSearchResponse->viewData('items');
        $pertiArchiveItem = $pertiItems->firstWhere('unique_key', 'arc-'.$archive->id);
        $pertiLedItem = $pertiItems->firstWhere('unique_key', 'req-'.$ledReq->id.'-'.$prodi->id);

        $this->assertNotNull($pertiArchiveItem);
        $this->assertSame(route('perti.archives.view', [$archive, 'file' => 0]), $pertiArchiveItem['files'][0]['view_url']);
        $this->assertSame(route('perti.archives.download', [$archive, 'file' => 0]), $pertiArchiveItem['files'][0]['download_url']);
        $this->assertNotNull($pertiLedItem);
        $this->assertSame('SK_Kurikulum_Prodi.pdf', $pertiLedItem['files'][0]['filename']);
    }
}

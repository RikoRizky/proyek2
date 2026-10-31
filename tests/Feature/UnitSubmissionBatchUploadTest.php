<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UnitSubmissionBatchUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_upload_saves_valid_files_and_reports_oversized_ones(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => UserRole::Prodi]);
        $module = Module::query()->create([
            'name' => 'Kriteria Demo',
            'sort_order' => 1,
        ]);
        $validRequirement = Requirement::query()->create([
            'module_id' => $module->id,
            'title' => 'Dokumen Valid',
            'sort_order' => 1,
        ]);
        $oversizedRequirement = Requirement::query()->create([
            'module_id' => $module->id,
            'title' => 'Dokumen Besar',
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($user)->post(route('unit.modules.submissions.batch', $module), [
            'expected_file_count' => 2,
            'files' => [
                $validRequirement->id => UploadedFile::fake()->create('valid.pdf', 512, 'application/pdf'),
                $oversizedRequirement->id => UploadedFile::fake()->create('besar.pdf', 25000, 'application/pdf'),
            ],
        ]);

        $response
            ->assertRedirect(route('unit.submissions.module', $module))
            ->assertSessionHas('status')
            ->assertSessionHas('upload_partial_failure')
            ->assertSessionHasErrors('files.'.$oversizedRequirement->id);

        $this->assertSame(1, Submission::query()->count());
        $this->assertDatabaseHas('submissions', [
            'requirement_id' => $validRequirement->id,
            'user_id' => $user->id,
            'original_filename' => 'valid.pdf',
        ]);
    }

    public function test_batch_upload_saves_all_files_in_one_request(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => UserRole::Prodi]);
        $module = Module::query()->create([
            'name' => 'Kriteria Demo',
            'sort_order' => 1,
        ]);

        $requirements = collect(range(1, 5))->map(function (int $sortOrder) use ($module) {
            return Requirement::query()->create([
                'module_id' => $module->id,
                'title' => 'Dokumen '.$sortOrder,
                'sort_order' => $sortOrder,
            ]);
        });

        $files = $requirements->mapWithKeys(fn (Requirement $requirement) => [
            $requirement->id => UploadedFile::fake()->create('file-'.$requirement->sort_order.'.pdf', 256, 'application/pdf'),
        ])->all();

        $response = $this->actingAs($user)->post(route('unit.modules.submissions.batch', $module), [
            'expected_file_count' => 5,
            'files' => $files,
        ]);

        $response
            ->assertRedirect(route('unit.submissions.module', $module))
            ->assertSessionHas('status')
            ->assertSessionDoesntHaveErrors();

        $this->assertSame(5, Submission::query()->count());
    }

    public function test_single_file_json_upload_works_for_ajax_client(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => UserRole::Prodi]);
        $module = Module::query()->create(['name' => 'Kriteria Demo', 'sort_order' => 1]);
        $requirement = Requirement::query()->create([
            'module_id' => $module->id,
            'title' => 'Dokumen Valid',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->postJson(route('unit.submissions.store', $requirement), [
            'document' => UploadedFile::fake()->create('valid.pdf', 512, 'application/pdf'),
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('submission.requirement_id', $requirement->id);

        $this->assertSame(1, Submission::query()->count());
    }

    public function test_upload_with_google_drive_links_and_multiple_files_works(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => UserRole::Prodi]);
        $module = Module::query()->create(['name' => 'Kriteria Demo', 'sort_order' => 1]);
        $requirement = Requirement::query()->create([
            'module_id' => $module->id,
            'title' => 'Dokumen Valid',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('unit.submissions.store', $requirement), [
            'google_drive_links' => [
                ['name' => 'SK Rektor', 'url' => 'https://drive.google.com/link1'],
                ['name' => 'Laporan Akreditasi', 'url' => 'https://drive.google.com/link2'],
            ],
            'documents' => [
                UploadedFile::fake()->create('doc1.pdf', 256, 'application/pdf'),
                UploadedFile::fake()->create('doc2.xlsx', 512, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
            ],
        ]);

        $response
            ->assertRedirect(route('unit.submissions.module', $requirement->module))
            ->assertSessionHas('status')
            ->assertSessionDoesntHaveErrors();

        $submission = Submission::query()->first();
        $this->assertNotNull($submission);
        $this->assertSame($requirement->id, $submission->requirement_id);
        
        // Assert json columns are correctly stored
        $this->assertCount(2, $submission->google_drive_links);
        $this->assertSame('SK Rektor', $submission->google_drive_links[0]['name']);
        $this->assertSame('https://drive.google.com/link1', $submission->google_drive_links[0]['url']);
        
        $this->assertCount(2, $submission->files);
        $this->assertSame('doc1.pdf', $submission->files[0]['original_filename']);
        $this->assertSame('doc2.xlsx', $submission->files[1]['original_filename']);
        
        // Assert first file is filled in legacy columns
        $this->assertSame('doc1.pdf', $submission->original_filename);
        $this->assertNotNull($submission->file_path);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Module;
use App\Models\Perti;
use App\Models\Prodi;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PertiValidationTest extends TestCase
{
    use RefreshDatabase;

    private User $pertiUser;
    private User $prodiUser;
    private Prodi $prodiRecord;
    private Module $module;
    private Requirement $requirement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pertiUser = User::factory()->create(['role' => UserRole::Perti]);
        $this->prodiUser = User::factory()->create(['role' => UserRole::Prodi]);
        
        $this->prodiRecord = $this->prodiUser->prodiProfile;
        $this->prodiRecord->update([
            'perti_id' => $this->pertiUser->pertiProfile->id,
        ]);

        $this->module = Module::query()->create([
            'name' => 'Kriteria 1 Test',
            'sort_order' => 1,
        ]);

        $this->requirement = Requirement::query()->create([
            'module_id' => $this->module->id,
            'title' => 'Dokumen Visi Misi',
            'sort_order' => 1,
        ]);
    }

    public function test_perti_can_approve_prodi_submission(): void
    {
        $submission = Submission::query()->create([
            'requirement_id' => $this->requirement->id,
            'user_id' => $this->prodiUser->id,
            'file_path' => 'accreditation/test.pdf',
            'original_filename' => 'test.pdf',
            'status' => SubmissionStatus::Uploaded,
            'version' => 1,
            'is_latest' => true,
        ]);

        $response = $this->actingAs($this->pertiUser)
            ->post(route('perti.submissions.validate', $submission), [
                'status' => 'approved',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $submission->refresh();
        $this->assertEquals(SubmissionStatus::Approved, $submission->status);
        $this->assertEquals($this->pertiUser->id, $submission->validated_by);
        $this->assertNotNull($submission->validated_at);
    }

    public function test_perti_can_request_revision_with_notes(): void
    {
        $submission = Submission::query()->create([
            'requirement_id' => $this->requirement->id,
            'user_id' => $this->prodiUser->id,
            'file_path' => 'accreditation/test.pdf',
            'original_filename' => 'test.pdf',
            'status' => SubmissionStatus::Uploaded,
            'version' => 1,
            'is_latest' => true,
        ]);

        $response = $this->actingAs($this->pertiUser)
            ->post(route('perti.submissions.validate', $submission), [
                'status' => 'revision',
                'validation_notes' => 'Tanda tangan dekan belum terlampir, mohon disahkan ulang.',
            ]);

        $response->assertSessionHasNoErrors();

        $submission->refresh();
        $this->assertEquals(SubmissionStatus::Revision, $submission->status);
        $this->assertEquals('Tanda tangan dekan belum terlampir, mohon disahkan ulang.', $submission->validation_notes);
    }

    public function test_prodi_reupload_resets_status_to_uploaded_for_perti_revalidation(): void
    {
        Storage::fake('local');

        $submission = Submission::query()->create([
            'requirement_id' => $this->requirement->id,
            'user_id' => $this->prodiUser->id,
            'file_path' => 'accreditation/test.pdf',
            'original_filename' => 'test.pdf',
            'status' => SubmissionStatus::Revision,
            'validation_notes' => 'Perlu tanda tangan.',
            'version' => 1,
            'is_latest' => true,
        ]);

        $response = $this->actingAs($this->prodiUser)
            ->post(route('unit.submissions.store', $this->requirement), [
                'documents' => [
                    UploadedFile::fake()->create('revisi_v2.pdf', 256, 'application/pdf'),
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $latestSubmission = Submission::query()
            ->where('requirement_id', $this->requirement->id)
            ->where('user_id', $this->prodiUser->id)
            ->where('is_latest', true)
            ->first();

        $this->assertNotNull($latestSubmission);
        $this->assertEquals(2, $latestSubmission->version);
        $this->assertEquals(SubmissionStatus::Uploaded, $latestSubmission->status);
    }

    public function test_perti_can_batch_approve_module_submissions(): void
    {
        $req2 = Requirement::query()->create([
            'module_id' => $this->module->id,
            'title' => 'Dokumen Renstra',
            'sort_order' => 2,
        ]);

        Submission::query()->create([
            'requirement_id' => $this->requirement->id,
            'user_id' => $this->prodiUser->id,
            'file_path' => 'doc1.pdf',
            'original_filename' => 'doc1.pdf',
            'status' => SubmissionStatus::Uploaded,
            'version' => 1,
            'is_latest' => true,
        ]);

        Submission::query()->create([
            'requirement_id' => $req2->id,
            'user_id' => $this->prodiUser->id,
            'file_path' => 'doc2.pdf',
            'original_filename' => 'doc2.pdf',
            'status' => SubmissionStatus::Uploaded,
            'version' => 1,
            'is_latest' => true,
        ]);

        $response = $this->actingAs($this->pertiUser)
            ->post(route('perti.prodis.modul.batch-validate', [$this->prodiRecord->id, $this->module->id]));

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $approvedCount = Submission::query()
            ->where('user_id', $this->prodiUser->id)
            ->where('status', SubmissionStatus::Approved)
            ->count();

        $this->assertEquals(2, $approvedCount);
    }

    public function test_perti_can_request_revision_targeting_specific_files_and_links(): void
    {
        $submission = Submission::query()->create([
            'requirement_id' => $this->requirement->id,
            'user_id' => $this->prodiUser->id,
            'file_path' => 'accreditation/test.pdf',
            'original_filename' => 'test.pdf',
            'status' => SubmissionStatus::Uploaded,
            'version' => 1,
            'is_latest' => true,
        ]);

        $response = $this->actingAs($this->pertiUser)
            ->post(route('perti.submissions.validate', $submission), [
                'status' => 'revision',
                'target_files' => ['laporan-ringkasan.pdf'],
                'target_links' => ['SK Rektor Link'],
                'validation_notes' => 'Harap disahkan dan diperbaiki.',
            ]);

        $response->assertSessionHasNoErrors();

        $submission->refresh();
        $this->assertEquals(SubmissionStatus::Revision, $submission->status);
        $this->assertStringContainsString('Target Revisi', $submission->validation_notes);
        $this->assertStringContainsString('laporan-ringkasan.pdf', $submission->validation_notes);
        $this->assertStringContainsString('SK Rektor Link', $submission->validation_notes);
        $this->assertStringContainsString('Harap disahkan dan diperbaiki', $submission->validation_notes);
    }
}

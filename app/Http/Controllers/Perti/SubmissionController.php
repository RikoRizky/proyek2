<?php

namespace App\Http\Controllers\Perti;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\SendsSubmissionFile;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    use SendsSubmissionFile;

    public function validateSubmission(\Illuminate\Http\Request $request, Submission $submission): \Illuminate\Http\RedirectResponse
    {
        $submission->load('user');
        $this->authorizeSubmission($submission);

        $validated = $request->validate([
            'status'           => 'required|in:approved,revision',
            'validation_notes' => 'nullable|string|max:2000',
            'target_files'     => 'nullable|array',
            'target_files.*'   => 'string|max:255',
            'target_links'     => 'nullable|array',
            'target_links.*'   => 'string|max:255',
        ]);

        $finalNotes = $validated['validation_notes'] ?? null;

        if ($validated['status'] === 'revision') {
            if (!empty($finalNotes) && str_contains($finalNotes, '📌 Target Revisi:')) {
                $parts = explode('💬 Catatan Perti:', $finalNotes);
                $finalNotes = isset($parts[1]) ? trim($parts[1]) : '';
            }

            $targetItems = [];
            if (!empty($validated['target_files'])) {
                $targetItems[] = "• Berkas Dokumen: " . implode(', ', $validated['target_files']);
            }
            if (!empty($validated['target_links'])) {
                $targetItems[] = "• Link Google Drive: " . implode(', ', $validated['target_links']);
            }

            if (!empty($targetItems)) {
                $formattedTargets = "📌 Target Revisi:\n" . implode("\n", $targetItems);
                if (!empty($finalNotes)) {
                    $finalNotes = $formattedTargets . "\n\n💬 Catatan Perti:\n" . trim($finalNotes);
                } else {
                    $finalNotes = $formattedTargets;
                }
            }
        }

        $submission->update([
            'status'           => $validated['status'],
            'validation_notes' => $finalNotes,
            'validated_at'     => now(),
            'validated_by'     => auth()->id(),
        ]);

        $statusLabel = $validated['status'] === 'approved' ? 'Sesuai' : 'Perlu Revisi';

        return redirect()->route('perti.submissions.view', $submission)->with('status', "Status validasi berhasil diperbarui: {$statusLabel}.");
    }

    public function show(Submission $submission): View
    {
        $submission->load(['user', 'requirement.module']);
        $this->authorizeSubmission($submission);

        $history = Submission::query()
            ->where('requirement_id', $submission->requirement_id)
            ->where('user_id', $submission->user_id)
            ->orderByDesc('version')
            ->get();

        // Resolve the Prodi record ID (perti.prodis.modul route needs prodis.id, not user_id)
        $prodiRecord = \App\Models\Prodi::query()
            ->where('user_id', $submission->user_id)
            ->first();
        $prodiId = $prodiRecord?->id;

        return view('perti.submissions.show', compact('submission', 'history', 'prodiId'));
    }

    public function viewer(Submission $submission): View
    {
        $submission->load(['user', 'requirement.module']);
        $this->authorizeSubmission($submission);

        $fileIndex = request()->query('file');
        $activeFileIndex = 0;

        if ($fileIndex !== null) {
            $activeFileIndex = (int) $fileIndex;
        }

        $files = $submission->files ?? [];
        $activeFile = $files[$activeFileIndex] ?? null;

        $filename = $activeFile ? $activeFile['original_filename'] : $submission->original_filename;

        // Resolve the Prodi record ID (perti.prodis.modul route needs prodis.id, not user_id)
        $prodiRecord = \App\Models\Prodi::query()
            ->where('user_id', $submission->user_id)
            ->first();
        $prodiId = $prodiRecord?->id;

        $backUrl = $prodiId
            ? route('perti.prodis.modul', [$prodiId, $submission->requirement->module_id])
            : route('perti.prodis.index');

        return view('submissions.viewer', [
            'submission'      => $submission,
            'title'           => $submission->requirement->title,
            'filename'        => $filename,
            'activeFileIndex' => $activeFileIndex,
            'routePrefix'     => 'perti',
            'inlineUrl'       => route('perti.submissions.inline', [$submission, 'file' => $activeFileIndex]),
            'downloadUrl'     => route('perti.submissions.download', [$submission, 'file' => $activeFileIndex]),
            'backUrl'         => $backUrl,
        ]);
    }

    public function inline(Submission $submission): StreamedResponse
    {
        $submission->load('user');
        $this->authorizeSubmission($submission);

        return $this->submissionInlineResponse($submission);
    }

    public function download(Submission $submission): StreamedResponse
    {
        $submission->load('user');
        $this->authorizeSubmission($submission);

        return $this->submissionDownloadResponse($submission);
    }

    private function authorizeSubmission(Submission $submission): void
    {
        $user = auth()->user();

        // Cek apakah submission ini milik prodi yang berada di bawah naungan perti yang sedang login
        $pertiProfile = $user?->pertiProfile;

        $isOwned = $pertiProfile && $submission->user &&
            $submission->user->role === \App\Enums\UserRole::Prodi &&
            \App\Models\Prodi::query()
                ->where('user_id', $submission->user_id)
                ->where('perti_id', $pertiProfile->id)
                ->exists();

        abort_unless(
            $user &&
            $user->role === UserRole::Perti &&
            $isOwned,
            403
        );
    }
}

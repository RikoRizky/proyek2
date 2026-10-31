<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Concerns\SendsSubmissionFile;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionOverviewController extends Controller
{
    use SendsSubmissionFile;

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $pertisQuery = User::query()
            ->where('role', UserRole::Perti)
            ->orderBy('name');

        if ($q !== '') {
            $pertisQuery->where(function ($outer) use ($q) {
                // Match perti name / email directly
                $outer->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    // Or match any prodi / requirement under this perti
                    ->orWhereHas('pertiProfile.prodis.user', function ($query) use ($q) {
                        $query->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%")
                            ->orWhereHas('submissions', function ($s) use ($q) {
                                $s->where('is_latest', true)
                                    ->where(function ($inner) use ($q) {
                                        $inner->whereHas('requirement', fn ($r) => $r->where('title', 'like', "%{$q}%"))
                                            ->orWhere('original_filename', 'like', "%{$q}%");
                                    });
                            });
                    });
            });
        }

        $pertis = $pertisQuery
            ->with(['pertiProfile.prodis.user.submissions' => function ($subQuery) {
                $subQuery->latestForUnit()
                    ->with(['requirement.module'])
                    ->orderBy('requirement_id');
            }])
            ->paginate(12)
            ->withQueryString();


        return view('admin.submissions.index', compact('pertis', 'q'));
    }

    public function viewer(Submission $submission): View
    {
        $this->authorizeAdminSubmission($submission);
        $submission->load(['requirement.module', 'user']);

        $fileIndex = request()->query('file');
        $activeFileIndex = 0;

        if ($fileIndex !== null) {
            $activeFileIndex = (int) $fileIndex;
        }

        $files = $submission->files ?? [];
        $activeFile = $files[$activeFileIndex] ?? null;

        $filename = $activeFile ? $activeFile['original_filename'] : $submission->original_filename;

        return view('submissions.viewer', [
            'submission' => $submission,
            'title' => $submission->requirement->title.' — '.$submission->user->name,
            'filename' => $filename,
            'activeFileIndex' => $activeFileIndex,
            'routePrefix' => 'admin',
            'inlineUrl' => route('admin.submissions.inline', [$submission, 'file' => $activeFileIndex]),
            'downloadUrl' => route('admin.submissions.download', [$submission, 'file' => $activeFileIndex]),
            'backUrl' => route('admin.submissions.index'),
        ]);
    }

    public function inline(Submission $submission): StreamedResponse
    {
        $this->authorizeAdminSubmission($submission);

        return $this->submissionInlineResponse($submission);
    }

    public function download(Submission $submission): StreamedResponse
    {
        $this->authorizeAdminSubmission($submission);

        return $this->submissionDownloadResponse($submission);
    }

    private function authorizeAdminSubmission(Submission $submission): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
        abort_unless($submission->user?->role === UserRole::Prodi, 403);
    }
}

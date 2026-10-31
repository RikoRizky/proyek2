<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Submission;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait SendsSubmissionFile
{
    protected function getFileFromSubmission(Submission $submission, ?int $fileIndex): ?array
    {
        if ($fileIndex === null) {
            return null;
        }

        $files = $submission->files;
        if (!is_array($files)) {
            $files = json_decode($files, true) ?? [];
        }

        return $files[$fileIndex] ?? null;
    }

    protected function submissionDownloadResponse(Submission $submission): StreamedResponse
    {
        $fileIndex = request()->query('file');
        if ($fileIndex !== null) {
            $file = $this->getFileFromSubmission($submission, (int) $fileIndex);
            abort_unless($file && Storage::disk('local')->exists($file['file_path']), 404);
            return Storage::disk('local')->download($file['file_path'], $file['original_filename']);
        }

        abort_unless(Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->download($submission->file_path, $submission->original_filename);
    }

    protected function submissionInlineResponse(Submission $submission): StreamedResponse
    {
        $fileIndex = request()->query('file');
        if ($fileIndex !== null) {
            $file = $this->getFileFromSubmission($submission, (int) $fileIndex);
            abort_unless($file && Storage::disk('local')->exists($file['file_path']), 404);
            return Storage::disk('local')->response(
                $file['file_path'],
                $file['original_filename'],
                ['Content-Disposition' => 'inline; filename="'.$this->asciiFilename($file['original_filename']).'"']
            );
        }

        abort_unless(Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->response(
            $submission->file_path,
            $submission->original_filename,
            ['Content-Disposition' => 'inline; filename="'.$this->asciiFilename($submission->original_filename).'"']
        );
    }

    private function asciiFilename(string $name): string
    {
        return preg_replace('/[^\x20-\x7E]/', '_', $name) ?: 'document';
    }
}

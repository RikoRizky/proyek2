<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\User;
use App\Support\UploadProgress;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $stats = match ($user->role) {
            UserRole::Admin => $this->adminStats(),
            UserRole::Perti => $this->pertiStats($user),
            UserRole::Prodi => $this->unitStats($user),
        };

        return view('dashboard', [
            'stats' => $stats,
            'progress' => match ($user->role) {
                UserRole::Admin => UploadProgress::forAllUnits(),
                UserRole::Perti => UploadProgress::forAllUnitsOfPerti($user),
                UserRole::Prodi => UploadProgress::forUnit($user),
                default => null,
            },
        ]);
    }

    private function pertiStats(User $user): array
    {
        $pertiProfile = $user->pertiProfile;

        // Ambil user_id semua Prodi yang berada di bawah Perti ini
        $prodiUserIds = $pertiProfile
            ? \App\Models\Prodi::query()
                ->where('perti_id', $pertiProfile->id)
                ->pluck('user_id')
            : collect();

        $uploadedLatest = Submission::query()
            ->latestForUnit()
            ->whereIn('user_id', $prodiUserIds)
            ->where('status', '!=', SubmissionStatus::Pending)
            ->count();

        $pendingValidationCount = Submission::query()
            ->latestForUnit()
            ->whereIn('user_id', $prodiUserIds)
            ->where('status', SubmissionStatus::Uploaded)
            ->count();

        $totalReq = Requirement::query()->count();

        return [
            'role'                   => UserRole::Perti,
            'prodiCount'             => $prodiUserIds->count(),
            'totalRequirements'      => $totalReq,
            'uploadedLatest'         => $uploadedLatest,
            'pendingValidationCount' => $pendingValidationCount,
        ];
    }


    private function adminStats(): array
    {
        $modules = Module::query()
            ->withCount('requirements')
            ->orderBy('sort_order')
            ->get();

        $uploadedLatest = Submission::query()
            ->latestForUnit()
            ->where('status', '!=', SubmissionStatus::Pending)
            ->count();

        return [
            'role' => UserRole::Admin,
            'modules' => $modules,
            'pertiCount' => User::query()->where('role', UserRole::Perti)->count(),
            'unitCount' => User::query()->where('role', UserRole::Prodi)->count(),
            'totalRequirements' => Requirement::query()->count(),
            'uploadedLatest' => $uploadedLatest,
        ];
    }

    private function unitStats(User $user): array
    {
        $modules = Module::query()
            ->with(['requirements' => function ($q) use ($user) {
                $q->orderBy('sort_order')
                    ->with(['submissions' => function ($s) use ($user) {
                        $s->where('user_id', $user->id)->latestForUnit();
                    }]);
            }])
            ->orderBy('sort_order')
            ->get();

        $totalReq = Requirement::query()->count();

        $latestSubmissions = Submission::query()
            ->where('user_id', $user->id)
            ->latestForUnit()
            ->get()
            ->keyBy('requirement_id');

        $uploadedCount = $latestSubmissions->filter(
            fn (Submission $s) => $s->status !== SubmissionStatus::Pending
        )->count();

        $revisionCount = $latestSubmissions->filter(
            fn (Submission $s) => $s->status === SubmissionStatus::Revision
        )->count();

        $notUploadedCount = max(0, $totalReq - $latestSubmissions->count());

        return [
            'role'              => UserRole::Prodi,
            'modules'           => $modules,
            'totalRequirements' => $totalReq,
            'notUploadedCount'  => $notUploadedCount,
            'revisionCount'     => $revisionCount,
            'progressPercent'   => $totalReq > 0 ? round(($uploadedCount / $totalReq) * 100) : 0,
        ];
    }
}

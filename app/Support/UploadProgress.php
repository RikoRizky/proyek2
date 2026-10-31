<?php

namespace App\Support;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Collection;

class UploadProgress
{
    public static function totalRequirements(): int
    {
        return Requirement::query()->count();
    }

    public static function modulesWithRequirements(): Collection
    {
        return Module::query()
            ->with(['requirements:id,module_id'])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'sort_order']);
    }

    /**
     * @return array{uploaded: int, total: int, percent: int, modules: list<array<string, mixed>>}
     */
    public static function forUnit(User $unit): array
    {
        $totalReq = self::totalRequirements();
        $modules = self::modulesWithRequirements();

        $latestSubmissions = Submission::query()
            ->where('user_id', $unit->id)
            ->latestForUnit()
            ->get()
            ->keyBy('requirement_id');

        $moduleRows = $modules->map(function (Module $module) use ($latestSubmissions) {
            $total = $module->requirements->count();
            
            $uploaded = 0;
            $approved = 0;
            $revision = 0;
            $pendingValidation = 0;

            foreach ($module->requirements as $requirement) {
                $sub = $latestSubmissions->get($requirement->id);
                if ($sub && $sub->status !== SubmissionStatus::Pending) {
                    $uploaded++;
                    if ($sub->status === SubmissionStatus::Approved) {
                        $approved++;
                    } elseif ($sub->status === SubmissionStatus::Revision) {
                        $revision++;
                    } elseif ($sub->status === SubmissionStatus::Uploaded) {
                        $pendingValidation++;
                    }
                }
            }

            $percent = $total > 0 ? (int) round(($uploaded / $total) * 100) : 0;
            $approvedPercent = $total > 0 ? (int) round(($approved / $total) * 100) : 0;

            return [
                'module_id'          => $module->id,
                'name'               => $module->name,
                'short_label'        => $module->shortLabel(),
                'uploaded'           => $uploaded,
                'approved'           => $approved,
                'revision'           => $revision,
                'pending_validation' => $pendingValidation,
                'total'              => $total,
                'percent'            => $percent,
                'approved_percent'   => $approvedPercent,
            ];
        })->values()->all();

        $uploadedTotal = (int) collect($moduleRows)->sum('uploaded');
        $approvedTotal = (int) collect($moduleRows)->sum('approved');
        $revisionTotal = (int) collect($moduleRows)->sum('revision');
        $pendingValidationTotal = (int) collect($moduleRows)->sum('pending_validation');

        return [
            'uploaded'           => $uploadedTotal,
            'approved'           => $approvedTotal,
            'revision'           => $revisionTotal,
            'pending_validation' => $pendingValidationTotal,
            'total'              => $totalReq,
            'percent'            => $totalReq > 0 ? (int) round(($uploadedTotal / $totalReq) * 100) : 0,
            'approved_percent'   => $totalReq > 0 ? (int) round(($approvedTotal / $totalReq) * 100) : 0,
            'modules'            => $moduleRows,
        ];
    }

    /**
     * @return array{
     *     total_requirements: int,
     *     units: list<array<string, mixed>>,
     *     summary: array{unit_count: int, complete_count: int, in_progress_count: int, empty_count: int, average_percent: float}
     * }
     */
    public static function forAllUnits(): array
    {
        $totalReq = self::totalRequirements();
        $units = User::query()
            ->where('role', UserRole::Prodi)
            ->with('prodiProfile.perti.user')
            ->orderBy('name')
            ->get();

        $rows = $units->map(function (User $unit) use ($totalReq) {
            $progress = self::forUnit($unit);
            $uniName = $unit->prodiProfile?->perti?->user?->name ?? 'Lainnya';

            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'university_name' => $uniName,
                'uploaded' => $progress['uploaded'],
                'total' => $totalReq,
                'percent' => $progress['percent'],
                'modules' => $progress['modules'],
            ];
        })->values()->all();

        $collection = collect($rows);

        return [
            'total_requirements' => $totalReq,
            'units' => $rows,
            'summary' => [
                'unit_count' => $collection->count(),
                'complete_count' => $collection->where('percent', 100)->count(),
                'in_progress_count' => $collection->where(fn (array $row) => $row['percent'] > 0 && $row['percent'] < 100)->count(),
                'empty_count' => $collection->where('percent', 0)->count(),
                'average_percent' => $collection->isEmpty() ? 0.0 : round($collection->avg('percent'), 1),
            ],
        ];
    }

    /**
     * @return array{
     *     total_requirements: int,
     *     units: list<array<string, mixed>>,
     *     summary: array{unit_count: int, complete_count: int, in_progress_count: int, empty_count: int, average_percent: float}
     * }
     */
    public static function forAllUnitsOfPerti(User $perti): array
    {
        $totalReq = self::totalRequirements();

        $pertiProfile = $perti->pertiProfile;
        if (is_null($pertiProfile)) {
            return [
                'total_requirements' => $totalReq,
                'units' => [],
                'summary' => [
                    'unit_count' => 0,
                    'complete_count' => 0,
                    'in_progress_count' => 0,
                    'empty_count' => 0,
                    'average_percent' => 0.0,
                ],
            ];
        }

        // Ambil user_id semua prodi di bawah perti ini
        $prodiUserIds = \App\Models\Prodi::query()
            ->where('perti_id', $pertiProfile->id)
            ->pluck('user_id');

        $units = User::query()
            ->whereIn('id', $prodiUserIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        $rows = $units->map(function (User $unit) use ($totalReq) {
            $progress = self::forUnit($unit);
            $prodiRecord = \App\Models\Prodi::where('user_id', $unit->id)->first();

            return [
                'id'                 => $unit->id,
                'prodi_id'           => $prodiRecord?->id,
                'name'               => $unit->name,
                'uploaded'           => $progress['uploaded'],
                'approved'           => $progress['approved'],
                'revision'           => $progress['revision'],
                'pending_validation' => $progress['pending_validation'],
                'total'              => $totalReq,
                'percent'            => $progress['percent'],
                'modules'            => $progress['modules'],
            ];
        })->values()->all();

        $collection = collect($rows);

        return [
            'total_requirements' => $totalReq,
            'units' => $rows,
            'summary' => [
                'unit_count' => $collection->count(),
                'complete_count' => $collection->where('percent', 100)->count(),
                'in_progress_count' => $collection->where(fn (array $row) => $row['percent'] > 0 && $row['percent'] < 100)->count(),
                'empty_count' => $collection->where('percent', 0)->count(),
                'average_percent' => $collection->isEmpty() ? 0.0 : round($collection->avg('percent'), 1),
            ],
        ];
    }
}

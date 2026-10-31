<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Requirement;
use App\Models\Submission;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $modules = Module::query()->withCount('requirements')->orderBy('sort_order')->get();

        $recentUsers = User::query()->orderByDesc('created_at')->limit(8)->get();
        $recentSubmissions = Submission::query()
            ->latestForUnit()
            ->with(['user', 'requirement.module'])
            ->whereHas('user', fn ($q) => $q->where('role', UserRole::Prodi))
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'modules' => $modules,
            'pertiCount' => User::query()->where('role', UserRole::Perti)->count(),
            'unitCount' => User::query()->where('role', UserRole::Prodi)->count(),
            'requirementsCount' => Requirement::query()->count(),
            'submissionsCount' => Submission::query()->count(),
            'recentUsers' => $recentUsers,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }
}

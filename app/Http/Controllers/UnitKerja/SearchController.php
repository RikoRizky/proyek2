<?php

namespace App\Http\Controllers\UnitKerja;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Services\UnifiedSearchService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __construct(
        protected UnifiedSearchService $searchService
    ) {}

    /**
     * Tampilkan halaman pencarian semua kriteria, dokumen LED, tabel LKPS, dan Arsip Tambahan untuk Unit Kerja (Prodi).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        abort_unless($user && $user->role === UserRole::Prodi, 403);

        $data = $this->searchService->buildForProdi($user);

        return view('unit.search', array_merge($data, [
            'initialQuery' => (string) $request->input('q', ''),
        ]));
    }
}

<?php

namespace App\Http\Controllers\Perti;

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
     * Tampilkan halaman pencarian semua kriteria, dokumen LED, tabel LKPS, dan Arsip Tambahan untuk Perguruan Tinggi (Perti).
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        abort_unless($user && $user->role === UserRole::Perti, 403);

        $data = $this->searchService->buildForPerti(
            $user,
            $request->input('prodi_id') !== null ? (string) $request->input('prodi_id') : null
        );

        return view('perti.search', array_merge($data, [
            'initialQuery' => (string) $request->input('q', ''),
        ]));
    }
}

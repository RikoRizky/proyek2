<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DiscussionController extends Controller
{
    /**
     * Display a listing of all discussion submissions.
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');

        $discussions = Discussion::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('perusahaan', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.discussions.index', [
            'discussions' => $discussions,
            'search'      => $search,
        ]);
    }
}

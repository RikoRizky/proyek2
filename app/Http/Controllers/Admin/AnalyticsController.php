<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\UploadProgress;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.analytics.index', [
            'progress' => UploadProgress::forAllUnits(),
        ]);
    }
}

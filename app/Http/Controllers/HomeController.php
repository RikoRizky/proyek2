<?php

namespace App\Http\Controllers;

use App\Support\UploadProgress;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home.index', [
            'progress' => UploadProgress::forAllUnits(),
        ]);
    }
}

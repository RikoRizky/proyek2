<?php

namespace App\Http\Controllers\Perti;

use App\Http\Controllers\Concerns\ManagesSupportingDocuments;
use App\Http\Controllers\Controller;

class SupportingDocumentController extends Controller
{
    use ManagesSupportingDocuments;

    protected function archiveRoutePrefix(): string
    {
        return 'perti';
    }
}

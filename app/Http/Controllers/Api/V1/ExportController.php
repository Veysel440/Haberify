<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Exports\ArticlesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function __construct()
    { $this->middleware(['auth:sanctum','permission:analytics.view']); }

    public function articlesCsv()
    { return Excel::download(new ArticlesExport, 'articles.csv'); }
}

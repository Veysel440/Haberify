<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Imports\CategoriesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function __construct()
    { $this->middleware(['auth:sanctum','permission:categories.manage']); }

    public function categories(Request $r)
    {
        $r->validate(['file'=>'required|file|mimes:csv,txt']);
        Excel::import(new CategoriesImport, $r->file('file'));
        return response()->noContent();
    }
}

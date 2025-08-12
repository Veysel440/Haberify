<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller
{
    public function __construct()
    { $this->middleware(['auth:sanctum','permission:analytics.view']); }

    public function index()
    {
        $logs = Activity::latest()->limit(100)->get(['id','log_name','description','subject_type','subject_id','causer_id','properties','created_at']);
        return response()->json(['data'=>$logs]);
    }
}

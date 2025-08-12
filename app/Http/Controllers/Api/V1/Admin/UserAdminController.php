<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function __construct()
    { $this->middleware(['auth:sanctum','permission:analytics.view']); }

    public function index(Request $r)
    {
        $q = User::query()
            ->when($r->filled('q'), fn($qq)=>$qq->where('name','like','%'.$r->q.'%')->orWhere('email','like','%'.$r->q.'%'))
            ->when($r->filled('role'), fn($qq)=>$qq->role($r->role));
        return response()->json($q->latest()->paginate($r->integer('per_page',20)));
    }

    public function assignRole(int $id, Request $r)
    {
        $r->validate(['role'=>'required|string']);
        $u = User::findOrFail($id);
        $u->syncRoles([$r->role]);
        return response()->json(['data'=>['id'=>$u->id,'roles'=>$u->getRoleNames()]]);
    }
}

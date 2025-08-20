<?php

namespace App\Http\Controllers\Api\V1;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Sanctum\PersonalAccessToken;

class SessionController extends Controller
{
    public function index(Request $r)
    {
        $tokens = $r->user()->tokens()->get(['id','name','ip','ua','last_used_at','created_at','expires_at']);
        return response()->json(['data'=>$tokens]);
    }

    public function destroy(Request $r, int $id)
    {
        $tok = PersonalAccessToken::findOrFail($id);
        abort_unless($tok->tokenable_id === $r->user()->id, 403);
        $tok->delete();
        return response()->noContent();
    }

    public function destroyOthers(Request $r)
    {
        $current = $r->user()->currentAccessToken()?->id;
        $r->user()->tokens()->when($current, fn($q)=>$q->where('id','!=',$current))->delete();
        return response()->noContent();
    }
}

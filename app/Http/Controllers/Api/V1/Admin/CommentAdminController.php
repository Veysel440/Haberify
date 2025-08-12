<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Comment\BanUserRequest;
use App\Models\User;

class CommentAdminController extends Controller
{
    public function __construct()
    { $this->middleware(['auth:sanctum','permission:comments.moderate']); }

    public function ban(BanUserRequest $r)
    {
        $v = $r->validated();
        $u = User::findOrFail($v['user_id']);
        $u->forceFill([
            'is_comment_banned'   => true,
            'comment_banned_until'=> $v['until'] ?? null,
            'comment_ban_reason'  => $v['reason'] ?? null,
        ])->save();

        return response()->json(['data'=>[
            'user_id'=>$u->id,
            'banned'=>true,
            'until'=>$u->comment_banned_until,
            'reason'=>$u->comment_ban_reason,
        ]], 200);
    }

    public function unban(int $userId)
    {
        $u = User::findOrFail($userId);
        $u->forceFill([
            'is_comment_banned'=>false,
            'comment_banned_until'=>null,
            'comment_ban_reason'=>null,
        ])->save();

        return response()->json(['data'=>['user_id'=>$u->id,'banned'=>false]], 200);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function __construct(){ $this->middleware('auth:sanctum'); }

    public function index()
    {
        $user = request()->user();
        return response()->json([
            'data' => $user->notifications()->latest()->limit(50)->get()
        ]);
    }

    public function unreadCount()
    {
        return response()->json(['count' => request()->user()->unreadNotifications()->count()]);
    }

    public function markAsRead(string $id)
    {
        $n = request()->user()->notifications()->where('id',$id)->firstOrFail();
        $n->markAsRead();
        return response()->noContent();
    }
}

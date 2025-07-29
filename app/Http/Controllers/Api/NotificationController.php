<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(30)
            ->get();
        return response()->json(['data' => $notifications]);
    }

    public function markAsRead($id)
    {
        $notif = Notification::findOrFail($id);
        $notif->read = true;
        $notif->save();
        return response()->json(['message' => 'Okundu olarak işaretlendi.']);
    }
}


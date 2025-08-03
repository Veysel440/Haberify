<?php

namespace App\Http\Controllers\Api\Notification;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\NotificationResource;
use App\Models\Notification;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $notifications = Notification::where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->take(30)
                ->get();
            return ApiResponse::success(
                NotificationResource::collection($notifications),
                "Bildirimler listelendi"
            );
        } catch (Exception $e) {
            return ApiResponse::error("Bildirimler getirilemedi.", 500);
        }
    }

    public function markAsRead($id, Request $request)
    {
        try {
            $user = $request->user();
            $notif = Notification::findOrFail($id);
            if ($notif->user_id !== $user->id) {
                return ApiResponse::error("Yetkisiz erişim.", 403);
            }
            $notif->read = true;
            $notif->save();
            return ApiResponse::success(null, 'Okundu olarak işaretlendi.');
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Bildirim bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("İşlem başarısız.", 500);
        }
    }
}

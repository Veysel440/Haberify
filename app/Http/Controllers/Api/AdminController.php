<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminServiceInterface;
use App\Helpers\ApiResponse;
use App\Http\Resources\CommentResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Services\UserServiceInterface;
class AdminController extends Controller
{
    private AdminServiceInterface $adminService;

    public function __construct(AdminServiceInterface $adminService)
    {
        $this->adminService = $adminService;
    }

    public function approveComment($id)
    {
        try {
            $comment = $this->adminService->approveComment($id);
            return ApiResponse::success(new CommentResource($comment), "Yorum onaylandı.");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Yorum bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Yorum onaylanamadı.", 500);
        }
    }

    public function rejectComment($id)
    {
        try {
            $comment = $this->adminService->rejectComment($id);
            return ApiResponse::success(new CommentResource($comment), "Yorum reddedildi.");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Yorum bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Yorum reddedilemedi.", 500);
        }
    }

    public function makeFeatured($id)
    {
        try {
            $news = $this->adminService->makeFeatured($id);
            return ApiResponse::success(new NewsResource($news), "Haber öne çıkarıldı.");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Haber bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Haber öne çıkarılamadı.", 500);
        }
    }

    public function unpublishNews($id)
    {
        try {
            $news = $this->adminService->unpublishNews($id);
            return ApiResponse::success(new NewsResource($news), "Haber yayından kaldırıldı.");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Haber bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Haber yayından kaldırılamadı.", 500);
        }
    }

    public function users()
    {
        try {
            $users = $this->adminService->listUsers();
            return ApiResponse::success(UserResource::collection($users), "Kullanıcılar listelendi.");
        } catch (Exception $e) {
            return ApiResponse::error("Kullanıcılar listelenemedi.", 500);
        }
    }

    public function makeAdmin($id)
    {
        try {
            $user = $this->adminService->makeAdmin($id);
            return ApiResponse::success(new UserResource($user), "Kullanıcı admin yapıldı.");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Kullanıcı bulunamadı.", 404);
        } catch (Exception $e) {
            return ApiResponse::error("Kullanıcı admin yapılamadı.", 500);
        }
    }


    public function suspendUser($id, UserServiceInterface $userService)
    {
        try {
            $user = $userService->suspendUser($id);
            return ApiResponse::success(new UserResource($user), "Kullanıcı askıya alındı.");
        } catch (\Exception $e) {
            return ApiResponse::error("Askıya alma işlemi başarısız.", 500);
        }
    }

    public function activateUser($id, UserServiceInterface $userService)
    {
        try {
            $user = $userService->activateUser($id);
            return ApiResponse::success(new UserResource($user), "Kullanıcı aktif edildi.");
        } catch (\Exception $e) {
            return ApiResponse::error("Aktifleştirme işlemi başarısız.", 500);
        }
    }

    public function stats()
    {
        return response()->json([
            'total_users'      => \App\Models\User::count(),
            'total_news'       => \App\Models\News::count(),
            'total_comments'   => \App\Models\Comment::count(),
            'popular_news'     => \App\Models\News::orderByDesc('views')->take(5)->get(['id', 'title', 'views']),
            'most_favorited'   => \App\Models\News::withCount('favorites')->orderByDesc('favorites_count')->take(5)->get(['id', 'title']),
            'most_reported_comments' => \App\Models\Comment::withCount('reports')->orderByDesc('reports_count')->take(5)->get(['id', 'content']),
        ]);
    }
}

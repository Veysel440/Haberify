<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\News\NewsResource;
use App\Http\Resources\User\UserResource;
use App\Services\Admin\AdminService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AdminController extends Controller
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
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
            \Log::error($e);
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
            \Log::error($e);
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
            \Log::error($e);
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
            \Log::error($e);
            return ApiResponse::error("Haber yayından kaldırılamadı.", 500);
        }
    }

    public function users()
    {
        try {
            $users = $this->adminService->listUsers();
            return ApiResponse::success(UserResource::collection($users), "Kullanıcılar listelendi.");
        } catch (Exception $e) {
            \Log::error($e);
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
            \Log::error($e);
            return ApiResponse::error("Kullanıcı admin yapılamadı.", 500);
        }
    }

    public function suspendUser($id)
    {
        try {
            $user = $this->adminService->suspendUser($id);
            return ApiResponse::success(new UserResource($user), "Kullanıcı askıya alındı.");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Kullanıcı bulunamadı.", 404);
        } catch (Exception $e) {
            \Log::error($e);
            return ApiResponse::error("Askıya alma işlemi başarısız.", 500);
        }
    }

    public function activateUser($id)
    {
        try {
            $user = $this->adminService->activateUser($id);
            return ApiResponse::success(new UserResource($user), "Kullanıcı aktif edildi.");
        } catch (ModelNotFoundException $e) {
            return ApiResponse::error("Kullanıcı bulunamadı.", 404);
        } catch (Exception $e) {
            \Log::error($e);
            return ApiResponse::error("Aktifleştirme işlemi başarısız.", 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;

class TrashController extends Controller
{
    public function __construct()
    { $this->middleware(['auth:sanctum']); }

    // ARTICLES
    public function articles()
    {
        $this->authorize('articles.delete');
        $items = \App\Models\Article::onlyTrashed()
            ->orderByDesc('deleted_at')->paginate(per_page(request()));
        return ApiResponse::ok($items->toArray());
    }
    public function articleRestore(int $id)
    {
        $this->authorize('articles.delete');
        $m = \App\Models\Article::onlyTrashed()->findOrFail($id);
        $m->restore();
        return ApiResponse::ok(['restored'=>true,'id'=>$m->id]);
    }
    public function articleForceDelete(int $id)
    {
        $this->authorize('articles.delete');
        $m = \App\Models\Article::onlyTrashed()->findOrFail($id);
        $m->forceDelete();
        return ApiResponse::noContent();
    }

    // CATEGORIES
    public function categories()
    {
        $this->authorize('categories.manage');
        $items = \App\Models\Category::onlyTrashed()->orderByDesc('deleted_at')->paginate(per_page(request()));
        return ApiResponse::ok($items->toArray());
    }
    public function categoryRestore(int $id)
    {
        $this->authorize('categories.manage');
        $m = \App\Models\Category::onlyTrashed()->findOrFail($id);
        $m->restore();
        return ApiResponse::ok(['restored'=>true,'id'=>$m->id]);
    }
    public function categoryForceDelete(int $id)
    {
        $this->authorize('categories.manage');
        $m = \App\Models\Category::onlyTrashed()->findOrFail($id);
        $m->forceDelete();
        return ApiResponse::noContent();
    }

    // TAGS
    public function tags()
    {
        $this->authorize('tags.manage');
        $items = \App\Models\Tag::onlyTrashed()->orderByDesc('deleted_at')->paginate(per_page(request()));
        return ApiResponse::ok($items->toArray());
    }
    public function tagRestore(int $id)
    {
        $this->authorize('tags.manage');
        $m = \App\Models\Tag::onlyTrashed()->findOrFail($id);
        $m->restore();
        return ApiResponse::ok(['restored'=>true,'id'=>$m->id]);
    }
    public function tagForceDelete(int $id)
    {
        $this->authorize('tags.manage');
        $m = \App\Models\Tag::onlyTrashed()->findOrFail($id);
        $m->forceDelete();
        return ApiResponse::noContent();
    }

    // COMMENTS
    public function comments()
    {
        $this->authorize('comments.moderate');
        $items = \App\Models\Comment::onlyTrashed()->orderByDesc('deleted_at')->paginate(per_page(request()));
        return ApiResponse::ok($items->toArray());
    }
    public function commentRestore(int $id)
    {
        $this->authorize('comments.moderate');
        $m = \App\Models\Comment::onlyTrashed()->findOrFail($id);
        $m->restore();
        return ApiResponse::ok(['restored'=>true,'id'=>$m->id]);
    }
    public function commentForceDelete(int $id)
    {
        $this->authorize('comments.moderate');
        $m = \App\Models\Comment::onlyTrashed()->findOrFail($id);
        $m->forceDelete();
        return ApiResponse::noContent();
    }

    // PAGES
    public function pages()
    {
        $this->authorize('pages.manage');
        $items = \App\Models\Page::onlyTrashed()->orderByDesc('deleted_at')->paginate(per_page(request()));
        return ApiResponse::ok($items->toArray());
    }
    public function pageRestore(int $id)
    {
        $this->authorize('pages.manage');
        $m = \App\Models\Page::onlyTrashed()->findOrFail($id);
        $m->restore();
        return ApiResponse::ok(['restored'=>true,'id'=>$m->id]);
    }
    public function pageForceDelete(int $id)
    {
        $this->authorize('pages.manage');
        $m = \App\Models\Page::onlyTrashed()->findOrFail($id);
        $m->forceDelete();
        return ApiResponse::noContent();
    }
}

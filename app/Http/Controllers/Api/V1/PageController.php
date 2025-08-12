<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Http\Requests\Api\V1\Page\{StorePageRequest, UpdatePageRequest};
use App\Http\Resources\Api\V1\PageResource;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __construct(){ $this->middleware(['auth:sanctum','permission:pages.manage'])->except(['show']); }

    public function show(string $slug)
    { return new PageResource(Page::where(['slug'=>$slug,'status'=>'published'])->firstOrFail()); }

    public function store(StorePageRequest $r)
    {
        $d = $r->validated(); $d['slug'] = $d['slug'] ?? \Str::slug($d['title']);
        return (new PageResource(Page::create($d)))->response()->setStatusCode(201);
    }

    public function update(int $id, UpdatePageRequest $r)
    {
        $p = Page::findOrFail($id); $p->update($r->validated());
        return new PageResource($p->fresh());
    }

    public function destroy(int $id)
    { Page::findOrFail($id)->delete(); return response()->noContent(); }
}

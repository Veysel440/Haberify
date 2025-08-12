<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Menu\UpsertMenuRequest;
use App\Http\Resources\Api\V1\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    public function show(string $name)
    {
        $menu = Cache::remember("menu:{$name}", 300, fn()=> Menu::where('name',$name)->first());
        abort_if(!$menu,404);
        return new MenuResource($menu);
    }

    public function update(string $name, UpsertMenuRequest $r)
    {
        $menu = Menu::firstOrCreate(['name'=>$name]);
        $menu->update(['items'=>$r->validated()['items']]);
        Cache::forget("menu:{$name}");
        return new MenuResource($menu->fresh());
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Setting\UpsertSettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function show(string $key)
    {
        $s = Cache::remember("setting:{$key}", 600, fn()=> Setting::find($key));
        abort_if(!$s,404);
        return response()->json(['data'=>['key'=>$s->key,'value'=>$s->value]]);
    }

    public function update(string $key, UpsertSettingRequest $r)
    {
        $s = Setting::updateOrCreate(['key'=>$key], ['value'=>$r->validated()['value']])->fresh();
        Cache::forget("setting:{$key}");
        return response()->json(['data'=>['key'=>$s->key,'value'=>$s->value]]);
    }
}

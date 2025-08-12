<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Media\{UploadCoverRequest, UploadGalleryRequest};
use App\Jobs\ImageOptimizeJob;
use App\Models\Article;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function __construct()
    { $this->middleware(['auth:sanctum','permission:articles.update']); }

    public function uploadCover(int $id, UploadCoverRequest $r)
    {
        $a = Article::findOrFail($id);
        /** @var UploadedFile $f */
        $f = $r->file('file');

        $dir = "articles/{$a->id}";
        $path = $f->storePublicly($dir, ['disk'=>'public']);

        $a->update(['cover_path'=>$path]);
        ImageOptimizeJob::dispatch($path, disk: 'public', keepOriginal: true, widths: [1200, 800, 400]);

        return response()->json([
            'data'=>[
                'cover_url'=>Storage::disk('public')->url($path),
                'variants'=>[
                    '1200'=>Storage::disk('public')->url(ImageOptimizeJob::variantPath($path,1200)),
                    '800' =>Storage::disk('public')->url(ImageOptimizeJob::variantPath($path,800)),
                    '400' =>Storage::disk('public')->url(ImageOptimizeJob::variantPath($path,400)),
                    'webp'=>Storage::disk('public')->url(ImageOptimizeJob::webpPath($path)),
                ]
            ]
        ], 201);
    }

    public function uploadGallery(int $id, UploadGalleryRequest $r)
    {
        $a = Article::findOrFail($id);
        $dir = "articles/{$a->id}/gallery";
        $urls = [];

        foreach ($r->file('files') as $f) {
            $path = $f->storePublicly($dir, ['disk'=>'public']);
            ImageOptimizeJob::dispatch($path, disk: 'public', keepOriginal: true, widths: [1200, 800, 400]);
            $urls[] = [
                'original'=>Storage::disk('public')->url($path),
                'w1200'   =>Storage::disk('public')->url(ImageOptimizeJob::variantPath($path,1200)),
                'w800'    =>Storage::disk('public')->url(ImageOptimizeJob::variantPath($path,800)),
                'w400'    =>Storage::disk('public')->url(ImageOptimizeJob::variantPath($path,400)),
                'webp'    =>Storage::disk('public')->url(ImageOptimizeJob::webpPath($path)),
            ];
        }

        return response()->json(['data'=>$urls], 201);
    }
}

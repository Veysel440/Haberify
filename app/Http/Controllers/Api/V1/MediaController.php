<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Media\{UploadCoverRequest, UploadGalleryRequest};
use App\Jobs\ImageOptimizeJob;
use App\Models\Article;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\VirusScanner;
class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','permission:articles.update']);
    }

    public function uploadCover(int $id, UploadCoverRequest $r)
    {
        $a = Article::findOrFail($id);
        /** @var \Illuminate\Http\UploadedFile $f */
        $f = $r->file('file');

        if (!app(VirusScanner::class)->isClean($f->getPathname())) {
            return response()->json(['message'=>'Dosya virüslü görünüyor'], 422);
        }

        abort_if(!in_array($f->getMimeType(), ['image/jpeg','image/png','image/webp']), 422, 'Desteklenmeyen format');
        [$w,$h] = @getimagesize($f->getPathname());
        abort_if(!$w || !$h, 422, 'Görsel okunamadı');
        abort_if($w > 8000 || $h > 8000, 422, 'Görsel boyutu çok büyük');

        $dir = "articles/{$a->id}";
        $ext = $f->extension() ?: 'jpg';
        $name = uniqid('img_', true).'.'.$ext;
        $path = $f->storePubliclyAs($dir, $name, ['disk'=>'public','visibility'=>'public']);

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
            /** @var UploadedFile $f */
            abort_if(!in_array($f->getMimeType(), ['image/jpeg','image/png','image/webp']), 422, 'Desteklenmeyen format');
            [$w,$h] = @getimagesize($f->getPathname());
            abort_if(!$w || !$h, 422, 'Görsel okunamadı');
            abort_if($w > 8000 || $h > 8000, 422, 'Görsel boyutu çok büyük');

            $ext = $f->extension() ?: 'jpg';
            $name = uniqid('img_', true).'.'.$ext;
            $path = $f->storePubliclyAs($dir, $name, ['disk'=>'public','visibility'=>'public']);

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

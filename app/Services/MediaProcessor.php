<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaProcessor
{
    public function __construct(
        private ImageManager $img = new ImageManager(new Driver())
    ) {}

    /**
     * @return array{cover:string,thumb:string}
     */
    public function process(string $disk, string $srcPath, string $dstDir, int $coverW = 1600, int $coverH = 900, int $thumbW = 400, int $thumbH = 225): array
    {
        $bytes = Storage::disk($disk)->get($srcPath);

        // Cover
        $cover = $this->img->read($bytes)->orientate()->cover($coverW, $coverH, 'center');
        $coverPath = trim($dstDir,'/').'/cover.webp';
        Storage::disk($disk)->put($coverPath, (string) $cover->toWebp(85));

        // Thumb
        $thumb = $this->img->read($bytes)->orientate()->cover($thumbW, $thumbH, 'center');
        $thumbPath = trim($dstDir,'/').'/thumb.webp';
        Storage::disk($disk)->put($thumbPath, (string) $thumb->toWebp(85));

        return ['cover' => $coverPath, 'thumb' => $thumbPath];
    }
}

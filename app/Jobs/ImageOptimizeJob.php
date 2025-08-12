<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageOptimizeJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $path,
        public string $disk = 'public',
        public bool $keepOriginal = true,
        public array $widths = [1200,800,400]
    ) {}

    public function handle(): void
    {
        $fs = Storage::disk($this->disk);
        if (!$fs->exists($this->path)) return;

        $manager = new ImageManager(['driver' => 'gd']);

        $data = $fs->get($this->path);
        $image = $manager->read($data);

        // WebP
        $webp = $image->toWebp(82);
        $fs->put(self::webpPath($this->path), (string) $webp);

        // Resized variants
        foreach ($this->widths as $w) {
            $resized = $image->scale(width: $w);
            $fs->put(self::variantPath($this->path, $w), (string) $resized->toJpeg(82));
        }

        if (!$this->keepOriginal) {
            $fs->delete($this->path);
        }
    }

    public static function webpPath(string $path): string
    {
        $extPos = strrpos($path, '.');
        return ($extPos !== false ? substr($path,0,$extPos) : $path) . '.webp';
    }

    public static function variantPath(string $path, int $width): string
    {
        $extPos = strrpos($path, '.');
        if ($extPos === false) return "{$path}.w{$width}.jpg";
        $name = substr($path,0,$extPos);
        return "{$name}.w{$width}.jpg";
    }
}

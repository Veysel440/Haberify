<?php

namespace App\Support;

final class CacheKeys
{
    public static function categoriesActive(): string { return 'cat:active'; }
    public static function categorySlug(string $slug): string { return "cat:slug:{$slug}"; }

    public static function tagsActive(): string { return 'tag:active'; }
    public static function tagSlug(string $slug): string { return "tag:slug:{$slug}"; }

    public static function setting(string $key): string { return "setting:{$key}"; }
    public static function menu(string $name): string { return "menu:{$name}"; }
}

<?php

namespace App\Support\Traits;


use Mews\Purifier\Facades\Purifier;

trait SanitizesHtml
{
    protected function sanitizeHtml(?string $html, string $profile='news'): ?string
    {
        if ($html === null) return null;
        return Purifier::clean($html, $profile);
    }
}

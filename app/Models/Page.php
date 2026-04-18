<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\SanitizesHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SanitizesHtml, SoftDeletes;

    protected $fillable = ['title', 'slug', 'body', 'meta', 'status'];

    protected $casts = ['meta' => 'array'];

    public function setBodyAttribute($v): void
    {
        $this->attributes['body'] = $this->sanitizeHtml($v);
    }
}

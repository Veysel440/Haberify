<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class News extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'image', 'category_id',
        'status', 'is_featured', 'views',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'views' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(NewsHistory::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(NewsImage::class);
    }
}

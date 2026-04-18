<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Str;

class Series extends Model
{
    protected $fillable = ['title', 'slug', 'description'];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_series')->withPivot('order');
    }

    public function setSlugAttribute($v)
    {
        $this->attributes['slug'] = $v ?: Str::slug($this->attributes['title'] ?? '');
    }
}

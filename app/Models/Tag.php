<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'is_active'];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }

    public function setSlugAttribute($v)
    {
        $this->attributes['slug'] = $v ?: Str::slug($this->attributes['name'] ?? '');
    }
}

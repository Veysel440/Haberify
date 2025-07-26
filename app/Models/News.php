<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'image', 'category_id',
        'status', 'is_featured', 'views'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tag');
    }
    public function histories()
    {
        return $this->hasMany(NewsHistory::class);
    }
    public function images()
    {
        return $this->hasMany(NewsImage::class);
    }
}

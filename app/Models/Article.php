<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use HasFactory/*, Searchable*/;

    protected $fillable = [
        'author_id','category_id','title','slug','summary','body','cover_path',
        'status','scheduled_at','published_at','meta','reading_time','is_featured','language'
    ];

    protected $casts = [
        'meta' => 'array',
        'is_featured' => 'bool',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    // Relations
    public function author(){ return $this->belongsTo(User::class,'author_id'); }
    public function category(){ return $this->belongsTo(Category::class); }
    public function tags(){ return $this->belongsToMany(Tag::class); }
    public function comments(){ return $this->hasMany(Comment::class); }
    public function series(){ return $this->belongsToMany(Series::class,'article_series')->withPivot('order'); }

    public function scopePublished($q){ return $q->where('status','published'); }
    public function scopeFeatured($q){ return $q->where('is_featured',true); }

    public function setSlugAttribute($v){ $this->attributes['slug'] = $v ?: \Str::slug($this->attributes['title'] ?? ''); }

}

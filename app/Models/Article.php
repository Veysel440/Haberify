<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Article extends Model
{
    use HasFactory, Searchable, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('article')
            ->logOnly([
                'title','slug','summary','body','status','published_at','is_featured','language',
                'author_id','category_id'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relations
    public function author(){ return $this->belongsTo(User::class,'author_id'); }
    public function category(){ return $this->belongsTo(Category::class); }
    public function tags(){ return $this->belongsToMany(Tag::class); }
    public function comments(){ return $this->hasMany(Comment::class); }

    // Scopes
    public function scopePublished($q){ return $q->where('status','published'); }
    public function scopeFeatured($q){ return $q->where('is_featured',true); }

    // Mutator
    public function setSlugAttribute($v){ $this->attributes['slug'] = $v ?: \Str::slug($this->attributes['title'] ?? ''); }

    // Scout
    public function toSearchableArray(): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'summary'      => $this->summary,
            'body'         => strip_tags($this->body),
            'category'     => $this->category?->name,
            'tags'         => $this->tags()->pluck('name')->all(),
            'language'     => $this->language,
            'published_at' => optional($this->published_at)->timestamp,
        ];
    }
}

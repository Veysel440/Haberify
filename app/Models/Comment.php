<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\SanitizesHtml;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory, SanitizesHtml, SoftDeletes;

    protected $fillable = ['article_id', 'user_id', 'guest_name', 'body', 'status', 'ip', 'ua'];

    protected $casts = ['created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }

    public function setBodyAttribute($v): void
    {
        $this->attributes['body'] = strip_tags((string) $v);
    }
}

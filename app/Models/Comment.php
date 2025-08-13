<?php

namespace App\Models;

use App\Support\Traits\SanitizesHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Comment extends Model
{
    use SoftDeletes, SanitizesHtml;

    protected $fillable = ['article_id','user_id','guest_name','body','status','ip','ua'];

    protected $casts = [ 'created_at'=>'datetime', 'updated_at'=>'datetime' ];

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
        return $q->where('status','approved');
    }
    public function setBodyAttribute($v): void
    {
        $this->attributes['body'] = strip_tags((string)$v);
    }
}

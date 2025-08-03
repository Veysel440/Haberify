<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentLikeHistory extends Model
{
    protected $fillable = [
        'comment_id',
        'user_id',
        'is_like',
        'action',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

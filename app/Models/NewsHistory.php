<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsHistory extends Model
{
    protected $fillable = [
        'news_id', 'edited_by', 'title', 'content', 'excerpt', 'slug',
        'image', 'status', 'scheduled_at'
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}

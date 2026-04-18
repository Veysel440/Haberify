<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsImage extends Model
{
    protected $fillable = ['news_id', 'image'];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}

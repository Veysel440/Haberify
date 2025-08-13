<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\Traits\SanitizesHtml;
class Page extends Model
{
    use SoftDeletes, SanitizesHtml;

    protected $fillable = ['title','slug','body','meta','status'];
    protected $casts = ['meta'=>'array'];

    public function setBodyAttribute($v): void
    {
        $this->attributes['body'] = $this->sanitizeHtml($v);
    }
}

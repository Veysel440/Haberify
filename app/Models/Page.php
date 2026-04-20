<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\Traits\SanitizesHtml;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Page extends Model
{
    use LogsActivity, SanitizesHtml, SoftDeletes;

    protected $fillable = ['title', 'slug', 'body', 'meta', 'status'];

    protected $casts = ['meta' => 'array'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('page')
            ->logOnly(['*'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function setBodyAttribute($v): void
    {
        $this->attributes['body'] = $this->sanitizeHtml($v);
    }
}

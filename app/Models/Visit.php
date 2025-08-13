<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'session_id','path','ref','utm_source','utm_medium','utm_campaign','ip','ua','article_id'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'read', 'data'
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
    ];
}

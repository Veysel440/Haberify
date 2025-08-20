<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id','action','target_type','target_id','ip','ua','route','meta'];
    protected $casts = ['meta'=>'array'];
}

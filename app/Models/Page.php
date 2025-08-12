<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title','slug','body','meta','status'];
    protected $casts = ['meta'=>'array'];
}

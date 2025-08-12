<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleView extends Model
{
    public $timestamps = true;
    protected $fillable = ['article_id','session_id','ip','ua'];
}

<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['article_id','user_id','guest_name','body','status','ip','ua'];

    protected $casts = [ 'created_at'=>'datetime', 'updated_at'=>'datetime' ];

    public function article(){ return $this->belongsTo(Article::class); }
    public function user(){ return $this->belongsTo(User::class); }

    public function scopeApproved($q){ return $q->where('status','approved'); }
}

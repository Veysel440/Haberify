<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name','slug','is_active'];

    public function articles(){ return $this->belongsToMany(Article::class); }

    public function setSlugAttribute($v){ $this->attributes['slug'] = $v ?: \Str::slug($this->attributes['name'] ?? ''); }
}

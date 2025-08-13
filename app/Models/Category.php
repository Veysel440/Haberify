<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Category extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','slug','description','parent_id','is_active'];

    public function parent(){ return $this->belongsTo(Category::class,'parent_id'); }
    public function children(){ return $this->hasMany(Category::class,'parent_id'); }
    public function articles(){ return $this->hasMany(Article::class); }

    public function setSlugAttribute($v){ $this->attributes['slug'] = $v ?: \Str::slug($this->attributes['name'] ?? ''); }
}

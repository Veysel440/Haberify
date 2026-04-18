<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuthorProfile extends Model
{
    protected $fillable = ['user_id', 'bio', 'social_links'];

    protected $casts = ['social_links' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

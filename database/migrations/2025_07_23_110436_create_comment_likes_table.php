<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('comments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_like'); // true: like, false: dislike
            $table->timestamps();
            $table->unique(['comment_id', 'user_id']);
        });
    }
    public function down()
    {
        Schema::dropIfExists('comment_likes');
    }
};

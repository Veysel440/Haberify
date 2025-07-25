<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->onDelete('cascade');
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->text('content');
            $table->string('excerpt', 300)->nullable();
            $table->string('slug');
            $table->string('image')->nullable();
            $table->enum('status', ['draft', 'published', 'scheduled'])->default('published');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('news_histories');
    }
};

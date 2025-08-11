<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('category_id')->constrained()->cascadeOnDelete();

            $t->string('title');
            $t->string('slug')->unique();
            $t->string('summary', 500)->nullable();
            $t->longText('body');
            $t->string('cover_path')->nullable();

            $t->enum('status', ['draft','review','published','scheduled','archived'])
                ->default('draft')->index();

            $t->timestamp('scheduled_at')->nullable()->index();
            $t->timestamp('published_at')->nullable()->index();

            $t->json('meta')->nullable();
            $t->unsignedSmallInteger('reading_time')->default(0);
            $t->boolean('is_featured')->default(false)->index();
            $t->string('language', 8)->default('tr');

            $t->timestamps();
            $t->softDeletes();

            $t->index(['status', 'published_at'], 'articles_status_published_idx');

            $t->fullText(['title', 'summary', 'body']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};

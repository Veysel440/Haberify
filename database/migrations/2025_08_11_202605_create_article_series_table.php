<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_series', function (Blueprint $t) {
            $t->foreignId('article_id')->constrained()->cascadeOnDelete();
            $t->foreignId('series_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('order')->default(0);
            $t->primary(['article_id','series_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_series');
    }
};

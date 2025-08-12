<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_view_daily', function (Blueprint $t) {
            $t->date('day');
            $t->foreignId('article_id')->constrained()->cascadeOnDelete();
            $t->unsignedInteger('views')->default(0);
            $t->primary(['day','article_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_view_daily');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $t) {
            $t->id();
            $t->string('session_id', 64)->nullable();
            $t->string('path', 255);
            $t->string('ref', 255)->nullable();
            $t->string('utm_source', 100)->nullable();
            $t->string('utm_medium', 100)->nullable();
            $t->string('utm_campaign', 120)->nullable();
            $t->string('ip', 45)->nullable();
            $t->string('ua', 255)->nullable();
            $t->unsignedBigInteger('article_id')->nullable();
            $t->timestamps();

            $t->index(['created_at','ref']);
            $t->index(['article_id','created_at']);
            $t->index(['path']);
        });
    }
    public function down(): void { Schema::dropIfExists('visits'); }
};

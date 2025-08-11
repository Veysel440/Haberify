<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $t) {
            $t->id();
            $t->string('session_id')->index();
            $t->ipAddress('ip')->nullable();
            $t->string('ua',255)->nullable();
            $t->string('ref',255)->nullable();
            $t->string('landed_path',255)->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};

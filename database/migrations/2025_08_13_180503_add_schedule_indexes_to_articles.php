<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('articles', function (Blueprint $t) {
            if (!Schema::hasColumn('articles','scheduled_at')) {
                $t->timestamp('scheduled_at')->nullable()->after('status');
            }
            $t->index(['status','scheduled_at'], 'articles_status_schedule_idx');
        });
    }
    public function down(): void {
        Schema::table('articles', function (Blueprint $t) {
            $t->dropIndex('articles_status_schedule_idx');
        });
    }
};

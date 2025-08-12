<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->boolean('is_comment_banned')->default(false)->after('remember_token');
            $t->timestamp('comment_banned_until')->nullable()->after('is_comment_banned');
            $t->string('comment_ban_reason', 255)->nullable()->after('comment_banned_until');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['is_comment_banned','comment_banned_until','comment_ban_reason']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('articles', function (Blueprint $t) {
            $t->index(['language','status','published_at'], 'art_lang_status_pub_idx');
            $t->index(['category_id','status','published_at'], 'art_cat_status_pub_idx');
        });
        Schema::table('article_view_daily', function (Blueprint $t) {
            $t->index(['day','article_id'], 'avd_day_art_idx');
        });
        Schema::table('visits', function (Blueprint $t) {
            $t->index(['ref','created_at'], 'vis_ref_created_idx');
        });
        Schema::table('comments', function (Blueprint $t) {
            $t->index(['article_id','status','created_at'], 'com_art_stat_created_idx');
        });
    }
    public function down(): void {
        Schema::table('articles', function (Blueprint $t) {
            $t->dropIndex('art_lang_status_pub_idx');
            $t->dropIndex('art_cat_status_pub_idx');
        });
        Schema::table('visits', function (Blueprint $t) {
            $t->dropIndex('vis_ref_created_idx');
        });
        Schema::table('comments', function (Blueprint $t) {
            $t->dropIndex('com_art_stat_created_idx');
        });
    }
};

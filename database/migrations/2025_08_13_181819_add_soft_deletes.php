<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        foreach (['articles','comments','categories','tags','pages'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                if (!Schema::hasColumn($t->getTable(),'deleted_at')) {
                    $t->softDeletes();
                }
            });
        }
    }
    public function down(): void {
        foreach (['articles','comments','categories','tags','pages'] as $table) {
            Schema::table($table, function (Blueprint $t) { $t->dropSoftDeletes(); });
        }
    }
};

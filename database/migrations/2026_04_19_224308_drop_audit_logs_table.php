<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Removes the legacy `audit_logs` table.
 *
 * This table was created by `2025_08_20_112458_create_audit_logs` and
 * backed a parallel, home-grown audit pipeline:
 *
 *   App\Http\Middleware\AuditMutation      (never registered in any route)
 *   App\Services\AuditLogger               (only ever called from that middleware)
 *   App\Jobs\WriteAuditLog                 (queued writer)
 *   App\Models\AuditLog                    (Eloquent model)
 *
 * Zero production code read from this table — `AuditController` reads the
 * Spatie `activity_log` table, not this one. The writer was dead code; the
 * reader pointed somewhere else. spatie/laravel-activitylog is now the
 * single source of truth for the audit trail, so the legacy table is no
 * longer meaningful and is dropped to avoid schema drift.
 *
 * `down()` intentionally has no body. Rolling this migration back cannot
 * restore the lost rows (there were none, because no writer ever ran), and
 * recreating an empty table would only resurrect the same dead-code path.
 * If you genuinely need a native audit table again, create a fresh, purpose-
 * built migration rather than reversing this one.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('audit_logs');
    }

    public function down(): void
    {
        // Intentionally irreversible — see class docblock.
    }
};

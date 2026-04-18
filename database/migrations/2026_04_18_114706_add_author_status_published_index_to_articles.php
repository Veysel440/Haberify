<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Composite index on (author_id, status, published_at) for the articles table.
 *
 * Why this composite (left-to-right prefix matters):
 *   1. author_id   — equality predicate (per-author dashboards, public author profile pages)
 *   2. status      — equality predicate ('published', 'draft', ...)
 *   3. published_at — range / ORDER BY predicate (newest-first listings)
 *
 * Targeted query patterns this index serves:
 *   - "Latest published articles by author X"
 *       SELECT * FROM articles
 *        WHERE author_id = ? AND status = 'published'
 *        ORDER BY published_at DESC
 *
 *   - "All articles authored by X" (uses leftmost prefix only)
 *       SELECT * FROM articles WHERE author_id = ?
 *
 *   - "Author X's draft queue"
 *       SELECT * FROM articles
 *        WHERE author_id = ? AND status = 'draft'
 *        ORDER BY published_at DESC
 *
 * The existing single-column `author_id` FK index would force a filesort + extra
 * filter pass for the status/published_at predicates; this composite collapses
 * the whole access path into a single index range scan.
 *
 * It complements (does NOT replace) `articles_status_published_idx` which serves
 * the global "all published articles" feed where author is unbounded.
 */
return new class extends Migration
{
    private const INDEX_NAME = 'articles_author_status_published_idx';

    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->index(
                ['author_id', 'status', 'published_at'],
                self::INDEX_NAME,
            );
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table): void {
            $table->dropIndex(self::INDEX_NAME);
        });
    }
};

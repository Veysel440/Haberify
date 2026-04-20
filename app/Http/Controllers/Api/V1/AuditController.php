<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Audit\AuditIndexRequest;
use App\Http\Resources\Api\V1\AuditLogEntryResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

/**
 * Audit trail read-only controller.
 *
 * Reads from the `activity_log` table that spatie/laravel-activitylog
 * populates. Writes happen implicitly through each model's `LogsActivity`
 * trait — nothing to dispatch from this controller.
 *
 * Route: `GET /api/v1/audit` (auth:sanctum + permission:analytics.view).
 *
 * Query-string parameters (see `AuditIndexRequest` for exact validation):
 *   log_name   — model-specific channel, e.g. `article`, `user`, `setting`
 *   causer_id  — integer user id
 *   from       — `YYYY-MM-DD` / ISO 8601 (inclusive, 00:00:00)
 *   to         — `YYYY-MM-DD` / ISO 8601 (inclusive, 23:59:59)
 *   per_page   — 1..100 (default 50)
 *   page       — standard Laravel pagination
 */
class AuditController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'permission:analytics.view']);
    }

    public function index(AuditIndexRequest $request): ApiResponse
    {
        $logs = $this->buildQuery($request)
            ->latest('id')
            ->paginate($request->perPage())
            ->appends($request->validated());

        return ApiResponse::ok(
            AuditLogEntryResource::collection($logs)->resolve(),
            meta: [
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                    'last_page' => $logs->lastPage(),
                ],
                'filters' => array_filter([
                    'log_name' => $request->logName(),
                    'causer_id' => $request->causerId(),
                    'from' => $request->from(),
                    'to' => $request->to(),
                ], static fn ($v): bool => $v !== null),
            ],
        );
    }

    /**
     * @return Builder<Activity>
     */
    private function buildQuery(AuditIndexRequest $request): Builder
    {
        return Activity::query()
            ->when(
                $request->logName(),
                static fn (Builder $q, string $name): Builder => $q->where('log_name', $name),
            )
            ->when(
                $request->causerId(),
                static fn (Builder $q, int $id): Builder => $q->where('causer_id', $id),
            )
            ->when(
                $request->from(),
                static fn (Builder $q, string $from): Builder => $q->where('created_at', '>=', $from . ' 00:00:00'),
            )
            ->when(
                $request->to(),
                static fn (Builder $q, string $to): Builder => $q->where('created_at', '<=', $to . ' 23:59:59'),
            );
    }
}

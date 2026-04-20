<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Activitylog\Models\Activity;

/**
 * Shape of a single `activity_log` row served via `GET /api/v1/audit`.
 *
 * Explicit allowlist — mirrors `UserResource` in spirit: the underlying
 * `Activity` Eloquent model can grow extra columns; we do not leak them
 * automatically. `properties` may carry arbitrary payloads (already
 * scrubbed by each model's `logExcept(...)`), so we surface it as-is.
 *
 * @property-read Activity $resource
 */
class AuditLogEntryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Activity $activity */
        $activity = $this->resource;

        return [
            'id' => $activity->getKey(),
            'log_name' => $activity->log_name,
            'event' => $activity->event,
            'description' => $activity->description,
            'subject' => $activity->subject_type === null ? null : [
                'type' => class_basename((string) $activity->subject_type),
                'id' => $activity->subject_id,
            ],
            'causer' => $activity->causer_type === null ? null : [
                'type' => class_basename((string) $activity->causer_type),
                'id' => $activity->causer_id,
            ],
            'properties' => $activity->properties,
            'batch_uuid' => $activity->batch_uuid,
            'created_at' => $activity->created_at,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use LogsActivity;

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    protected $casts = ['value' => 'array'];

    /**
     * Settings routinely hold credentials: API keys, webhook secrets,
     * third-party tokens, payment provider keys. We therefore EXCLUDE
     * the `value` column from the audit trail entirely.
     *
     * What still gets captured:
     *   - which `key` was created / updated / deleted
     *   - who (causer) did it
     *   - when (created_at / updated_at on the activity row)
     *
     * What is intentionally NOT captured:
     *   - the actual setting value (old or new)
     *
     * The live value is always available in the `settings` table if a
     * legitimate operator needs to inspect it; the audit trail stays
     * safe to export to cold storage / SIEM without any secret exposure.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('setting')
            ->logOnly(['*'])
            ->logExcept(['value'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

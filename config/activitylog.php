<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| spatie/laravel-activitylog — project-specific configuration
|--------------------------------------------------------------------------
|
| Denetim izinin tek kaynağıdır (native audit pipeline `audit_logs` kaldırıldı).
| Herhangi bir modeli izlemek için `LogsActivity` trait'i + `getActivitylogOptions()`
| kullanılır; log satırları `activity_log` tablosuna yazılır ve `AuditController::index`
| buradan okur.
|
| Saklama süresi (`delete_records_older_than_days`) `activitylog:clean` artisan
| komutu için geçerlidir — bu komut scheduler'a bağlandığında eski kayıtlar
| otomatik temizlenir.
*/

return [

    /*
     * If set to false, no activities will be saved to the database.
     */
    'enabled' => (bool) env('ACTIVITY_LOGGER_ENABLED', true),

    /*
     * When the clean-command is executed, all recording activities older than
     * the number of days specified here will be deleted.
     *
     * Haberify policy: 730 days (2 years). Uyumluluk / adli analiz ihtiyaçları
     * için yıllık raporların tamamlanmasına izin verir, ancak tablonun sonsuza
     * kadar büyümesini engeller. Ortam bazlı override için
     * `ACTIVITY_LOGGER_RETENTION_DAYS` env değişkeni kullanılabilir.
     */
    'delete_records_older_than_days' => (int) env('ACTIVITY_LOGGER_RETENTION_DAYS', 730),

    /*
     * If no log name is passed to the activity() helper
     * we use this default log name.
     *
     * Haberify'a özel değer: modellerde `useLogName(...)` atanmadığında
     * log_name kolonu 'haberify' olur; genel log bucket'ını kendi namespace'imize
     * sabitler.
     */
    'default_log_name' => env('ACTIVITY_LOGGER_DEFAULT_LOG_NAME', 'haberify'),

    /*
     * You can specify an auth driver here that gets user models.
     * If this is null we'll use the current Laravel auth driver.
     */
    'default_auth_driver' => null,

    /*
     * If set to true, the subject returns soft deleted models.
     */
    'subject_returns_soft_deleted_models' => false,

    /*
     * This model will be used to log activity.
     * It should implement the Spatie\Activitylog\Contracts\Activity interface
     * and extend Illuminate\Database\Eloquent\Model.
     */
    'activity_model' => \Spatie\Activitylog\Models\Activity::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Activity model shipped with this package.
     */
    'table_name' => env('ACTIVITY_LOGGER_TABLE_NAME', 'activity_log'),

    /*
     * This is the database connection that will be used by the migration and
     * the Activity model shipped with this package. In case it's not set
     * Laravel's database.default will be used instead.
     */
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),
];

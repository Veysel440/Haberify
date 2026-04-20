<?php

declare(strict_types=1);

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is utilized to write
    | messages to your logs. The value provided here should match one of
    | the channels present in the list of "channels" configured below.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Laravel
    | utilizes the Monolog PHP logging library, which includes a variety
    | of powerful log handlers and formatters that you're free to use.
    |
    | Available drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog", "custom", "stack"
    |
    */

    'channels' => [

        /*
         * Application log stack.
         *
         * Kanallar `.env`'deki `LOG_STACK` ile virgülle ayrılmış olarak tanımlanır.
         * Örnek üretim değeri: `LOG_STACK=daily,sentry` — rotated dosya logları
         * + Sentry'ye kritik uyarıların aggregation'ı.
         *
         * `audit` ve `security` **bilinçli olarak bu stack'in dışındadır**:
         * debug gürültüsüyle karışmaları istenmez; ilgili kod yolları doğrudan
         * `Log::channel('audit')->...` / `Log::channel('security')->...` ile yazar.
         */
        'stack' => [
            'driver' => 'stack',
            'channels' => array_values(array_filter(array_map(
                'trim',
                explode(',', (string) env('LOG_STACK', 'single')),
            ))),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://' . env('PAPERTRAIL_URL') . ':' . env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        /*
         * Audit — her türlü domain event iz bırakma için (spatie/activitylog
         * zaten veri tabanında kalıcı iz tutar; bu kanal complementary bir
         * düz-dosya backup'tır: DB çökerse, SIEM'e aktarma yapılırken veya
         * denetim talep edildiğinde hızlı grep için).
         *
         * 730 gün retention = projenin 2 yıllık uyumluluk penceresiyle
         * hizalıdır. `LOG_AUDIT_DAYS` ile ortam bazlı override edilebilir.
         */
        'audit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/audit.log'),
            'level' => env('LOG_AUDIT_LEVEL', 'info'),
            'days' => (int) env('LOG_AUDIT_DAYS', 730),
            'replace_placeholders' => true,
        ],

        /*
         * Security — 2FA başarısızlıkları, şifre sıfırlama, hesap kilitleme,
         * yetkilendirme ihlali denemeleri gibi kritik güvenlik olayları için.
         *
         * Bu kanala yazan her satır bir olay-müfettişi tarafından
         * sorgulanabilir varsayılır; mümkün olduğunca structured context ile
         * yazın (user_id, ip, route, reason). Varsayılan retention 365 gün;
         * `LOG_SECURITY_DAYS` ile override.
         */
        'security' => [
            'driver' => 'daily',
            'path' => storage_path('logs/security.log'),
            'level' => env('LOG_SECURITY_LEVEL', 'notice'),
            'days' => (int) env('LOG_SECURITY_DAYS', 365),
            'replace_placeholders' => true,
        ],

        /*
         * Sentry — `sentry/sentry-laravel` paketi tarafından sağlanan driver.
         * `LOG_STACK=daily,sentry` gibi bir ayar bu kanalı application stack'ine
         * dahil eder; bunun dışında `Log::channel('sentry')->error(...)` ile
         * doğrudan da yazılabilir.
         *
         * `LOG_LEVEL_SENTRY`'yi `error` veya `warning` seviyesinde tutmak
         * önerilir — `debug`/`info` Sentry kotasını hızla tüketir.
         */
        'sentry' => [
            'driver' => 'sentry',
            'level' => env('LOG_LEVEL_SENTRY', 'error'),
            'bubble' => true,
            'report_exceptions' => true,
        ],

    ],

];

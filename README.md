<h1 align="center">Haberify</h1>

<p align="center">
  <strong>Modern, güvenlik odaklı, çok katmanlı bir haber yönetim platformu.</strong><br/>
  <em>Laravel 12 REST API · Next.js 15 Admin Panel · MySQL · Meilisearch · Sanctum · TOTP 2FA</em>
</p>

<p align="center">
  <img alt="PHP" src="https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white">
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white">
  <img alt="Next.js" src="https://img.shields.io/badge/Next.js-15.4-000000?logo=next.js&logoColor=white">
  <img alt="React" src="https://img.shields.io/badge/React-19-61DAFB?logo=react&logoColor=black">
  <img alt="TypeScript" src="https://img.shields.io/badge/TypeScript-5-3178C6?logo=typescript&logoColor=white">
  <img alt="Tailwind" src="https://img.shields.io/badge/TailwindCSS-4-06B6D4?logo=tailwindcss&logoColor=white">
  <img alt="PHPStan" src="https://img.shields.io/badge/PHPStan-Level%206-8892BF">
  <img alt="Pint" src="https://img.shields.io/badge/Pint-PSR--12-F05340">
  <img alt="Pest" src="https://img.shields.io/badge/Pest-3.8-8A2BE2">
</p>

---

## İçindekiler

1. [Proje Kimliği](#1-proje-kimliği)
2. [Mimari](#2-mimari)
3. [Kalite Standartları](#3-kalite-standartları)
4. [Güvenlik](#4-güvenlik)
5. [Performans](#5-performans)
6. [Kurulum](#6-kurulum)
7. [Dizin Yapısı](#7-dizin-yapısı)
8. [API Yüzeyi](#8-api-yüzeyi)
9. [Test Altyapısı](#9-test-altyapısı)
10. [Artisan Komutları ve Zamanlanmış İşler](#10-artisan-komutları-ve-zamanlanmış-i̇şler)
11. [Geliştirici Akışı](#11-geliştirici-akışı)

---

## 1. Proje Kimliği

Haberify; içerik üretimi, editöryal süreçler, moderasyon, analitik ve arama yetenekleri sunan **API-first** bir haber platformudur. Sistem iki bağımsız dağıtım birimiyle çalışır:

- **`/` (Laravel 12 API)** — iş mantığının tamamını, kimlik doğrulamayı, yetkilendirmeyi, içerik depolamasını ve arama/analitik entegrasyonlarını barındıran REST API.
- **`/haberify-admin` (Next.js 15 Admin Panel)** — editörler, yazarlar ve yöneticiler için TypeScript + React 19 tabanlı panel. Aynı API’yi tüketir.

Bu ayrım, API’nin aynı anda public web sitesi, admin paneli, mobil istemciler ve üçüncü taraf entegrasyonları tarafından tutarlı biçimde kullanılmasını sağlar.

### Teknoloji Yığını

| Katman | Teknoloji | Sürüm |
|---|---|---|
| Arka uç çatısı | **Laravel** | **12.x** (PHP **8.4**) |
| API kimlik doğrulama | Laravel Sanctum + `pragmarx/google2fa` (TOTP) | 4.x / 8.x |
| Yetkilendirme | `spatie/laravel-permission` (rol + izin) | 6.x |
| Audit / denetim | `spatie/laravel-activitylog` | 4.10 |
| HTML temizleme | `mews/purifier` + özel `HtmlSanitizer` | 3.4 |
| Veri tabanı | MySQL 8 (`utf8mb4`) | — |
| Tam metin arama | Laravel Scout + Meilisearch | 10.17 / 1.15 |
| Medya işleme | `intervention/image` | 3.8 |
| Raporlama (Excel/CSV) | `maatwebsite/excel` | 3.1 |
| Gözlemlenebilirlik | Sentry | 4.15 |
| API dokümantasyonu | `darkaonline/l5-swagger` (OpenAPI 3) | 9.x |
| Admin paneli | Next.js + React + TypeScript | 15.4.6 / 19 / 5 |
| Admin veri katmanı | TanStack Query · Jotai · React Hook Form · Zod | güncel |
| Grafikler | Recharts | 3.1 |
| Stil | Tailwind CSS | 4.x |
| Blade varlık boru hattı | Vite + `laravel-vite-plugin` | 6.x / 1.2 |
| Test koşucusu | Pest + PHPUnit | 3.8 / 11.5 |
| Statik analiz | Larastan + PHPStan | 3.x / 2.1 (seviye 6) |
| Kod biçimlendirme | Laravel Pint | 1.13 |

---

## 2. Mimari

### 2.1 Katmanlı Yapı

Kod; katmanlı mimari (Layered Architecture) + **SOLID** + **Clean Code** ilkeleriyle düzenlenmiştir. Her sınıfın tek bir değişme sebebi olur (SRP), soyutlamalar somut sınıflara değil sözleşmelere dayanır (DIP):

```
┌─────────────────────────────────────────────────────────────┐
│  HTTP Katmanı — routes/api.php · FormRequest · Middleware   │
│  Controller  (App\Http\Controllers\Api\V1\...)              │
│     → yalnızca çeviri: Request → Service → Response         │
├─────────────────────────────────────────────────────────────┤
│  Service Layer  (App\Services\...)                          │
│     iş kuralları · orkestrasyon · DTO’lar · olaylar         │
├─────────────────────────────────────────────────────────────┤
│  Repository Sözleşmeleri  (App\Contracts\...)               │
│  Eloquent Uygulamaları    (App\Repositories\...)            │
├─────────────────────────────────────────────────────────────┤
│  Model / Eloquent ORM · Policy · Event · Listener · Job     │
└─────────────────────────────────────────────────────────────┘
```

### 2.2 Service Layer Örneği — `TwoFactorService`

İki faktörlü kimlik doğrulama akışı, **SRP** için referans olarak ele alınmıştır. Tek bir “tanrı sınıfı” yerine, her biri tek sorumluluk taşıyan küçük işbirlikçiler vardır. Controller (`TwoFactorController`) yalnızca 141 satırlık saf bir cephe (plumbing) olarak kalır; bütün kurallar, yan etkiler ve kriptografik işlemler aşağıdaki servislerde yaşar:

| Sınıf | Sorumluluk | Değişme Sebebi |
|---|---|---|
| `TwoFactorService` | Bütün akışın orkestratörü (facade). | Akış adımları değiştiğinde. |
| `TwoFactorSecretManager` | TOTP gizli anahtarının üretimi, **şifrelenmesi**, recovery kodları. | Depolama/şifreleme stratejisi değiştiğinde. |
| `TwoFactorRateLimiter` | Login ve challenge aşamaları için oran sınırı (rate limit). | Eşikler/stratejiler değiştiğinde. |
| `TwoFactorChallengeTokenizer` | Geçici challenge token üretimi, TTL kontrolü, çözme. | Token formatı/TTL değiştiğinde. |
| `Auth\SanctumTokenIssuer` | Rol → Sanctum ability eşlemesi (tek otorite). | Yetki modeli değiştiğinde. |

Sonuçlar **sealed-style DTO**’lar ile temsil edilir (`LoginAttemptResult`, `TwoFactorChallengeResult`, `TwoFactorEnrollmentData`). Controller bu sonuçları bir `match` ifadesiyle HTTP yanıtına çevirir:

```php
return match ($result->status) {
    LoginAttemptResult::STATUS_RATE_LIMITED       => ApiResponse::error('auth.rate_limited', 429),
    LoginAttemptResult::STATUS_INVALID_CREDENTIALS=> ApiResponse::error('auth.invalid_credentials', 422),
    LoginAttemptResult::STATUS_REQUIRES_TWO_FACTOR=> ApiResponse::ok(['requires_2fa' => true, 'tmp' => $result->challengeToken]),
    LoginAttemptResult::STATUS_AUTHENTICATED      => ApiResponse::ok(['token' => $result->token]),
};
```

Bu yapıyla yeni bir 2FA kanalı (SMS, WebAuthn, Push) eklemek istendiğinde mevcut kod değiştirilmeden yeni bir işbirlikçi enjekte edilir (**OCP**).

### 2.3 Diğer Servisler

| Servis | İşlev |
|---|---|
| `ArticleService` | Makale CRUD, yayın akışı, Scout indeksleme, front-end ISR revalidate tetikleyicisi, okuma süresi tahmini. |
| `Admin\ArticleBulkService` | Yığın işlemler (toplu yayınlama, arşivleme, silme) — ayrı throttle altında. |
| `CategoryService`, `TagService` | Taksonomi yönetimi + slug üretimi. |
| `CommentService` | Yorum kaydı, HTMLPurifier sanitizasyonu, moderasyon olayları. |
| `AnalyticsService` | Overview / top-articles / referrers / category-share metrikleri. |
| `MediaProcessor` | Medya optimizasyonu, `AntivirusScanner` entegrasyonu. |
| `AntivirusScanner`, `VirusScanner` | ClamAV tarzı opsiyonel antivirüs taraması. |
| `RssFeedService`, `SitemapService` | RSS ve sitemap üretimi (cache’li). |
| `VisitService` | Makale/site ziyaret takibi, günlük rollup. |

### 2.4 HTTP Boru Hattı

`bootstrap/app.php` Laravel 12’nin yeni konfigürasyon stilini kullanır:

- `withRouting(api: routes/api.php, apiPrefix: 'api', health: '/up')`
- `statefulApi()` — Sanctum SPA oturum desteği (Next.js admin için).
- Alias middleware’ları: `permission`, `role`, `role_or_permission` (Spatie), `comment.notbanned`.
- `App\Http\Middleware`:
  - `SecurityHeaders` — CSP, `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Permissions-Policy`.
  - `ThrottlePerRole` — rol bazlı oran sınırı: `admin=600/dk`, `editor=300/dk`, `author=200/dk`, `guest=60/dk`.
  - `ForceJsonResponse`, `ValidateJson` — tüm API yanıtları JSON.
  - `SetLocaleFromHeader` — `X-Locale: tr|en` başlığı.
  - `CacheHeadersMiddleware` + `ETagMiddleware` — koşullu `If-None-Match` / 304.
  - `SentryContext`, `RequestIdMiddleware` — izlenebilirlik.
  - `TrackArticleView`, `TrackReferrer` — analitik.

> **Denetim izi** artık yalnızca `spatie/laravel-activitylog` üzerinden tutulur. İlgili modellere `LogsActivity` trait'i eklenerek değişiklikler `activity_log` tablosuna yazılır; `GET /api/v1/audit` ucu bu tabloyu okur.

### 2.5 Servis Sağlayıcıları

`bootstrap/providers.php` yalnızca şu sağlayıcıları yükler:

- `AppServiceProvider` — repository ↔ Eloquent bağlamaları, `Google2FA` singleton, `Queue::failing` logger, **`Password::defaults()`** konfigürasyonu.
- `AuthServiceProvider` — Gate policy’leri (`ArticlePolicy`, `CategoryPolicy`, `TagPolicy`, `CommentPolicy`, `PagePolicy`, `UserPolicy`, `SettingPolicy`, `MenuPolicy`), `Gate::before` admin bypass.
- `EventServiceProvider` — domain olaylarının listener eşleşmeleri.
- `RateLimitServiceProvider` — `login`, `register`, `twofactor`, `comment-create`, `search`, `media-upload`, `admin-bulk`, `password-reset`, `sessions-list`.

---

## 3. Kalite Standartları

Haberify, **“baseline yalnızca küçülür, hiç büyümez”** politikasını uygular. Yeni kod her iki kapıdan (lint + static analysis) temiz geçmek zorundadır.

### 3.1 Laravel Pint — PSR-12 + Laravel Preset

`pint.json` zorunlu kuralları:

- **PSR-12** temel alınır + Laravel preset.
- `declare(strict_types=1);` her dosyada zorunludur.
- `single_quote` — çift tırnak yerine tek tırnak.
- `ordered_imports` — `alpha` algoritmasıyla sınıf → fonksiyon → sabit sırası.
- `no_unused_imports`, `short_ternary`, `concat_space: one`, `blank_line_before_statement`.
- `trailing_comma_in_multiline` — dizi, argüman ve parametrelerde sondaki virgül.
- `vendor/`, `storage/`, `database/migrations/`, `haberify-admin/` taramadan muaftır.

```bash
composer lint         # kontrol
composer lint:fix     # yerinde düzelt
```

### 3.2 Larastan / PHPStan — Seviye 6

`phpstan.neon`:

- Genişletme: `larastan/larastan` (Eloquent, container, facade analizi).
- **Seviye: 6**.
- Taranan yollar: `app`, `config`, `database/factories`, `database/seeders`, `routes`, `tests`.
- Baseline: `phpstan-baseline.neon` (318 kayıt; çalışma başlangıcında 393 idi).
- 4 paralel süreç, 300 sn işlem zaman aşımı.

```bash
composer analyse              # analiz
composer analyse:baseline     # baseline yenile
composer qa                   # lint + analyse (CI kapısı)
```

### 3.3 Sürekli Entegrasyon

`.github/workflows/qa.yml`; `push` ve `pull_request` tetikleyicilerinde Pint ve PHPStan işlerini paralel çalıştırır. Composer ve PHPStan cache’leri GitHub Actions seviyesinde saklanır.

---

## 4. Güvenlik

Sistem **“sunucu tarafında son söz”** ilkesiyle tasarlanmıştır: istemciden gelen hiçbir iddia, sunucu doğrulaması olmadan etkili sayılmaz.

### 4.1 İki Faktörlü Kimlik Doğrulama (TOTP 2FA)

- RFC 6238 uyumlu TOTP (`pragmarx/google2fa`).
- Login akışı iki adımlıdır:
  1. `POST /api/v1/auth/login` → e-posta + şifre doğrulanır; kullanıcı 2FA kayıtlıysa **geçici challenge token** döner.
  2. `POST /api/v1/auth/2fa/verify` → challenge token + 6 haneli kod doğrulanır; geçerli ise kalıcı Sanctum API token verilir.
- Challenge token, `APP_KEY` ile **şifrelenmiş** ve TTL gömülü (varsayılan 300 sn) bir yapıdır; istemcide doğrulanabilir iddia tutulmaz.
- **Recovery kodları** yalnızca enrolment yanıtında plaintext döner; veri tabanında `Illuminate\Contracts\Encryption\Encrypter` üzerinden şifrelenmiş JSON olarak saklanır.
- Her iki adımın bağımsız rate limiter’ı vardır (`config/twofactor.php → rate_limit.login` / `rate_limit.challenge`).

### 4.2 Kimlik Doğrulama ve Yetkilendirme Middleware’leri

- **Laravel Sanctum** — `auth:sanctum` ile korunan tüm uç noktalar Bearer token veya SPA çerezi üzerinden kimlik doğrular.
- **Spatie Permission** — `permission:articles.update`, `permission:comments.moderate` vb. route-alias middleware’larıyla fine-grained yetkilendirme.
- `Gate::before` yalnızca `admin` rolüne koşulsuz yetki verir; diğer tüm roller izin tabanlı çalışır.
- `ThrottlePerRole` — role göre oran sınırı (admin=600/dk → guest=60/dk); brute-force ve scraping saldırılarını azaltır.
- `EnsureNotCommentBanned` — yorum yazan kullanıcının IP/kullanıcı bazlı ban durumunu kontrol eder.
- `ValidateJson` + `ForceJsonResponse` — hatalı `Content-Type` erken reddedilir.

### 4.3 Şifre Politikası

`config/security.php` tek kaynak prensibi (single source of truth):

| Kural | Varsayılan | `.env` anahtarı |
|---|---|---|
| Minimum uzunluk | **12** | `PASSWORD_MIN_LENGTH` |
| Büyük/küçük harf | zorunlu | `PASSWORD_REQUIRE_MIXED_CASE` |
| Rakam | zorunlu | `PASSWORD_REQUIRE_NUMBERS` |
| Sembol | zorunlu | `PASSWORD_REQUIRE_SYMBOLS` |
| HIBP (breach) kontrolü | açık | `PASSWORD_UNCOMPROMISED_CHECK` |

OWASP ASVS 4.0 §2.1 ve NIST SP 800-63B §5.1.1.2 rehberleriyle uyumludur. `Password::defaults()` callback’i `AppServiceProvider::boot` içinde kayıtlıdır; register, reset ve admin tarafından yapılan tüm şifre yazımları aynı kurala uyar. Hashleme `Hash::make` ile yapılır (Bcrypt 12 tur varsayılan).

### 4.4 İçerik Güvenliği

- **HTMLPurifier** + özel `App\Support\HtmlSanitizer` — yorum ve makale gövdelerini beyaz listeli tag seti ve şema kısıtlı URI’larla (`http`, `https`, `mailto`, `tel`) temizler. `javascript:`, `data:` URI’ları kökten reddedilir.
- **Security Headers** middleware’i her yanıta ekler:
  - `Content-Security-Policy: default-src 'self'; ...`
  - `X-Frame-Options: DENY`
  - `Referrer-Policy: no-referrer-when-downgrade`
  - `Permissions-Policy: geolocation=(), microphone=(), camera=()`
- **Antivirus/VirusScanner** — yüklenen medya için opsiyonel ClamAV taraması (`config/virus.php`).
- **Audit Log** — kritik mutasyonlar (yayın, silme, rol değişikliği) `spatie/laravel-activitylog` üzerinden `activity_log` tablosuna yazılır; izlenecek model `LogsActivity` trait'ini uygular ve `getActivitylogOptions()` ile allowlist sağlar.

### 4.5 Oturum / Token Hijyeni

- Şifre sıfırlandığında kullanıcının tüm Sanctum token’ları iptal edilir (`PasswordResetController::reset`).
- Logout yalnızca o oturumun token’ını iptal eder; diğer cihaz oturumları korunur.
- `personal_access_tokens` tablosu token hash’lerini SHA-256 ile saklar; plaintext token asla loglanmaz.

### 4.6 Oran Sınırı (Rate Limiting)

`RateLimitServiceProvider` tüm limiter’ları tek noktadan tanımlar:

| Limiter | Kural |
|---|---|
| `register` | 3/dk + 10/saat (IP bazlı) |
| `login` | 5/dk (e-posta+IP) + 20/dk (IP) |
| `twofactor` | 6/dk (e-posta+IP) |
| `password-reset` | 5/dk |
| `comment-create` | 3/dk (kullanıcı+IP) + 10/dk (IP) |
| `search` | 30/dk |
| `media-upload` | 6/dk |
| `admin-bulk` | 3/dk |

---

## 5. Performans

### 5.1 Veri Tabanı — Composite Indexing

`articles` tablosu, yazma maliyetini artırmadan okuma sorgularını tek bir index range scan’e indirgeyen hedefli composite index’lerle donatılmıştır:

| Index | Kolonlar | Amaç |
|---|---|---|
| `articles_status_published_idx` | `(status, published_at)` | Global “yayındaki makaleler, en yeniden eskiye” feed. |
| `articles_status_schedule_idx` | `(status, scheduled_at)` | Zamanlanmış yayın kuyruğu. |
| `articles_author_status_published_idx` | `(author_id, status, published_at)` | Yazar dashboard’u / public yazar profili — **equality → equality → range/ORDER BY** sıralaması ile leftmost-prefix kuralını tam anlamıyla kullanır. |
| `articles_title_summary_body_fulltext` | `FULLTEXT(title, summary, body)` | Meilisearch devre dışı olsa bile çalışan geri düşüş (fallback) araması. |

Öncelikli olarak **equality** kolonları en sola, **range / ORDER BY** kolonu ise en sağa konumlandırılır; bu MySQL B-Tree’de ek filesort’u ortadan kaldırır.

Diğer tablolardaki öne çıkan index’ler: `articles.is_featured`, `articles.scheduled_at`, `categories.parent_id`, `article_views (article_id, day)`.

### 5.2 Önbellekleme

- **`CacheHeadersMiddleware` + `ETagMiddleware`** — koşullu `If-None-Match` istekleri 304 ile yanıtlanır.
- Yayın sırasında otomatik invalidation:
  - `Cache::forget('rss:latest')`, `Cache::forget('sitemap:xml')`.
  - `InvalidateContentCache` artisan komutu manuel temizlik için.
- Front-end (Next.js) tarafında **ISR revalidate** uç noktası, makale güncellendiğinde `App\Services\ArticleService::revalidate` tarafından `secret + tags` ile çağrılır.

### 5.3 Arama

- `laravel/scout` + `meilisearch` entegrasyonu; makaleler `published` olduğunda `searchable()`, aksi halde `unsearchable()` çağrısıyla anlık senkronize edilir.
- `MeiliConfigureArticles` ve `ScoutSyncArticles` artisan komutları ile yeniden indeksleme yapılabilir.
- Test ortamında `SCOUT_DRIVER=collection` kullanılarak arama altyapısı olmadan da çalışır.

### 5.4 Vite + Next.js

- **Vite 6** (`vite.config.js`) + `laravel-vite-plugin` + `@tailwindcss/vite`:
  - HMR, ESM tabanlı derleme, content-hashed `public/build/*` çıktıları.
  - Production: `npm run build`.
- **Next.js 15.4** admin paneli (`haberify-admin/`):
  - App Router + React 19 Server Components.
  - `@tanstack/react-query` ile istemci tarafı akıllı cache, `jotai` ile atomic state.
  - **Incremental Static Regeneration (ISR)** — Laravel tarafı yayın/güncelleme tetikleyicisiyle sayfaları on-demand yeniler.

### 5.5 Asenkron İş Yükü

`App\Jobs` altındaki job sınıfları arka planda çalışır:

- `ProcessArticleMedia`, `ProcessGalleryMedia`, `ImageOptimizeJob` — medya işleme.
- `MalwareScan` — antivirüs taraması.
- `PublishScheduledArticles`, `PublishArticleJob` — zamanlanmış yayın.
- `SendWeeklyDigest` — haftalık özet e-postaları.

`AppServiceProvider::boot` içindeki `Queue::failing` dinleyicisi başarısız job’ları hem Sentry’ye yollar hem de structured log’a düşer.

---

## 6. Kurulum

### 6.1 Gereksinimler

- **PHP 8.4** — `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `bcmath`, `fileinfo`, (opsiyonel) `gd`
- **MySQL 8.0+**
- **Node.js 20+** ve npm
- **Composer 2.6+**
- (Opsiyonel) Meilisearch 1.15+, ClamAV

### 6.2 Arka Uç (Laravel API)

```bash
# 1) Depoyu klonlayın
git clone https://github.com/Veysel440/Haberify.git haberify
cd haberify

# 2) PHP bağımlılıkları
composer install

# 3) Ortam dosyası ve anahtar
cp .env.example .env
php artisan key:generate

# 4) .env içindeki veri tabanı bilgilerini MySQL için düzenleyin
#    DB_CONNECTION=mysql
#    DB_DATABASE=haberify
#    DB_USERNAME=...
#    DB_PASSWORD=...

# 5) Şemayı kurun
php artisan migrate --force
php artisan db:seed --force       # opsiyonel

# 6) Depolama bağlantısı
php artisan storage:link

# 7) Blade varlıkları (Vite + Tailwind)
npm install
npm run build                     # production
# geliştirme için: npm run dev

# 8) Sunucu
php artisan serve                 # http://127.0.0.1:8000
```

### 6.3 Admin Paneli (Next.js)

```bash
cd haberify-admin
npm install

# .env.local oluşturun
# NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api/v1

npm run dev                       # http://localhost:3000
# prod: npm run build && npm start
```

### 6.4 Kritik `.env` Anahtarları

| Anahtar | Varsayılan | Açıklama |
|---|---|---|
| `APP_KEY` | — | Sanctum token ve 2FA challenge şifreleme anahtarı. |
| `APP_LOCALE` | `en` | Uygulama yereli. `X-Locale` header’ı ile request bazlı değişir. |
| `PASSWORD_MIN_LENGTH` | `12` | Minimum şifre uzunluğu. |
| `PASSWORD_UNCOMPROMISED_CHECK` | `true` | HIBP k-anonymity kontrolü (testte `false`). |
| `TWOFACTOR_CHALLENGE_TTL_SECONDS` | `300` | 2FA challenge token ömrü. |
| `SCOUT_DRIVER` | `meilisearch` | Testte `collection`. |
| `MEILISEARCH_HOST` / `MEILISEARCH_KEY` | — | Meilisearch bağlantı bilgileri. |
| `FRONT_REVALIDATE_URL` / `FRONT_REVALIDATE_SECRET` | — | Next.js ISR tetikleyicisi. |
| `CSP_CONNECT_SRC` / `CSP_IMG_SRC` | — | Ek CSP host’ları (CDN, analytics vb.). |
| `CORS_ALLOWED_ORIGINS` | — | Tarayıcı isteği için virgülle ayrılmış origin listesi. |
| `SENTRY_LARAVEL_DSN` | — | Sentry entegrasyonu. |
| `BCRYPT_ROUNDS` | `12` | Şifre hashleme maliyeti. |

### 6.5 Kurulum Doğrulaması

```bash
composer qa                       # Pint + PHPStan
vendor/bin/pest                   # tüm test paketi
php artisan route:list            # /api/v1/* uçları bağlı mı?
```

---

## 7. Dizin Yapısı

```
haberify/
├── app/
│   ├── Console/Commands/        # Artisan komutları (Meili, Scout, cache, digest)
│   ├── Contracts/               # Repository sözleşmeleri (DIP)
│   ├── DTO/                     # Article, Comment, Tag, Category, TwoFactor
│   ├── Enums/                   # ApiErrorCode
│   ├── Events/ + Listeners/     # ArticlePublished, CommentSubmitted, ...
│   ├── Exceptions/              # ApiException + Handler
│   ├── Exports/ + Imports/      # Excel / CSV köprüleri
│   ├── Http/
│   │   ├── Controllers/Api/V1/  # Yalnızca HTTP plumbing
│   │   ├── Middleware/          # SecurityHeaders, ThrottlePerRole, ...
│   │   ├── Requests/            # FormRequest doğrulaması (v1 + Admin)
│   │   ├── Resources/           # Eloquent → API Resource / Collection
│   │   └── Responses/           # ApiResponse (birleşik yanıt formatı)
│   ├── Jobs/                    # Arka plan iş sınıfları
│   ├── Models/                  # Article, User, Category, Tag, Comment, ...
│   ├── Notifications/           # Mail / DB bildirimleri
│   ├── Observers/               # Eloquent observer’ları
│   ├── Policies/                # Spatie + native Gate policy’leri
│   ├── Providers/               # AppService, Auth, Event, RateLimit
│   ├── Repositories/            # Eloquent* somut uygulamalar
│   ├── Services/                # ⭐ İş mantığının tamamı
│   │   ├── TwoFactor/           # Service Layer referansı (SRP)
│   │   ├── Auth/                # SanctumTokenIssuer
│   │   ├── Admin/               # ArticleBulkService
│   │   └── Article/Category/Tag/Comment/Media/Analytics/...
│   └── Support/                 # HtmlSanitizer, helpers.php (global)
├── bootstrap/
│   ├── app.php                  # Laravel 12: routing + middleware
│   └── providers.php            # Yüklenecek servis sağlayıcıları
├── config/
│   ├── security.php             # Şifre politikası
│   ├── twofactor.php            # 2FA eşikleri, recovery, abilities
│   ├── purifier.php             # HTML whitelist
│   ├── permission.php           # Spatie rol/izin ayarları
│   ├── sanctum.php · scout.php · sentry.php · virus.php · ...
├── database/
│   ├── factories/               # User, Article, Category, Tag, Comment
│   ├── migrations/              # Composite index’ler dahil
│   └── seeders/
├── haberify-admin/              # ⭐ Next.js 15 admin paneli
│   ├── src/app/                 # App Router (admin, public, login, api, sitemap)
│   ├── src/components/          # AppShell, ArticleForm, Comments, ...
│   ├── src/lib/                 # api, public-api, gtm
│   ├── src/middleware.ts        # Route guard
│   └── next.config.ts
├── resources/                   # Vite/Tailwind + Blade
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── api.php                  # /api/v1/* tamamı
│   ├── web.php
│   └── console.php
├── tests/
│   ├── Feature/Api/             # ArticleApiTest, CommentApiTest
│   ├── Feature/Auth/            # AuthEndpointsTest, PasswordPolicyTest
│   ├── Feature/Policies/        # ArticlePolicyTest
│   └── Unit/                    # HtmlSanitizerTest, ...
├── docs/qa.md                   # Denetim ve kalite notları
├── .github/workflows/qa.yml     # CI
├── pint.json
├── phpstan.neon + phpstan-baseline.neon
├── phpunit.xml
└── composer.json
```

---

## 8. API Yüzeyi

Bütün uç noktalar `/api/v1/*` altında, `api` middleware grubu içinden servis edilir. Yanıt biçimi tüm uçlarda aynıdır:

```json
{
  "status": "success",
  "message": "ok",
  "data": { ... }
}
```

| Alan | Uç noktalar |
|---|---|
| **Auth (public)** | `POST /auth/register` (throttle: register), `POST /auth/login` (throttle: login, 2FA akışı başlangıcı), `POST /auth/2fa/verify` (throttle: twofactor), `POST /auth/forgot-password`, `POST /auth/reset-password` (her ikisi throttle: password-reset) |
| **Auth (authenticated)** | `GET /auth/me`, `POST /auth/logout` |
| **2FA yönetimi** | `POST /auth/2fa/enable`, `GET /auth/2fa/qrcode`, `POST /auth/2fa/disable` |
| **İçerik (public)** | `GET /articles`, `GET /articles/{slug}`, `GET /categories`, `GET /categories/{slug}`, `GET /tags`, `GET /tags/{slug}`, `GET /pages/{slug}`, `GET /menus/{name}` |
| **Arama** | `GET /search` (throttle: search, Meilisearch destekli) |
| **Yorumlar** | `GET /articles/{id}/comments`, `POST /articles/{id}/comments` (throttle: comment-create + comment.notbanned), moderasyon: `approve`, `reject`, `delete` |
| **Makale yazma** | `POST /articles` (permission:articles.create), `PUT /articles/{id}` (articles.update), `POST /articles/{id}/publish` (articles.publish), `DELETE /articles/{id}` (articles.delete) |
| **Medya** | `POST /articles/{id}/cover`, `POST /articles/{id}/gallery` (permission:articles.update + throttle:media-upload) |
| **Taksonomi yönetimi** | `POST/PUT/DELETE /categories`, `/tags` (permission:*.manage) |
| **Sayfalar / menüler / ayarlar** | `POST/PUT/DELETE /pages`, `PUT /menus/{name}`, `GET/PUT /settings/{key}` |
| **Analitik** | `/analytics/overview`, `/analytics/top-articles`, `/analytics/referrers`, `/analytics/category-share` (permission:analytics.view) |
| **Bildirimler** | `GET /notifications`, `GET /notifications/unread-count`, `POST /notifications/{id}/read` |
| **Export / Import** | `GET /exports/articles.csv`, `POST /imports/categories` |
| **Audit** | `GET /audit` (permission:analytics.view) |
| **Admin** | `/admin/articles/{id}/schedule|feature|unfeature`, `/admin/articles/bulk` (throttle:admin-bulk), `/admin/comments/ban\|unban`, `/admin/users`, `/admin/users/{id}/assign-role`, `/admin/trash/*` |
| **İçerik akışları (public web)** | `/rss`, `/sitemap.xml` |
| **Sağlık** | `GET /up` |
| **Dokümantasyon** | `GET /api/documentation` (Swagger UI) |

---

## 9. Test Altyapısı

- **Pest 3** + `pest-plugin-laravel` — ana test koşucusu.
- **PHPUnit 11** — class-tabanlı testler (ör. `PasswordPolicyTest`) ve Pest’in altyapısı.
- Feature testler gerçek HTTP katmanından geçer: FormRequest, middleware, policy ve service zinciri canlı çalışır.
- **`RefreshDatabase`** — her test tam temiz bir şema ile başlar.
- **`UserFactory`** — politikaya uyumlu, önceden hash’lenmiş, cache’li varsayılan şifre.

Test ortamı env anahtarları (`phpunit.xml`):

```
APP_ENV=testing · BCRYPT_ROUNDS=4 · QUEUE_CONNECTION=sync · MAIL_MAILER=array
CACHE_STORE=array · SESSION_DRIVER=array · DB_CONNECTION=sqlite · DB_DATABASE=:memory:
SCOUT_DRIVER=collection · PASSWORD_UNCOMPROMISED_CHECK=false
PULSE_ENABLED=false · TELESCOPE_ENABLED=false · NIGHTWATCH_ENABLED=false
```

### Test Kapsamı (mevcut)

| Paket | Test |
|---|---|
| `Feature/Api/ArticleApiTest` | Yayındaki makalelerin listelenmesi; izinli kullanıcıyla makale oluşturma. |
| `Feature/Api/CommentApiTest` | Yorum kaydı pending durumunda, XSS sanitize edilmiş. |
| `Feature/Auth/AuthEndpointsTest` | register / forgot / reset / me / logout uçlarının HTTP sözleşmesi. |
| `Feature/Auth/PasswordPolicyTest` | 12 zayıf + 2 güçlü şifre senaryosu, confirmation mismatch. |
| `Feature/Policies/ArticlePolicyTest` | İzinli/izinsiz update reddi. |
| `Unit/HtmlSanitizerTest` | Script ve tehlikeli niteliklerin temizlenmesi. |

Komutlar:

```bash
vendor/bin/pest                 # tüm suite
vendor/bin/pest --filter=Auth   # belirli grup
vendor/bin/pest --coverage      # opsiyonel kapsama
```

---

## 10. Artisan Komutları ve Zamanlanmış İşler

`app/Console/Commands/` altında özel Artisan komutları:

| Komut | İşlev |
|---|---|
| `articles:publish-scheduled` | `scheduled_at` değeri gelmiş draft’ları yayına al. |
| `news:publish-scheduled` | (Legacy `News` modeli için) zamanlanmış yayın. |
| `articles:rollup-views` | Günlük ziyaret rollup’ı (`article_view_daily`). |
| `scout:sync-articles` | Meilisearch ile yayındaki makaleleri senkronize et. |
| `meili:configure-articles` | Meilisearch index ayarlarını (ranking rules, sortable vb.) uygula. |
| `content:invalidate-cache` | RSS / sitemap / ISR cache’lerini topluca temizle. |
| `digest:send-weekly` | Haftalık özet e-posta job’unu kuyruğa bırak. |

Tipik kuyruk & scheduler işletimi:

```bash
php artisan queue:work --tries=3
php artisan schedule:work          # cron yerine geliştirme ortamı
```

---

## 11. Geliştirici Akışı

Önerilen çalışma döngüsü:

```bash
# 1) Feature branch
git checkout -b feat/my-change

# 2) Kod + testler

# 3) Statik kapılar
composer qa                      # pint --test && phpstan analyse

# 4) Testler
vendor/bin/pest

# 5) Commit (conventional commit başlıkları)
git commit -m "feat(articles): add scheduled publish support"

# 6) PR’ı açın — GitHub Actions (.github/workflows/qa.yml) aynı kapıları koşar
```

### Dallandırma (Branching)

- `master` — her zaman yeşil, deploy edilebilir.
- `feat/*`, `fix/*`, `refactor/*`, `perf/*`, `chore/*`, `style/*`, `security/*`, `docs/*`, `hardening/*` — konu bazlı kısa ömürlü dallar.
- **Baseline `phpstan-baseline.neon` yalnızca küçülür.** Yeni kod hiçbir koşulda baseline’a eklenmez; ya sorun çözülür ya da gerçek bir false-positive ise minimum kapsamlı `ignoreErrors` tercih edilir.

### Commit Mesajı Stili

- `feat(scope): ...`, `fix(scope): ...`, `refactor(scope): ...`, `perf(scope): ...`, `security(scope): ...`, `chore(scope): ...`, `style(scope): ...`, `docs(scope): ...`
- Mesaj gövdesi **ne** yapıldığından çok **neden** yapıldığını anlatır.
- Büyük değişikliklerde commit gövdesine doğrulama çıktıları (`Pint PASS`, `PHPStan OK`, `Pest OK N tests`) eklenir.

---

<p align="center">
  <sub>Haberify · Laravel 12 + Next.js 15 · Kurumsal seviyede bakım altındadır.</sub>
</p>

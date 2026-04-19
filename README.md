<h1 align="center">Haberify</h1>

<p align="center">
  <strong>Kurumsal seviyede, güvenlik öncelikli, çok katmanlı bir haber yönetim platformu.</strong>
</p>

<p align="center">
  <em>Laravel 12 API · Next.js 15 Admin Panel · TypeScript · MySQL · Meilisearch · Sanctum · 2FA</em>
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
8. [Test Altyapısı](#8-test-altyapısı)
9. [API Yüzeyi](#9-api-yüzeyi)
10. [Geliştirici Akışı](#10-geliştirici-akışı)

---

## 1. Proje Kimliği

Haberify; içerik üretimi, yayın süreçleri, moderasyon ve analitik kabiliyetlerini tek bir çatı altında toplayan modern bir haber platformudur. Sistem, **API-first** ilkesiyle tasarlanmıştır: iş mantığı ve veri katmanı Laravel tarafında, sunum ve editöryal arayüz ise bağımsız bir Next.js uygulamasında yer alır. Bu ayrım sayesinde aynı API, yayın (public) ön yüzü, admin paneli, mobil istemciler ve üçüncü taraf entegrasyonları tarafından tutarlı biçimde tüketilebilir.

**Teknoloji yığını (özet):**

| Katman | Teknoloji | Sürüm |
|---|---|---|
| Arka uç çatısı | Laravel | **12.x** (PHP 8.4) |
| Kimlik doğrulama | Laravel Sanctum + TOTP (Google2FA) | 4.x |
| Veri tabanı | MySQL 8 (UTF-8 `utf8mb4`) | — |
| Tam metin arama | Laravel Scout + Meilisearch | 10.17 / 1.15 |
| Yetkilendirme | `spatie/laravel-permission` (rol + izin) | 6.x |
| Denetim / audit | `spatie/laravel-activitylog` | 4.10 |
| HTML temizleme | `mews/purifier` + özel `HtmlSanitizer` | 3.4 |
| Admin paneli | Next.js + React + TypeScript | 15.4.6 / 19 / 5 |
| Veri yönetimi (front) | TanStack Query + Jotai + React Hook Form + Zod | güncel |
| Stil | Tailwind CSS | 4.x |
| Derleyici (Laravel) | Vite + `laravel-vite-plugin` | 6.x |
| Gözlemlenebilirlik | Sentry (Laravel) | 4.15 |
| API dokümantasyonu | `darkaonline/l5-swagger` (OpenAPI 3) | 9.x |
| Test koşucusu | Pest 3 + PHPUnit 11 | 3.8 / 11.5 |
| Statik analiz | Larastan + PHPStan (seviye 6) | 3.x / 2.x |
| Kod biçimlendirme | Laravel Pint (PSR-12 + Laravel preset) | 1.13 |

---

## 2. Mimari

### 2.1 Genel Yaklaşım

Haberify; **katmanlı mimari (Layered Architecture)** ve **SOLID** prensiplerine sıkı biçimde uyar. Controller’lar yalnızca **request validation** ve **response cycle**’dan sorumludur; iş mantığı tamamen `App\Services` altındaki servis sınıflarına taşınmıştır. Veri erişimi, soyut `App\Contracts\*RepositoryInterface` sözleşmeleri üzerinden gerçekleşir ve bu sözleşmeler `AppServiceProvider` içinde Eloquent somut sınıflarına bağlanır.

```
┌─────────────────────────────────────────────────────┐
│  HTTP / Router  (routes/api.php)                    │
│  ├─ FormRequest → validation                        │
│  └─ Middleware → auth, permission, throttle, CSP    │
├─────────────────────────────────────────────────────┤
│  Controller  (App\Http\Controllers\Api\V1\...)      │
│     yalnızca çeviri: Request → Service → Response   │
├─────────────────────────────────────────────────────┤
│  Service Layer  (App\Services\...)                  │
│     iş kuralları · orkestrasyon · DTO’lar · olaylar │
├─────────────────────────────────────────────────────┤
│  Repository Contract  (App\Contracts\...)           │
│     Eloquent uygulaması  (App\Repositories\...)     │
├─────────────────────────────────────────────────────┤
│  Model / Eloquent ORM · Policy · Event / Listener   │
└─────────────────────────────────────────────────────┘
```

### 2.2 Service Layer Örneği: `TwoFactorService`

İki faktörlü kimlik doğrulama akışı, **Single Responsibility Principle (SRP)** açısından referans bir örnektir. Tek bir “tanrı sınıfı” yerine, her biri tek bir sorumluluk taşıyan küçük işbirlikçilerden (collaborators) oluşur. `TwoFactorController` yalnızca 141 satırlık saf bir cephe (plumbing) olarak kalır; bütün kurallar ve yan etkiler aşağıdaki hizmetlerde yaşar:

| Sınıf | Sorumluluk | Değişme Sebebi |
|---|---|---|
| `TwoFactorService` | Bütün akışın **orkestratörü** (facade). | Akışın adımlarını değiştirmek istediğimizde. |
| `TwoFactorSecretManager` | TOTP gizli anahtarının üretimi, **şifrelenmesi**, recovery kodları. | Şifreleme veya depolama stratejisi değiştiğinde. |
| `TwoFactorRateLimiter` | Login ve challenge aşamaları için **oran sınırı** (rate limit). | Eşikler/stratejiler değiştiğinde. |
| `TwoFactorChallengeTokenizer` | Geçici challenge token’ının üretimi, TTL kontrolü, çözümlenmesi. | Token formatı/TTL değiştiğinde. |
| `Auth\SanctumTokenIssuer` | Rol → Sanctum ability eşlemesi, tek otorite. | Yetki modeli değiştiğinde. |

Bu dekompozisyon sayesinde:

- **SRP** – her sınıf tek bir değişme sebebi taşır,
- **OCP (Open/Closed)** – yeni 2FA kanalı (SMS, WebAuthn vb.) eklemek için mevcut kod değiştirilmez, yeni işbirlikçi enjekte edilir,
- **DIP (Dependency Inversion)** – `StatefulGuard`, `Encrypter`, `ConfigRepository` gibi Laravel sözleşmeleri doğrudan üzerinden enjekte edilir; facade (`Auth`, `Crypt`) kullanımı bilinçli olarak asgari tutulur,
- **Sealed-style DTO**’lar (`LoginAttemptResult`, `TwoFactorChallengeResult`, `TwoFactorEnrollmentData`) akışın olası her sonucunu tür güvenli bir biçimde temsil eder; controller bu sonuçları bir `match` ifadesiyle HTTP yanıtına çevirir.

> Controller’ın akışı özetle: `FormRequest → Service → Result DTO → ApiResponse`.

### 2.3 Diğer Servisler

| Servis | İşlev |
|---|---|
| `ArticleService` | Makale CRUD, yayın akışı, Scout indeksleme, front-end ISR yeniden doğrulama (revalidate) tetikleyicisi, tahmini okuma süresi. |
| `CategoryService`, `TagService` | Taksonomi yönetimi + slug üretimi. |
| `CommentService` | Yorum kaydı, HTMLPurifier tabanlı sanitizasyon, moderasyon olayları. |
| `Admin\ArticleBulkService` | Yığın işlemler (toplu yayınlama, arşivleme, silme) — ayrı bir throttle altında. |
| `AnalyticsService` | Overview / top-articles / referrers / category-share metrikleri. |
| `MediaProcessor` | `intervention/image` ile medya işleme ve `AntivirusScanner` entegrasyonu. |
| `AntivirusScanner`, `VirusScanner` | Yüklenen dosyalar için ClamAV tarzı arka plan taraması (yapılandırılabilir). |
| `AuditLogger` | `spatie/laravel-activitylog` üzerine yazılmış, merkezî audit girişi. |
| `SitemapService`, `RssFeedService` | Sitemap ve RSS üretimi (cache’li). |
| `VisitService`, `TrackArticleView`/`TrackReferrer` middleware’ları | Ziyaret ve referrer toplama. |

### 2.4 HTTP Boru Hattı (Middleware Pipeline)

`bootstrap/app.php` içinde konumlandırılmış, Laravel 12’nin yeni konfigürasyon akışı kullanılır. Sırasıyla uygulanan önemli middleware’lar:

- `statefulApi` – Sanctum SPA çerez desteği (Next.js admin paneli için).
- `SecurityHeaders` – CSP, `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `Permissions-Policy`.
- `ThrottlePerRole` – Role özel oran sınırı: `admin=600/dk`, `editor=300/dk`, `author=200/dk`, `guest=60/dk`.
- `ForceJsonResponse`, `ValidateJson` – Tüm API yanıtlarının JSON olmasını zorunlu kılar.
- `SetLocaleFromHeader` – `X-Locale` başlığından `tr`/`en` yerelleştirmesi.
- `CacheHeadersMiddleware` + `ETagMiddleware` – Akıllı 304 yanıtları.
- `SentryContext`, `RequestIdMiddleware` – İzlenebilirlik.
- `TrackArticleView`, `TrackReferrer` – Analitik.
- Route-alias middleware’ları: `permission` (Spatie), `role`, `role_or_permission`, `comment.notbanned`.

---

## 3. Kalite Standartları

Haberify, **“baseline yalnızca küçülür, hiç büyümez”** politikasını uygular. Yeni gelen kod her iki kapıdan (lint + static analysis) temiz geçmek zorundadır; mevcut teknik borç ise kontrollü biçimde `phpstan-baseline.neon` üzerinde azaltılır.

### 3.1 Laravel Pint — PSR-12 + Laravel Preset

`pint.json` dosyası aşağıdaki kuralları zorunlu kılar:

- **PSR-12** temel alınır,
- Tüm PHP dosyaları `declare(strict_types=1);` ile başlar (`declare_strict_types`),
- Tek tırnak zorunludur (`single_quote`),
- `use` ifadeleri **length + alphabetical** sırada tutulur (`ordered_imports`),
- `! ` operatörünün ardına boşluk konur (`not_operator_with_successor_space`),
- `short_ternary`, `no_unused_imports`, `global_namespace_import` vb. modern kurallar aktiftir,
- `vendor/`, `storage/`, `database/migrations/` (bazı Laravel stub’ları için) ve `haberify-admin/` dizinleri taramadan muaftır.

Kullanım:

```bash
composer lint         # kontrol
composer lint:fix     # yerinde düzelt
```

### 3.2 Larastan / PHPStan — Seviye 6

`phpstan.neon` ayarları:

- Genişletme: `larastan/larastan` (Eloquent, container, facade analizi),
- Seviye: **6**,
- Taranan yollar: `app`, `config`, `database/factories`, `database/seeders`, `routes`, `tests`,
- Baseline: `phpstan-baseline.neon` (318 kayıt — işe başlandığında 393 idi),
- Paralel 4 süreç, 300 sn işlem zaman aşımı,
- `missingType.iterableValue` ve Sentry facade türleri baseline harici olarak göz ardı edilir.

Kullanım:

```bash
composer analyse              # analiz
composer analyse:baseline     # baseline’ı yeniden üret
composer qa                   # lint + analyse (CI kapısı)
```

### 3.3 Sürekli Entegrasyon

`.github/workflows/qa.yml`; her `push` ve `pull_request` için Pint ve PHPStan işlerini paralel çalıştırır. Composer ve PHPStan cache’leri GitHub Actions seviyesinde saklanarak analiz süresi asgaride tutulur.

---

## 4. Güvenlik

Güvenlik, sistemin tasarımı boyunca **sunucu tarafında son söz** ilkesiyle ele alınır. İstemciden gelen hiçbir iddia, sunucu doğrulaması olmadan etkili sayılmaz.

### 4.1 İki Faktörlü Kimlik Doğrulama (2FA / TOTP)

- `pragmarx/google2fa` kütüphanesi üzerinden RFC 6238 uyumlu TOTP.
- Login akışı **iki adımlı**dır:
  1. `POST /api/v1/auth/login` — e-posta + şifre doğrulanır, kullanıcı 2FA kayıtlıysa **geçici challenge token** döner.
  2. `POST /api/v1/auth/2fa/verify` — challenge token + 6 haneli kod doğrulanır, geçerli ise kalıcı Sanctum API token verilir.
- Challenge token’ı sunucunun `APP_KEY`’i ile şifrelenmiş ve TTL’i gömülmüş (default 300 sn) bir kayıt olarak taşınır; istemcide doğrulanabilir hiçbir iddia tutulmaz.
- **Recovery kodları** yalnızca enrolment yanıtında bir kereliğine plaintext olarak döner; veri tabanında `Illuminate\Contracts\Encryption\Encrypter` üzerinden şifrelenmiş JSON olarak saklanır.
- Her iki adımın da bağımsız **rate limiter**’ı vardır (`config/twofactor.php → rate_limit.login` / `rate_limit.challenge`).

### 4.2 Sunucu Tarafı Kimlik Doğrulama Middleware’leri

- **Laravel Sanctum** — `auth:sanctum` ile korunan tüm uç noktalar `Bearer` token veya SPA çerez oturumu üzerinden kimlik doğrular.
- **Spatie Permission** — `permission:articles.update` gibi route-alias middleware’ları ile **fine-grained** yetkilendirme. `Gate::before` yalnızca `admin` rolüne koşulsuz yetki verir; diğer tüm roller izin tabanlı çalışır.
- **`ThrottlePerRole`** — Role göre oran sınırı; brute-force ve scraping saldırılarını azaltır.
- **`EnsureNotCommentBanned`** — Yorum yazan kullanıcının IP/kullanıcı bazlı banlı olup olmadığını denetler.
- **`ValidateJson` + `ForceJsonResponse`** — API uçları yalnızca JSON kabul/üretir, hatalı `Content-Type` erken reddedilir.

### 4.3 Şifre Politikası (OWASP ASVS / NIST 800-63B)

`config/security.php` üzerinden tek noktadan yönetilir:

- En az **12 karakter** (`PASSWORD_MIN_LENGTH`),
- Büyük/küçük harf + rakam + sembol zorunluluğu,
- **Have I Been Pwned** veri kümesine karşı k-anonymity sorgusu (`uncompromised`),
- `Password::defaults()` callback’i `AppServiceProvider::boot` içinde kayıtlıdır; register, reset ve admin tarafından yapılan tüm şifre yazımları bu ortak kuralı kullanır,
- Şifreler `Hash::make` ile hashlenir (Bcrypt 12 tur varsayılan; Argon2id’ye geçiş için `config/hashing.php` tek satırlık değişiklik).

### 4.4 İçerik Güvenliği

- **HTML Purifier** + özel `App\Support\HtmlSanitizer`, yorum ve makale gövdelerini beyaz listeli etiket seti ve **şema kısıtlı URI’lar** (`http`, `https`, `mailto`, `tel`) ile temizler. `javascript:`, `data:` URI’ları kökten reddedilir.
- **Security Headers** middleware’i her yanıta CSP, `X-Frame-Options: DENY`, `Referrer-Policy`, `Permissions-Policy` (kamera/mikrofon/konum kapalı) ekler.
- **Antivirus/VirusScanner** servisleri, yüklenen medya için opsiyonel ClamAV taraması yapabilir (`config/virus.php`).
- **Audit Log** (`spatie/laravel-activitylog` + `AuditMutation` middleware) — kritik mutasyonlar (yayın, silme, rol değişikliği) audit girişiyle kayıt altına alınır.

### 4.5 Oturum / Token Hijyeni

- Şifre sıfırlandığında kullanıcının tüm Sanctum token’ları iptal edilir (`PasswordResetController::reset`).
- Logout yalnızca **o oturumun** token’ını iptal eder; diğer cihaz oturumları korunur.
- `personal_access_tokens` tablosu, token hash’lerini SHA-256 ile saklar; plaintext token asla loglanmaz.

---

## 5. Performans

### 5.1 Veri Tabanı — Composite Indexing

`articles` tablosu, yazma maliyetini artırmadan okuma sorgularını tek bir index range scan’e indirgeyen hedefli composite index’lerle donatılmıştır:

| Index | Kolonlar | Amaç |
|---|---|---|
| `articles_status_published_idx` | `(status, published_at)` | Global “yayındaki makaleler, en yeniden eskiye” feed’i. |
| `articles_status_schedule_idx` | `(status, scheduled_at)` | Zamanlanmış yayın kuyruğu. |
| `articles_author_status_published_idx` | `(author_id, status, published_at)` | Yazar dashboard’u / public yazar profili — **equality → equality → range/ORDER BY** sıralamasıyla leftmost prefix kuralını tam anlamıyla kullanır. |
| `articles_title_summary_body_fulltext` | `FULLTEXT(title, summary, body)` | Meilisearch devre dışı kaldığında bile çalışan geri düşüş (fallback) araması. |

Öncelikli olarak **equality** kolonları en sola, **range / ORDER BY** kolonu ise en sağa konumlandırılır; bu, MySQL B-Tree’de ek filesort’u ortadan kaldırır.

### 5.2 Önbellek

- **`CacheHeadersMiddleware` + `ETagMiddleware`** — koşullu `If-None-Match` istekleri 304 ile yanıtlanır.
- `Cache::forget('rss:latest')`, `Cache::forget('sitemap:xml')` tetikleyicileri makale yayınında otomatik çalışır.
- Front-end (Next.js) tarafında **ISR revalidate** uç noktası, makale güncellendiğinde `App\Services\ArticleService::revalidate` tarafından çağrılır (secret + tags ile).

### 5.3 Arama

- `laravel/scout` + `meilisearch` entegrasyonu; makaleler `published` olduğunda `searchable()`, aksi halde `unsearchable()` çağrısıyla anlık senkronize edilir.
- Test ortamında `SCOUT_DRIVER=collection` kullanılarak arama altyapısı olmadan da çalışır.

### 5.4 Vite + Next.js Optimizasyonları

- **Vite 6** + `laravel-vite-plugin`: Hot Module Replacement, ESM tabanlı derleme, Tailwind CSS 4 plugin’i.
- Production build: `npm run build` (Vite) → `public/build/*` hashed varlıklar, cache-busting.
- **Next.js 15.4** admin paneli: App Router, React 19 Server Components ve **Incremental Static Regeneration** (ISR).
- `@tanstack/react-query` ile istemci tarafı akıllı cache; `jotai` ile atomic state.

### 5.5 Queue / Asenkron İş Yükü

- `ShouldQueue` listener’ları (`NotifyEditorsNewComment`, vb.) arka planda çalışır; senkron HTTP yanıtı bunlardan bağımsız olur.
- `AppServiceProvider::boot` içinde `Queue::failing` dinleyicisi; başarısız iş hem Sentry’ye yollanır hem de structured log’a düşer.

---

## 6. Kurulum

### 6.1 Gereksinimler

- PHP **8.4** — `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `bcmath`, `fileinfo`, (opsiyonel) `gd`
- MySQL **8.0+**
- Node.js **20+** ve npm (veya pnpm)
- Composer **2.6+**
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

# 4) Veri tabanı bilgilerini .env içine girin
#    DB_CONNECTION=mysql
#    DB_DATABASE=haberify
#    DB_USERNAME=...
#    DB_PASSWORD=...

# 5) Şemayı kurun ve (opsiyonel) seed edin
php artisan migrate --force
php artisan db:seed --force      # opsiyonel

# 6) Storage link (medya için)
php artisan storage:link

# 7) Vite varlıklarını derleyin (Blade + Tailwind)
npm install
npm run build                    # production
# ya da geliştirme için: npm run dev

# 8) Sunucuyu başlatın
php artisan serve                # http://127.0.0.1:8000
```

### 6.3 Admin Paneli (Next.js)

```bash
cd haberify-admin
npm install

# .env.local oluşturun
cp .env.example .env.local
# NEXT_PUBLIC_API_URL=http://127.0.0.1:8000/api/v1

npm run dev                      # http://localhost:3000
# prod:
npm run build && npm start
```

### 6.4 Kritik `.env` Anahtarları

| Anahtar | Varsayılan | Açıklama |
|---|---|---|
| `APP_KEY` | `base64:...` | Sanctum token ve 2FA challenge şifreleme anahtarı. |
| `PASSWORD_MIN_LENGTH` | `12` | Minimum şifre uzunluğu. |
| `PASSWORD_UNCOMPROMISED_CHECK` | `true` | HIBP k-anonymity kontrolü (testte kapalıdır). |
| `TWOFACTOR_CHALLENGE_TTL_SECONDS` | `300` | Challenge token ömrü. |
| `SCOUT_DRIVER` | `meilisearch` | Üretimde `meilisearch`, testte `collection`. |
| `MEILISEARCH_HOST` / `MEILISEARCH_KEY` | — | Meilisearch bağlantı bilgileri. |
| `FRONT_REVALIDATE_URL` / `FRONT_REVALIDATE_SECRET` | — | Next.js ISR tetikleyicisi. |
| `CSP_CONNECT_SRC` / `CSP_IMG_SRC` | — | CSP genişletmeleri (CDN, analytics vb.). |

### 6.5 Yerel Doğrulama

Kurulum tamamlandıktan sonra kapıların açık olduğunu doğrulayın:

```bash
composer qa                # Pint + PHPStan
vendor/bin/pest            # 33 test, 77 assertion
php artisan route:list     # /api/v1/* uçlarının bağlandığını doğrula
```

---

## 7. Dizin Yapısı

```
haberify/
├── app/
│   ├── Contracts/              # Repository sözleşmeleri (DIP)
│   ├── DTO/                    # Article, Comment, TwoFactor DTO’ları
│   ├── Events/ & Listeners/    # CommentSubmitted, CommentModerated, ...
│   ├── Exceptions/             # ApiException, domain-özel hatalar
│   ├── Http/
│   │   ├── Controllers/Api/V1/ # Yalnızca HTTP plumbing
│   │   ├── Middleware/         # SecurityHeaders, ThrottlePerRole, ...
│   │   ├── Requests/Api/V1/    # Tüm girdilerin FormRequest doğrulaması
│   │   ├── Resources/          # Eloquent → API Resource / Collection
│   │   └── Responses/          # ApiResponse (birleşik yanıt formatı)
│   ├── Models/                 # Article, User, Category, Tag, Comment, ...
│   ├── Policies/               # ArticlePolicy, CategoryPolicy, ...
│   ├── Providers/              # AppService, Auth, Event, RateLimit
│   ├── Repositories/           # Eloquent* somut uygulamalar
│   ├── Services/               # ⭐ İş mantığının tamamı
│   │   ├── TwoFactor/          # Service Layer referansı
│   │   ├── Auth/               # SanctumTokenIssuer
│   │   ├── Admin/              # ArticleBulkService
│   │   └── Article/Category/Tag/Comment/Media/Analytics/...
│   └── Support/                # HtmlSanitizer, helpers.php (global)
├── bootstrap/
│   ├── app.php                 # Laravel 12 konfig: routing + middleware
│   └── providers.php           # Yüklenecek servis sağlayıcıları
├── config/
│   ├── security.php            # Şifre politikası
│   ├── twofactor.php           # 2FA eşikleri, recovery, abilities
│   ├── purifier.php            # HTML whitelist
│   ├── permission.php          # Spatie role/izin ayarları
│   ├── sanctum.php, scout.php, sentry.php, virus.php, ...
├── database/
│   ├── factories/              # User, Article, Category, Tag, Comment
│   ├── migrations/             # Composite index’ler dahil
│   └── seeders/
├── haberify-admin/             # ⭐ Next.js 15 admin paneli
│   ├── src/
│   ├── next.config.ts
│   └── package.json
├── routes/
│   ├── api.php                 # /api/v1/* tamamı
│   ├── web.php
│   └── console.php
├── resources/                  # Vite/Tailwind kaynakları
├── tests/
│   ├── Feature/Api/            # ArticleApiTest, CommentApiTest
│   ├── Feature/Auth/           # AuthEndpointsTest, PasswordPolicyTest
│   ├── Feature/Policies/       # ArticlePolicyTest
│   └── Unit/                   # HtmlSanitizerTest, ...
├── pint.json
├── phpstan.neon + phpstan-baseline.neon
├── phpunit.xml
└── composer.json
```

---

## 8. Test Altyapısı

- **Pest 3** + `pest-plugin-laravel` — ana test koşucusu.
- **PHPUnit 11** — class-tabanlı testler (ör. `PasswordPolicyTest`) ve Pest’in altyapısı.
- **Feature test** vurgusu — uç noktalar gerçek HTTP katmanından geçer; FormRequest, middleware, policy ve service zinciri gerçekten çalışır.
- **`RefreshDatabase`** — her test tam temiz bir şema ile başlar (`MySQL` veya `sqlite :memory:`).
- **`UserFactory`** — politikaya uyumlu, önceden hash’lenmiş, cache’li `CorrectHorse-Battery!9Staple` varsayılan şifresi.
- **Test ortamı env’leri** (`phpunit.xml`): `BCRYPT_ROUNDS=4` (hız), `QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`, `SCOUT_DRIVER=collection`, `PASSWORD_UNCOMPROMISED_CHECK=false` (HIBP dış ağı tetiklenmez).

Komutlar:

```bash
vendor/bin/pest                 # tüm suite
vendor/bin/pest --filter=Auth   # belirli bir grup
vendor/bin/pest --coverage      # opsiyonel kapsama
```

Mevcut durum: **33 test / 77 assertion** — CI üzerinde %100 yeşil.

---

## 9. API Yüzeyi

Bütün uç noktalar `/api/v1/*` altında, `api` middleware grubu içinden servis edilir.

| Alan | Örnek uç noktalar |
|---|---|
| **Auth** | `POST /auth/register` (throttle: register), `POST /auth/login` (throttle: login, 2FA akışı), `POST /auth/2fa/verify`, `POST /auth/forgot-password`, `POST /auth/reset-password`, `GET /auth/me`, `POST /auth/logout` |
| **2FA yönetimi** | `POST /auth/2fa/enable`, `GET /auth/2fa/qrcode`, `POST /auth/2fa/disable` |
| **Makaleler** | `GET /articles`, `GET /articles/{slug}`, `POST /articles`, `PUT /articles/{id}`, `POST /articles/{id}/publish`, `DELETE /articles/{id}`, `POST /articles/{id}/cover`, `POST /articles/{id}/gallery` |
| **Taksonomi** | `/categories`, `/tags` — standart CRUD |
| **Yorumlar** | `GET /articles/{id}/comments`, `POST /articles/{id}/comments` (throttle: comment-create), moderasyon uçları |
| **Arama** | `GET /search` (Meilisearch destekli) |
| **Analitik** | `/analytics/overview`, `/top-articles`, `/referrers`, `/category-share` |
| **Admin** | `/admin/articles/{id}/schedule`, `/admin/articles/bulk`, `/admin/users`, `/admin/users/{id}/assign-role`, `/admin/trash/*` |
| **Içerik akışları** | `/rss`, `/sitemap.xml` (public web) |
| **Dokümantasyon** | `/api/documentation` (Swagger UI, `l5-swagger` ile) |

Yanıt biçimi: her yanıt `{ status, message, data | errors }` sözleşmesini izler (`App\Http\Responses\ApiResponse` / `App\Support\ApiResponse`).

---

## 10. Geliştirici Akışı

Öneri çalışma sırası:

```bash
# 1) Bir feature branch açın
git checkout -b feat/my-change

# 2) Kod yazın
# 3) Statik kapılar
composer qa                       # pint --test && phpstan analyse

# 4) Testler
vendor/bin/pest

# 5) Commit (conventional commit başlıkları tercih edilir)
git commit -m "feat(articles): add scheduled publish support"

# 6) PR’ı açın; GitHub Actions (.github/workflows/qa.yml) aynı kapıları koşar
```

**Dallandırma (branching) stratejisi:**

- `master` — her zaman yeşil, deploy edilebilir.
- `feat/*`, `fix/*`, `refactor/*`, `perf/*`, `chore/*`, `style/*`, `security/*` — konu bazlı kısa ömürlü dallar.
- Baseline dosyası (`phpstan-baseline.neon`) yalnızca **küçülür**; yeni kod asla baseline’a eklenmez.

---

<p align="center">
  <sub>Haberify · Laravel 12 + Next.js 15 · Kurumsal seviyede bakım altındadır.</sub>
</p>

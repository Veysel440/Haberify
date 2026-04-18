# Static Analysis & Code Style

This project uses **Laravel Pint** (PSR-12 + Laravel preset) for code style and **Larastan** (PHPStan level 6) for static analysis.

## Local commands

```bash
# Code style — fail-on-violation (CI mode)
composer lint

# Code style — auto-fix
composer lint:fix

# Static analysis (uses baseline)
composer analyse

# Both checks (lint + analyse)
composer qa

# Regenerate baseline after sweeping fixes
composer analyse:baseline
```

## Baseline policy

`phpstan-baseline.neon` freezes the **393 pre-existing errors** at the time of Larastan adoption so the build is unblocked. **New code must analyse cleanly at level 6** — the baseline must shrink, never grow. When you fix items off the baseline, regenerate it with `composer analyse:baseline` and commit the diff.

## Critical findings (review queue)

The baseline contains 393 frozen findings. After categorisation, the following are **real bugs or missing dependencies**, not just type-annotation gaps:

### 1. Missing dependency: `spatie/laravel-permission` — 11 errors
`database/seeders/RolesAndPermissionsSeeder.php` and `App\Models\User::assignRole()` reference `Spatie\Permission\Models\{Permission, Role}` and `PermissionRegistrar`, but the package is **not declared in `composer.json`**. Either install the package (`composer require spatie/laravel-permission`) or remove the dead code paths.

### 2. Undefined helper `estimate_minutes()` — 2 call sites
`App\Services\ArticleService` line 28 and 92 call `estimate_minutes()` which is not defined in `app/Support/helpers.php`. Reading-time estimation appears to be a planned feature with a missing helper.

### 3. Undeclared Eloquent relations on `App\Models\Article` — 13 errors
Repositories and console commands query `Article::with(['author', 'category', 'tags'])`, but those `BelongsTo`/`BelongsToMany` methods are not declared on the `Article` model. This works at runtime *only if* the relation methods exist (and the audit shows they don't, so these queries fail). Add `author()`, `category()`, `tags()` methods to the model.

### 4. Pest syntax in tests without Pest installed — 21+ errors
`tests/Pest.php`, `tests/Feature/Api/*Test.php`, `tests/Feature/Policies/*Test.php`, `tests/Unit/HtmlSanitizerTest.php` use `it()`, `expect()`, `uses()` but `pestphp/pest` is not in `composer.json`. Either `composer require --dev pestphp/pest pestphp/pest-plugin-laravel` or port to PHPUnit.

### 5. Intervention Image v3 API drift — 2 call sites
`App\Services\MediaProcessor` calls `$image->orientate()` (lines 24, 29) which **was removed in Intervention Image 3.x**. Replace with the `orient` driver call or rely on browser handling of EXIF orientation.

### 6. Undefined property `News::$status` — 1 site
`PublishScheduledNews` command writes to `$news->status` but the property is neither in `$fillable` nor a real column. Verify whether this command should target `Article` instead, or add the column.

### 7. Stale `RouteServiceProvider::boot()` parent call — 1 site
`parent::boot()` invoked on a class that no longer extends `Illuminate\Foundation\Support\Providers\RouteServiceProvider` — fatal at runtime if the parent path is taken. Either restore the extends or drop the parent call.

### 8. Unnecessary nullsafe (defensive but misleading) — 7 sites
`?->id ?? ...` chains in `RouteServiceProvider` and `RssFeedService` where the left-hand side cannot be null. Cosmetic but signals incorrect mental model.

## Categorical breakdown

| Identifier | Count | Severity |
|------------|------:|----------|
| `missingType.return` | 135 | low (type annotations) |
| `missingType.parameter` | 64 | low |
| `property.notFound` | 49 | **high** (mostly relation accessors — see #3) |
| `method.notFound` | 46 | **high** (incl. `assignRole`, `searchable`, `orientate`) |
| `missingType.generics` | 22 | medium |
| `function.notFound` | 21 | **high** (Pest + `estimate_minutes`) |
| `larastan.relationExistence` | 13 | **high** (see #3) |
| `class.notFound` | 11 | **high** (see #1) |
| `staticMethod.notFound` | 8 | **high** (factory + dispatch chains) |
| `nullsafe.neverNull` | 7 | low |
| `variable.undefined` | 5 | medium (Pest tests `$this`) |
| Other | 12 | low |
| **Total** | **393** | |

## Pint findings

Dry-run reported **199 files** would be modified. Most violations are `declare_strict_types`, `blank_line_after_opening_tag`, `concat_space`, `single_quote`. Run `composer lint:fix` to auto-format. **This PR intentionally does not run the auto-fix** to keep the change scoped to tooling — the formatting sweep belongs in a separate PR so reviewers can isolate substantive changes from cosmetic ones.

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

/**
 * Base controller for the app.
 *
 * Extends Laravel's routing Controller so subclasses can still call
 * `$this->middleware(...)` inside their constructors — the pattern used
 * throughout this codebase. The actual middleware chain is enforced at
 * the route definition level (see routes/api.php); the controller-level
 * declarations are a belt-and-suspenders safety net and make the
 * controller's security contract readable in isolation.
 */
abstract class Controller extends BaseController
{
    //
}

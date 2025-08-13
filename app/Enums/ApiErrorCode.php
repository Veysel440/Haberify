<?php

declare(strict_types=1);

namespace App\Enums;

enum ApiErrorCode: string
{
    case VALIDATION = 'VALIDATION_ERROR';
    case NOT_FOUND  = 'NOT_FOUND';
    case FORBIDDEN  = 'FORBIDDEN';
    case UNAUTH     = 'UNAUTHORIZED';
    case RATE_LIMIT = 'RATE_LIMITED';
    case CONFLICT   = 'CONFLICT';
    case SERVER     = 'SERVER_ERROR';
}

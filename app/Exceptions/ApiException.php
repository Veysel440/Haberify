<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct(string $message = 'İşlem başarısız', int $code = 400)
    {
        parent::__construct($message, $code);
    }
}

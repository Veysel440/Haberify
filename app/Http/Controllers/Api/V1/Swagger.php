<?php

namespace App\Http\Controllers\Api\V1;

/**
 * @OA\Info(title="Haberify API", version="1.0.0")
 * @OA\Server(url="/", description="Base")
 * @OA\SecurityScheme(
 *   securityScheme="bearerAuth",
 *   type="http",
 *   scheme="bearer",
 *   bearerFormat="Token"
 * )
 */
class Swagger {}

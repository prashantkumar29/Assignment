<?php

namespace App\Http;

use App\Http\Middleware\EncryptDecryptMiddleware;

class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        // ...existing code...
        'encrypt.decrypt' => EncryptDecryptMiddleware::class,
    ];
}
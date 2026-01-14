<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{

    private array $allowedOrigins = [
        'http://localhost:4200',
        'http://127.0.0.1:4200',
        'https://sucata.chfb.com.br'
    ];

    public function handle($request, Closure $next)
    {
        $origin = $request->headers->get('Origin');
        $allowOrigin = '';
        if (in_array($origin, $this->allowedOrigins, true)) {
            $allowOrigin = $origin;
        }

        $headers = [
            'Access-Control-Allow-Origin' => $allowOrigin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
            'Access-Control-Allow-Headers' => 'Authorization, Content-Type, Accept, X-Requested-With',
        ];

        if ($request->isMethod('OPTIONS')) {
            return response([], 204, $headers);
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            if ($value !== '') {
                $response->header($key, $value);
            }
        }

        return $response;
    }
}

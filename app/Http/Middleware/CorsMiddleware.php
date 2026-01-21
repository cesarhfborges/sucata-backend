<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{

    private array $allowedOrigins = [
        'http://localhost:4200',
        'http://127.0.0.1:4200',
        'http://192.168.3.23:4200',
        'https://sucata.chfb.com.br',
        'https://sucata-api.platoflex.com.br'
    ];

    public function handle($request, Closure $next)
    {
        $origin = null;

        if (in_array($request->headers->get('Origin'), $this->allowedOrigins, true)) {
            $origin = $request->headers->get('Origin');
        }

        $headers = [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
            'Access-Control-Allow-Headers' => 'Authorization, Content-Type, Accept, X-Requested-With',
            'Access-Control-Expose-Headers' => 'Content-Disposition, Content-Type, Content-Length',
        ];

        if ($request->getMethod() === 'OPTIONS') {
            return response('', 204)->withHeaders(array_filter($headers));
        }

        $response = $next($request);

        foreach ($headers as $key => $value) {
            if ($value !== null) {
                $response->headers->set($key, $value);
            }
        }

        return $response;
    }
}

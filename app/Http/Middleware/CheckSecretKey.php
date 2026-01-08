<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSecretKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $secretKey = $request->header('X-SUPER-SECRET-KEY');
        $expectedKey = config('app.super_secret_key');

        if (!$secretKey) {
            return response()->json([
                'error' => 'Missing X-SUPER-SECRET-KEY header'
            ], 401);
        }

        if ($secretKey !== $expectedKey) {
            return response()->json([
                'error' => 'Invalid secret key'
            ], 403);
        }

        return $next($request);
    }
}
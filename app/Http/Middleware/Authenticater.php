<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticater
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $Bearer = $request->bearerToken();
        if ($Bearer !== env('T1_PASSWORD')) {
            dd('test');
            return response()->json(['message' => 'Unauthorized Wrong Password'], 401);
        }
        return $next($request);
    }
}

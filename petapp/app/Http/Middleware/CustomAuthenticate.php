<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class CustomAuthenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : null;
    }

    public function handle($request, Closure $next, ...$guards)
    {
        $response = $next($request);

        if (in_array($response->status(), [200, 201, 404, 401, 422, 403, 500])) {
            $response->header('Content-Type', 'application/json');
        }

        return $response;
        //return parent::handle($request, $next, $guards);
    }
}

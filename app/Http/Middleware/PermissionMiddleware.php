<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $permissionSlug
    ): Response {
        abort_unless(
            $request->user()
                && $request->user()->hasPermission($permissionSlug),
            403
        );

        return $next($request);
    }
}

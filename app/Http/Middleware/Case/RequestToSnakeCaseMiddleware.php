<?php

namespace App\Http\Middleware\Case;

use Closure;
use Illuminate\Http\Request;

class RequestToSnakeCaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $request->replace(
            resolve(KeyCaseConverter::class)->convert(
                KeyCaseConverter::CASE_SNAKE,
                $request->all()
            )
        );

        return $next($request);
    }
}

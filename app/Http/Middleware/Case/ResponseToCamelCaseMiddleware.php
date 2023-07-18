<?php

namespace App\Http\Middleware\Case;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResponseToCamelCaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $response->setData(
                resolve(KeyCaseConverter::class)->convert(
                    KeyCaseConverter::CASE_CAMEL,
                    json_decode($response->content(), true)
                )
            );
        }

        return $response;
    }
}

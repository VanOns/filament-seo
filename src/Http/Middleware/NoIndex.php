<?php

namespace VanOns\FilamentSeo\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoIndex
{
    /**
     * The tag is always added for non-prod sites.
     * For production sites, it can be turned on in the config.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!app()->isProduction() || config('seo.no-index', false)) {
            $response = $next($request);
            $response->headers->set('X-Robots-Tag', 'none');

            return $response;
        }

        return $next($request);
    }
}

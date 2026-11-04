<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** /work/print-vision/ (the Astro URLs) → 301 to /work/print-vision, so each page has one URL. */
class RemoveTrailingSlash
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->getPathInfo();
        if (in_array($request->getMethod(), ['GET', 'HEAD'], true) && $path !== '/' && str_ends_with($path, '/')) {
            $query = $request->getQueryString();

            return redirect()->to(rtrim($path, '/').($query ? "?{$query}" : ''), 301);
        }

        return $next($request);
    }
}

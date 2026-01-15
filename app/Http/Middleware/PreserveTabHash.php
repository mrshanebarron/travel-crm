<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreserveTabHash
{
    /**
     * Handle an incoming request.
     * If the request has a _tab parameter and the response is a redirect,
     * append the tab as a URL fragment.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Check if we have a tab to preserve and if response is a redirect
        if ($request->has('_tab') && $response->isRedirect()) {
            $tab = $request->input('_tab');
            $targetUrl = $response->getTargetUrl();

            // Only add hash if it's not already there
            if (!str_contains($targetUrl, '#')) {
                $response->setTargetUrl($targetUrl . '#' . $tab);
            }
        }

        return $response;
    }
}

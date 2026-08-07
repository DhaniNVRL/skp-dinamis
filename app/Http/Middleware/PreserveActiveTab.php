<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreserveActiveTab
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response instanceof RedirectResponse) {
            return $response;
        }

        $tab = $this->resolveTab($request);

        if ($tab === null) {
            return $response;
        }

        $target = $response->getTargetUrl();
        $targetHost = parse_url($target, PHP_URL_HOST);

        if (is_string($targetHost) && $targetHost !== '' && $targetHost !== $request->getHost()) {
            return $response;
        }

        $response->setTargetUrl($this->withTab($target, $tab));

        return $response;
    }

    private function resolveTab(Request $request): ?string
    {
        $tab = $request->input('_active_tab') ?: $request->query('tab');

        if (! is_string($tab) || $tab === '') {
            $refererQuery = parse_url((string) $request->headers->get('referer'), PHP_URL_QUERY);
            if (is_string($refererQuery)) {
                parse_str($refererQuery, $query);
                $tab = $query['tab'] ?? null;
            }
        }

        return is_string($tab) && preg_match('/\A[a-zA-Z0-9_-]{1,50}\z/', $tab)
            ? $tab
            : null;
    }

    private function withTab(string $target, string $tab): string
    {
        $fragment = '';
        if (($position = strpos($target, '#')) !== false) {
            $fragment = substr($target, $position);
            $target = substr($target, 0, $position);
        }

        if (preg_match('/([?&])tab=[^&]*/', $target)) {
            $target = preg_replace(
                '/([?&])tab=[^&]*/',
                '$1tab='.rawurlencode($tab),
                $target,
                1
            );
        } else {
            $target .= (str_contains($target, '?') ? '&' : '?').'tab='.rawurlencode($tab);
        }

        return $target.$fragment;
    }
}

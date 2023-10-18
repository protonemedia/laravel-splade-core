<?php

namespace ProtoneMedia\SpladeCore\Http;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ProtoneMedia\SpladeCore\Facades\SpladeCore;
use ProtoneMedia\SpladeCore\View\Factory as ViewFactory;
use Symfony\Component\HttpFoundation\Response;

class Refreshable
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($isRefresh = SpladeCore::isRefreshingComponent()) {
            ViewFactory::trackSpladeComponents();
        }

        $response = $next($request);

        if (! $isRefresh) {
            return $response;
        }

        $componentId = SpladeCore::getComponentRefreshId();
        $component = ViewFactory::getSpladeComponent($componentId);

        $templates[$componentId] = $component;

        // Match template children (e.g. splade-template-id="103229c85bd5ef0cb361f7cb74823807")
        preg_match_all('/splade-template-id="([a-z0-9]+)"/i', $component, $matches);

        foreach ($matches[1] ?? [] as $childComponentId) {
            $templates[$childComponentId] = ViewFactory::getSpladeComponent($childComponentId);
        }

        return new JsonResponse([
            'templates' => $templates,
        ], headers: ['X-Splade-Request-Hash' => $request->header('X-Splade-Request-Hash')]);
    }
}

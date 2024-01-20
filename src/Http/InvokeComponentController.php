<?php

namespace ProtoneMedia\SpladeCore\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\Router;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use ProtoneMedia\SpladeCore\ComponentSerializer;
use ProtoneMedia\SpladeCore\ComponentUnserializer;
use ProtoneMedia\SpladeCore\MiddlewareException;

class InvokeComponentController
{
    /**
     * Unserializes the component, calls the method, and responds with the
     * serialized component and the response of the method.
     */
    public function __invoke(Request $request, ComponentMiddleware $middleware): JsonResponse
    {
        $verbs = Router::$verbs;

        try {
            $validated = $request->validate([
                'data' => ['nullable', 'array'],
                'instance' => ['required', 'string'],
                'method' => ['required', 'string'],
                'props' => ['nullable', 'array'],
                'signature' => ['required', 'string'],
                'template_hash' => ['required', 'string', 'size:32'],
                'original_url' => ['required', 'string', 'url'],
                'original_verb' => ['required', 'string', Rule::in($verbs)],
            ]);
        } catch (ValidationException $e) {
            abort(403, 'Invalid request');
        }

        $this->callMiddlewareFromOriginalRoute($request, $middleware);

        $instance = ComponentUnserializer::fromData($validated)->unserialize();

        $method = $request->input('method');

        if (! method_exists($instance, $method)) {
            abort(403, 'Method not found');
        }

        // Call the method on the component
        $response = $instance->{$method}(
            ...$request->input('data', [])
        );

        $data = ComponentSerializer::make($instance)->toArray([
            'response' => $response,
            'original_url' => $validated['original_url'],
            'original_verb' => $validated['original_verb'],
            'template_hash' => $validated['template_hash'],
        ], true);

        return response()->json($data);
    }

    /**
     * Resolves the Middleware from the original route and calls it.
     */
    private function callMiddlewareFromOriginalRoute(Request $request, ComponentMiddleware $middleware): void
    {
        $middleware->applyOriginalRouteParameters(
            $originalUrl = $request->input('original_url'),
            $originalVerb = $request->input('original_verb'),
            $request
        );

        $middlewares = $middleware->resolveApplicableMiddleware($originalUrl, $originalVerb);

        if (empty($middlewares)) {
            return;
        }

        /** @var Router */
        $router = app(Router::class);

        $response = (new Pipeline(app()))
            ->send($request)
            ->through($router->resolveMiddleware($middlewares))
            ->thenReturn();

        if ($response !== $request) {
            throw MiddlewareException::fromResponse($response);
        }
    }
}

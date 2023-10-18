<?php

namespace Tests\Feature;

use App\Http\Middleware\Authenticate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ProtoneMedia\SpladeCore\Http\ComponentMiddleware;
use Tests\TestCase;

class ComponentMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resolves_the_route_middleware()
    {
        $service = new ComponentMiddleware;

        $middleware = $service->resolveApplicableMiddleware(
            route('auth.change-blade-prop'), 'GET'
        );

        $this->assertCount(1, $middleware);
        $this->assertEquals(Authenticate::class, $middleware[0]);
    }

    /** @test */
    public function it_doesnt_substitue_bindings_if_the_middleware_is_missing_on_the_original_route()
    {
        $service = new ComponentMiddleware;

        $request = $service->makeRequestFromUrlAndMethod(route('splade-core.invoke-component'), 'POST');

        $user = User::factory()->create();

        $service->applyOriginalRouteParameters(
            url('dynamic'), 'GET', $request
        );

        $this->assertCount(0, $request->route()->parameters());
    }

    /** @test */
    public function it_substitutes_the_original_bindings()
    {
        $service = new ComponentMiddleware;

        $request = $service->makeRequestFromUrlAndMethod(route('splade-core.invoke-component'), 'POST');

        $user = User::factory()->create();

        $service->applyOriginalRouteParameters(
            route('auth.change-blade-prop.user', $user->id), 'GET', $request
        );

        $this->assertCount(1, $request->route()->parameters());
        $this->assertInstanceOf(User::class, $request->route()->parameters()['user']);
    }
}

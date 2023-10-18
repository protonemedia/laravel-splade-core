<?php

namespace Tests\Feature;

use App\View\Components\ChangeBladeProp;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use ProtoneMedia\SpladeCore\ComponentHelper;
use ProtoneMedia\SpladeCore\ComponentSerializer;
use ProtoneMedia\SpladeCore\ComponentUnserializer;
use Tests\TestCase;

class ComponentControllerTest extends TestCase
{
    private function dataForComponent(Component $component, array $with = []): array
    {
        $serializer = new ComponentSerializer($component, app(ComponentHelper::class));

        return $serializer->toArray(array_merge([
            'method' => 'setMessage',
            'template_hash' => md5('hash'),
            'original_url' => url('/change-blade-prop'),
            'original_verb' => 'GET',
        ], $with));
    }

    /** @test */
    public function it_aborts_when_the_request_is_incomplete()
    {
        $this->post(route('splade-core.invoke-component'))
            ->assertForbidden()
            ->assertSee('Invalid request');
    }

    /** @test */
    public function it_aborts_when_the_request_has_no_valid_signature()
    {
        $component = new ChangeBladeProp;

        $data = $this->dataForComponent($component);

        $data['signature'] = 'invalid';

        $this->post(route('splade-core.invoke-component'), $data)
            ->assertForbidden()
            ->assertSee('Malicious request');
    }

    /** @test */
    public function it_aborts_when_it_cant_resolve_the_instance()
    {
        $component = new ChangeBladeProp;

        $data = $this->dataForComponent($component);

        $data['instance'] .= 'invalid';

        $serializer = new ComponentSerializer($component, app(ComponentHelper::class));

        $data['signature'] = $serializer->getDataWithSignature($data)['signature'];

        $this->post(route('splade-core.invoke-component'), $data)
            ->assertForbidden()
            ->assertSee('Component not found');
    }

    /** @test */
    public function it_doesnt_apply_original_middleware_if_there_wasnt_any()
    {
        $component = new ChangeBladeProp;

        $data = $this->dataForComponent($component, [
            'original_url' => url('change-blade-prop'),
        ]);

        $this->post(route('splade-core.invoke-component'), $data)
            ->assertOk();
    }

    /** @test */
    public function it_applies_the_original_middleware_when_it_throws_an_exception()
    {
        $component = new ChangeBladeProp;

        $data = $this->dataForComponent($component, [
            'original_url' => route('auth.change-blade-prop'),
        ]);

        // Just to be sure
        Auth::logout();

        $this->post(route('splade-core.invoke-component'), $data)
            ->assertRedirectToRoute('login');
    }

    /** @test */
    public function it_applies_the_original_middleware_when_it_returns_another_response()
    {
        $component = new ChangeBladeProp;

        $data = $this->dataForComponent($component, [
            'original_url' => route('redirect.change-blade-prop'),
        ]);

        $this->post(route('splade-core.invoke-component'), $data)
            ->assertRedirectToRoute('login');
    }

    /** @test */
    public function it_makes_sure_the_component_method_exists()
    {
        $component = new ChangeBladeProp;

        $data = $this->dataForComponent($component);

        $data['method'] .= 'invalid';

        $serializer = new ComponentSerializer($component, app(ComponentHelper::class));

        $data['signature'] = $serializer->getDataWithSignature($data)['signature'];

        $this->post(route('splade-core.invoke-component'), $data)
            ->assertForbidden()
            ->assertSee('Method not found');
    }

    /** @test */
    public function it_calls_the_method_with_a_parameter()
    {
        $component = new ChangeBladeProp;

        $data = $this->dataForComponent($component, [
            'data' => ['Hello from Splade'],
        ]);

        $response = $this->post(route('splade-core.invoke-component'), $data)
            ->assertOk()
            ->assertJson(['data' => ['message' => 'From the inside: Hello from Splade']]);

        $instance = ComponentUnserializer::fromData($response->json())->unserialize();

        $this->assertInstanceOf(ChangeBladeProp::class, $instance);
        $this->assertEquals('From the inside: Hello from Splade', $instance->message);
    }
}

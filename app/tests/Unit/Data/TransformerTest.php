<?php

namespace Tests\Unit\Data;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ProtoneMedia\SpladeCore\Data\InvalidTransformerException;
use ProtoneMedia\SpladeCore\Data\TransformerRepository;
use ProtoneMedia\SpladeCore\Facades\Transformer;
use Tests\TestCase;
use Tests\Unit\Transformers\UserResource;
use Tests\Unit\Transformers\UserTransformer;

class TransformerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        User::factory(2)->create();
    }

    /** @test */
    public function it_returns_the_instance_when_no_transformer_has_been_found()
    {
        $user = User::firstOrFail();

        $this->assertTrue(Transformer::handle($user)->is($user));
    }

    /** @test */
    public function it_throws_an_exception_when_the_transformer_is_invalid()
    {
        $user = User::firstOrFail();

        Transformer::register(User::class, []);

        try {
            Transformer::handle($user);
        } catch (InvalidTransformerException $e) {
            return $this->assertTrue(true);
        }

        $this->fail('Should have thrown an exception');
    }

    /** @test */
    public function it_throws_an_exception_when_a_transformer_is_missing()
    {
        $user = User::firstOrFail();

        Transformer::enforce();

        try {
            Transformer::handle($user);
        } catch (InvalidTransformerException $e) {
            return $this->assertTrue(true);
        }

        $this->fail('Should have thrown an exception');
    }

    /** @test */
    public function it_doesnt_throw_an_exception_when_the_transformer_is_missing_when_the_instance_cant_be_transformed()
    {
        $user = User::firstOrFail()->toArray();

        Transformer::enforce();

        $this->assertEquals($user, Transformer::handle($user));
        $this->assertEquals([$user, $user], Transformer::handle([$user, $user]));
        $this->assertEquals([$user, $user], Transformer::handle(collect([$user, $user]))->all());
    }

    /** @test */
    public function it_can_transform_using_a_closure()
    {
        $user = User::firstOrFail();

        Transformer::enforce()->register(User::class, function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
            ];
        });

        $this->assertEquals([
            'name' => $user->name,
            'email' => $user->email,
        ], Transformer::handle($user));
    }

    /** @test */
    public function it_can_transform_a_collection_using_a_closure()
    {
        $users = User::take(2)->get();

        $this->assertCount(2, $users);

        Transformer::register(User::class, function ($user) {
            return [
                'name' => $user->name,
                'email' => $user->email,
            ];
        });

        $this->assertEquals([
            [
                'name' => $users->get(0)->name,
                'email' => $users->get(0)->email,
            ],
            [
                'name' => $users->get(1)->name,
                'email' => $users->get(1)->email,
            ],
        ], Transformer::handle($users));
    }

    /** @test */
    public function it_can_transform_using_an_api_resource()
    {
        $user = User::firstOrFail();

        Transformer::register(User::class, UserResource::class);

        $this->assertEquals([
            'name' => $user->name,
            'email' => $user->email,
        ], Transformer::handle($user));
    }

    /** @test */
    public function it_can_transform_a_collection_using_an_api_resource()
    {
        $users = User::take(2)->get();

        $this->assertCount(2, $users);

        Transformer::register(User::class, UserResource::class);

        $this->assertEquals([
            [
                'name' => $users->get(0)->name,
                'email' => $users->get(0)->email,
            ],
            [
                'name' => $users->get(1)->name,
                'email' => $users->get(1)->email,
            ],
        ], Transformer::handle($users));
    }

    /** @test */
    public function it_can_transform_an_array_using_an_api_resource()
    {
        $users = User::take(2)->get();

        $this->assertCount(2, $users);

        Transformer::register(User::class, UserResource::class);

        $this->assertEquals([
            [
                'name' => $users->get(0)->name,
                'email' => $users->get(0)->email,
            ],
            [
                'name' => $users->get(1)->name,
                'email' => $users->get(1)->email,
            ],
        ], Transformer::handle($users->all()));
    }

    /** @test */
    public function it_can_transform_using_a_fractal_transformer()
    {
        $user = User::firstOrFail();

        Transformer::register(User::class, UserTransformer::class);

        $this->assertEquals([
            'name' => $user->name,
            'email' => $user->email,
        ], Transformer::handle($user));
    }

    /** @test */
    public function it_can_transform_a_collection_using_a_fractal_transformer()
    {
        $users = User::take(2)->get();

        $this->assertCount(2, $users);

        Transformer::register(User::class, UserTransformer::class);

        $this->assertEquals([
            [
                'name' => $users->get(0)->name,
                'email' => $users->get(0)->email,
            ],
            [
                'name' => $users->get(1)->name,
                'email' => $users->get(1)->email,
            ],
        ], Transformer::handle($users->all()));
    }

    /** @test */
    public function it_can_transform_using_an_instance_that_has_a_transform_method()
    {
        $user = User::firstOrFail();

        Transformer::register(User::class, new UserTransformer);

        $transformer = $this->app->make(TransformerRepository::class);

        $this->assertEquals([
            'name' => $user->name,
            'email' => $user->email,
        ], Transformer::handle($user));
    }
}

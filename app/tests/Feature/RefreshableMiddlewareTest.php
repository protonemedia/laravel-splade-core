<?php

namespace Tests\Feature;

use Tests\TestCase;

class RefreshableMiddlewareTest extends TestCase
{
    /** @test */
    public function it_does_not_touch_the_request_or_response_if_the_refreshable_header_is_missing()
    {
        $this->get('/refresh')
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
            ->assertSee('const spladeTemplates = {};')
            ->assertSee('generic-splade-component');
    }

    /** @test */
    public function it_gathers_the_template_and_its_children()
    {
        return $this->markTestSkipped('Implementation flawed');

        $content = $this->get('/refresh')->getContent();

        // get all templates (spladeTemplates['175d8791545433da6cc09b2c24114bf3'])
        preg_match_all('/spladeTemplates\[[\'"](?<id>[a-f0-9]{32})[\'"]\]/', $content, $matches);

        $this->assertCount(2, $matches['id'] ?? []);

        $this->get('/refresh', ['X-Splade-Component-Refresh' => $matches['id'][1]], false)
            ->assertJsonStructure([
                'templates' => $matches['id'],
            ]);
    }
}

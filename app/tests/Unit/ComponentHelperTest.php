<?php

namespace Tests\Unit;

use App\View\Components\Form;
use App\View\Components\TwoWayBinding;
use Illuminate\View\AnonymousComponent;
use ProtoneMedia\SpladeCore\ComponentHelper;
use Tests\TestCase;

class ComponentHelperTest extends TestCase
{
    protected ComponentHelper $helper;

    public function setUp(): void
    {
        parent::setUp();

        $this->helper = app(ComponentHelper::class);
    }

    /** @test */
    public function it_finds_the_path_of_a_view()
    {
        $this->assertEquals(
            resource_path('views/welcome.blade.php'),
            $this->helper->getPath(view('welcome'))
        );
    }

    /** @test */
    public function it_finds_the_path_of_an_anonymous_component()
    {
        $this->assertEquals(
            resource_path('views/components/anonymous.blade.php'),
            $this->helper->getPath('components.anonymous')
        );
    }

    /** @test */
    public function it_finds_the_tag_of_a_component()
    {
        $this->assertEquals(
            'SpladeComponentTwoWayBinding',
            $this->helper->getTag(new TwoWayBinding)
        );
    }

    /** @test */
    public function it_finds_the_tag_of_an_anonymous_component()
    {
        $this->assertEquals(
            'SpladeComponentAnonymous',
            $this->helper->getTag(new AnonymousComponent('components.anonymous', []))
        );
    }

    /** @test */
    public function it_finds_the_class_of_a_component()
    {
        $this->assertEquals(
            TwoWayBinding::class,
            $this->helper->getClass(new TwoWayBinding)
        );
    }

    /** @test */
    public function it_doesnt_find_the_class_of_an_anonymous_component()
    {
        $this->assertNull(
            $this->helper->getClass('components.anonymous')
        );
    }

    /** @test */
    public function it_finds_the_class_of_a_full_path()
    {
        $this->assertEquals(
            Form::class,
            $this->helper->getClass(resource_path('views/components/form.blade.php')),
        );
    }
}

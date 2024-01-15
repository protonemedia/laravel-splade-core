<?php

namespace Tests\Unit;

use ProtoneMedia\SpladeCore\ScriptParser;
use Tests\TestCase;

class ScriptParserTest extends TestCase
{
    /** @test */
    public function it_can_merge_the_props_when_no_props_are_defined()
    {
        $parser = new ScriptParser('');

        $this->assertEquals([
            'original' => '',
            'new' => 'const props = defineProps({foo: String});',
        ], $parser->getDefineProps([
            'foo' => 'String',
        ]));
    }

    /** @test */
    public function it_can_extract_the_define_props_when_called_with_an_array()
    {
        $script = <<<'JS'
defineProps(['foo', 'bar']);
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'original' => "defineProps(['foo', 'bar']);",
            'new' => 'const props = defineProps({foo: {}, bar: {}});',
        ], $parser->getDefineProps());
    }

    /** @test */
    public function it_can_extract_the_define_props_when_called_with_an_array_with_a_const_declaration()
    {
        $script = <<<'JS'
const props = defineProps(['foo', 'bar']);
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'original' => "const props = defineProps(['foo', 'bar']);",
            'new' => 'const props = defineProps({foo: {}, bar: {}});',
        ], $parser->getDefineProps());
    }

    /** @test */
    public function it_can_extract_the_define_props_when_called_with_an_array_and_merge_other_props()
    {
        $script = <<<'JS'
defineProps(['foo', 'bar']);
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'original' => "defineProps(['foo', 'bar']);",
            'new' => 'const props = defineProps({foo: {}, bar: {}, baz: String});',
        ], $parser->getDefineProps([
            'baz' => 'String',
        ]));
    }

    /** @test */
    public function it_can_extract_the_define_props_when_called_with_an_object()
    {
        $script = <<<'JS'
defineProps({foo: {type: String}, bar: {type: Array}});
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'original' => 'defineProps({foo: {type: String}, bar: {type: Array}});',
            'new' => 'const props = defineProps({foo: {type: String}, bar: {type: Array}});',
        ], $parser->getDefineProps());
    }

    /** @test */
    public function it_can_extract_the_define_props_when_called_with_an_object_and_only_a_type()
    {
        $script = <<<'JS'
defineProps({foo: String, bar: Array});
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'original' => 'defineProps({foo: String, bar: Array});',
            'new' => 'const props = defineProps({foo: String, bar: Array});',
        ], $parser->getDefineProps());
    }

    /** @test */
    public function it_can_extract_the_define_props_when_called_with_an_object_and_merge_props()
    {
        $script = <<<'JS'
defineProps({foo: {type: String}, bar: {type: Array}});
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'original' => 'defineProps({foo: {type: String}, bar: {type: Array}});',
            'new' => 'const props = defineProps({baz: String, foo: {type: String}, bar: {type: Array}});',
        ], $parser->getDefineProps([
            'baz' => 'String',
        ]));
    }

    /** @test */
    public function it_can_extract_the_define_props_when_called_with_an_object_and_only_a_type_and_merge_props()
    {
        $script = <<<'JS'
defineProps({foo: String, bar: Array});
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'original' => 'defineProps({foo: String, bar: Array});',
            'new' => 'const props = defineProps({baz: String, foo: String, bar: Array});',
        ], $parser->getDefineProps([
            'baz' => 'String',
        ]));
    }

    /** @test */
    public function it_can_extract_the_define_props_when_called_with_an_object_with_a_const_declaration()
    {
        $script = <<<'JS'
const props = defineProps({foo: {type: String}, bar: {type: Array}});
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'original' => 'const props = defineProps({foo: {type: String}, bar: {type: Array}});',
            'new' => 'const props = defineProps({foo: {type: String}, bar: {type: Array}});',
        ], $parser->getDefineProps());
    }

    /** @test */
    public function it_gets_the_vue_specific_functions()
    {
        $script = <<<'JS'
        const name = ref('John');
        const age = computed(() => 30);
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'ref',  'computed',
        ], $parser->getVueFunctions()->all());
    }

    /** @test */
    public function it_gets_all_variable_declarations()
    {
        $script = <<<'JS'
const foo = 'bar';
let baz = 'qux';
var quux = 'corge';
const [name, age] = ['John', 30];
const {country, city} = {country: 'Belgium', city: 'Brussels'};
function greet() {
    return 'Hello world';
}
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals(['age', 'baz', 'city', 'country', 'foo', 'greet', 'name', 'quux'], $parser->getVariables()->toArray());
    }

    /** @test */
    public function it_returns_an_array_with_all_js_imports()
    {
        $script = <<<'JS'
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from "@headlessui/vue";
JS;

        $parser = new ScriptParser($script);

        $this->assertEquals([
            'Dialog' => '@headlessui/vue',
            'DialogPanel' => '@headlessui/vue',
            'TransitionRoot' => '@headlessui/vue',
            'TransitionChild' => '@headlessui/vue',
        ], $parser->getImports());
    }
}

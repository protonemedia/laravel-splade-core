<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h } from 'vue'
const props = defineProps({
    spladeBridge: Object,
    spladeTemplateId: String,
    mixed: { default: 'foo' },
    string: { type: String, default: 'foo' },
    defaultString: { type: String, default: 'foo' },
    nullableString: { type: String, default: null },
    int: { type: Number, default: null },
    bool: { type: Boolean, default: null },
    array: { type: Array, default: null },
    object: { type: Object, default: null },
    nullableInt: { type: Number, default: null },
    nullableBool: { type: Boolean, default: null },
    nullableArray: { type: Array, default: null },
    nullableObject: { type: Object, default: null },
    defaultInt: { type: Number, default: 1 },
    defaultBool: { type: Boolean, default: true },
    defaultArray: { type: Array, default: JSON.parse('[\u0022foo\u0022]') },
    multipleTypes: { type: [Array, String, Boolean], default: JSON.parse('[\u0022foo\u0022]') },
    renamed: { type: String, default: 'renamed-foo' },
    json: { default: JSON.parse('{\u0022foo\u0022:\u0022bar\u0022}') },
    dataFromMethod: { type: Array, default: null },
})
const spladeRender = h({
    name: 'SpladeComponentToVuePropRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { ...props }
    },
})
</script>
<template><spladeRender /></template>

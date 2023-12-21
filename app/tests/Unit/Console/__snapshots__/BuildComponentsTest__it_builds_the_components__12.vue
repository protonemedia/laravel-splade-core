<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h } from 'vue'
const props = defineProps({
    spladeBridge: Object,
    spladeTemplateId: String,
    mixed: {},
    string: { type: String },
    defaultString: { type: String },
    nullableString: { type: String },
    int: { type: Number },
    bool: { type: Boolean },
    array: { type: Array },
    object: { type: Object },
    nullableInt: { type: Number },
    nullableBool: { type: Boolean },
    nullableArray: { type: Array },
    nullableObject: { type: Object },
    defaultInt: { type: Number },
    defaultBool: { type: Boolean },
    defaultArray: { type: Array },
    multipleTypes: { type: [Array, String, Boolean] },
    renamed: { type: String },
    jsObject: {},
    dataFromMethod: { type: Array },
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

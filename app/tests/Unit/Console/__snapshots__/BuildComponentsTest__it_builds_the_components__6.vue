<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })

const form = ref({
    package: 'Splade',
    framework: 'laravel',
    date: '2021-01-01',
})

const spladeRender = h({
    name: 'SpladeComponentFormRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { ...props, form }
    },
})
</script>
<template><spladeRender /></template>

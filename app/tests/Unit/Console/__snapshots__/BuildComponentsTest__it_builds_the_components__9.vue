<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { computed, h, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })

const message = ref('Hello Vue!')
const uppercase = computed(() => message.value.toUpperCase())

const spladeRender = h({
    name: 'SpladeComponentTwoWayBindingRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { message, uppercase }
    },
    props,
})
</script>
<template><spladeRender /></template>

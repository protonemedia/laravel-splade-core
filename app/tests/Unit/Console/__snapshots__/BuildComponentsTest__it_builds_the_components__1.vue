<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { computed, h, ref } from 'vue'
const props = defineProps(['spladeBridge', 'spladeTemplateId'])

const message = ref('Hello Vue!')
const reversed = computed(() => message.value.split('').reverse().join(''))

const spladeRender = h({
    name: 'SpladeComponentAnonymousRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { message, reversed }
    },
})
</script>
<template><spladeRender /></template>

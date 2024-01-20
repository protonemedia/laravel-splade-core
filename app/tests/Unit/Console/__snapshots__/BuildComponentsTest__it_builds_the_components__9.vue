<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { computed, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const message = ref('Hello Vue!')
const uppercase = computed(() => message.value.toUpperCase())
const spladeRender = {
    name: 'SpladeComponentTwoWayBindingRender',
    components: { GenericSpladeComponent },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { message, uppercase }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template><spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId" /></template>

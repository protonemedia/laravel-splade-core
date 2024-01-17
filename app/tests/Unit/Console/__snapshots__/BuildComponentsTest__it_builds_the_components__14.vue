<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { markRaw, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from '@headlessui/vue'

const openend = ref(false)

function show() {
    openend.value = true
}
const spladeRender = {
    name: 'SpladeComponentDynamicComponentImportRender',
    components: { GenericSpladeComponent, Dialog, DialogPanel },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { openend, show, TransitionRoot: markRaw(TransitionRoot), TransitionChild: markRaw(TransitionChild) }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template><spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId" /></template>

<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h, markRaw, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })

import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from '@headlessui/vue'

const openend = ref(false)

function show() {
    openend.value = true
}

const spladeRender = h({
    name: 'SpladeComponentDynamicComponentImportRender',
    components: { GenericSpladeComponent, Dialog, DialogPanel },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return {
            ...props,
            openend,
            show,
            TransitionRoot: markRaw(TransitionRoot),
            TransitionChild: markRaw(TransitionChild),
        }
    },
})
</script>
<template><spladeRender /></template>

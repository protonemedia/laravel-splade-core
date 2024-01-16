<script setup>
import { GenericSpladeComponent } from '@protonemedia/laravel-splade-core'
import { h, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })

import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from '@headlessui/vue'

const openend = ref(false)

function show() {
    openend.value = true
}

const spladeRender = h({
    name: 'SpladeComponentComponentImportRender',
    components: { GenericSpladeComponent, Dialog, DialogPanel, TransitionRoot, TransitionChild },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { openend, show }
    },
    props,
})
</script>
<template><spladeRender /></template>

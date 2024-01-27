<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
import { Dialog, DialogPanel, TransitionRoot, TransitionChild } from '@headlessui/vue'

const openend = ref(false)

function show() {
    openend.value = true
}
const spladeRender = {
    name: 'SpladeComponentComponentImportRender',
    components: { Dialog, DialogPanel, TransitionRoot, TransitionChild },
    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { openend, show }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId"
        ><template #default><slot /></template
    ></spladeRender>
</template>

<script setup>
import { h, ref } from 'vue'
const props = defineProps(['spladeTemplateId'])

const message = ref('Hello World!')

const spladeRender = h({
    name: 'SpladeRegularViewRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { ...props, message }
    },
})
</script>
<template><spladeRender /></template>

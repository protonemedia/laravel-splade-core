<script setup>
import { h, ref } from 'vue'
const props = defineProps(['spladeTemplateId'])

const message = ref('Hello Included view!')

const spladeRender = h({
    name: 'SpladeIncludedViewRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { ...props, message }
    },
})
</script>
<template><spladeRender /></template>

<script setup>
import { ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const form = ref({
    package: 'Splade',
    framework: 'laravel',
    date: '2021-01-01',
})
const spladeRender = {
    name: 'SpladeComponentFormRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { form }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId"
        ><template><slot /></template
    ></spladeRender>
</template>

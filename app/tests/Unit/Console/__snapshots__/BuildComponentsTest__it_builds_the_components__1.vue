<script setup>
import { computed, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const message = ref('Hello Vue!')
const reversed = computed(() => message.value.split('').reverse().join(''))
const spladeRender = {
    name: 'SpladeComponentAnonymousRender',

    template: spladeTemplates[props.spladeTemplateId],
    data: () => {
        return { message, reversed }
    },
    props: { spladeBridge: Object, spladeTemplateId: String },
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId"
        ><template #default><slot /></template
    ></spladeRender>
</template>

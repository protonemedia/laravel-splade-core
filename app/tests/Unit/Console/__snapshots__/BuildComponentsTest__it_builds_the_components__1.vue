<script setup>
import { computed, ref } from 'vue'
const props = defineProps({ spladeBridge: Object, spladeTemplateId: String })
const message = ref('Hello Vue!')
const reversed = computed(() => message.value.split('').reverse().join(''))
const spladeRender = {
    name: 'SpladeComponentAnonymousRender',
    template: spladeTemplates[props.spladeTemplateId],
    props: { spladeBridge: Object, spladeTemplateId: String },
    data: () => ({ message, reversed }),
}
</script>
<template>
    <spladeRender :splade-bridge="spladeBridge" :splade-template-id="spladeTemplateId">
        <template v-for="(_, slot) of $slots" #[slot]="scope"><slot :name="slot" v-bind="scope" /></template>
    </spladeRender>
</template>

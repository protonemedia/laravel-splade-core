<script setup>
import { computed, h, inject, ref, onUnmounted } from "vue";

const props = defineProps({
    bridge: {
        type: Object,
        required: true,
    },
});

const template = ref(`<${props.bridge.tag}></${props.bridge.tag}>`);
const templateId = props.bridge["template_hash"];
const eventBus = inject("$spladeTemplateBus");

const updateTemplate = async function (data) {
    spladeTemplates[templateId] = data.template;
    template.value = `<!--${data.hash}--><${props.bridge.tag}></${props.bridge.tag}>`;
};

eventBus.on(`template:${templateId}`, updateTemplate);

onUnmounted(() => {
    eventBus.off(`template:${templateId}`, updateTemplate);
});

const render = computed(() =>
    h({
        template: template.value,
        name: "GenericSpladeComponentRender",
    }),
);
</script>

<template>
    <render :splade-bridge="bridge" :splade-template-id="templateId"></render>
</template>

<script setup>
import { computed, inject, ref, onUnmounted } from "vue";

const props = defineProps({
    bridge: {
        type: Object,
        required: true,
    },
    slots: {
        type: Array,
        required: false,
        default: () => ["default"],
    },
});

const tag = props.bridge.tag;

function generateTemplate() {
    const slots = props.slots.map((slot) => {
        return `<slot name="${slot}" />`;
    });
    return `<${tag}>${slots}</${tag}>`;
}

const template = ref(generateTemplate());
const templateId = props.bridge["template_hash"];
const eventBus = inject("$spladeTemplateBus");

const updateTemplate = async function (data) {
    spladeTemplates[templateId] = data.template;
    template.value = `<!--${data.hash}-->` + generateTemplate();
};

eventBus.on(`template:${templateId}`, updateTemplate);

onUnmounted(() => {
    eventBus.off(`template:${templateId}`, updateTemplate);
});

const render = computed(() => {
    return {
        template: template.value,
        name: "GenericSpladeComponentRender",
    };
});
</script>

<template>
    <render :splade-bridge="bridge" :splade-template-id="templateId">
        <template v-for="slotName in slots">
            <slot :name="slotName" />
        </template>
    </render>
</template>

<script setup>
import { computed, inject, ref, onUnmounted } from "vue";

const props = defineProps({
    bridge: {
        type: Object,
        required: true,
    },
});

const tag = props.bridge.tag;

function generateTemplate() {
    return `<${tag}><template v-for="(_, slot) of $slots" v-slot:[slot]="scope"><slot :name="slot" v-bind="scope" /></template></${tag}>`;
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
        <template v-for="(_, slot) of $slots" #[slot]="scope"
            ><slot :name="slot" v-bind="scope"
        /></template>
    </render>
</template>
